<?php
/**
*
* Force Account Reactivation - An extension for the phpBB Forum Software package.
*
* @copyright (c) 2024, LukeWCS <https://github.com/LukeWCS>
* @license GNU General Public License, version 2 (GPL-2.0)
*
* Note: This extension is 100% genuine handcraft and consists of selected
*       natural raw materials. There was no AI involved in making it.
*
*/

namespace lukewcs\forcereactivation\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
	protected $language;
	protected $config;
	protected $user;
	protected $log;
	protected $db;
	protected $phpbb_root_path;
	protected $php_ext;

	public function __construct(
		\phpbb\language\language $language,
		\phpbb\config\config $config,
		\phpbb\user $user,
		\phpbb\log\log $log,
		\phpbb\db\driver\driver_interface $db,
		$phpbb_root_path,
		$php_ext
	)
	{
		$this->language			= $language;
		$this->config			= $config;
		$this->user				= $user;
		$this->log				= $log;
		$this->db				= $db;
		$this->phpbb_root_path	= $phpbb_root_path;
		$this->php_ext			= $php_ext;
	}

	public static function getSubscribedEvents()
	{
		return [
			'core.auth_login_session_create_before' => 'check_force_reactivation',
		];
	}

	// Code was partially ported from "includes\acp\acp_users.php"
	public function check_force_reactivation($event)
	{
		$user_row = $event['login']['user_row'];

		// Check requirements
		if ($event['login']['status'] != LOGIN_SUCCESS
			|| $user_row['user_type'] != USER_NORMAL
			|| !$this->config['foraccrea_enable']
			|| !$this->config['email_enable']
			|| $user_row['user_email'] == ''
		)
		{
			// if ($user_row['username_clean'] == 'force_react_user')
			// {
				// trigger_error('requirements false');
			// }
			// Requirements not met, cancel process.
			return;
		}

		$exclude_groups = json_decode($this->config['foraccrea_exclude_groups']) ?? [];

		$sql = 'SELECT session_user_id, MAX(session_time) AS session_time
				FROM ' . SESSIONS_TABLE . '
				WHERE session_user_id = ' . (int) $user_row['user_id'];
				// WHERE session_user_id = 100';
		$result = $this->db->sql_query($sql);
		$session = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		$user_last_time = $session['session_time'] ?? $user_row['user_lastvisit'] ?? 1;

// var_dump($user_row['user_lastvisit']);
// var_dump($user_row['user_lastvisit'] - strtotime('-' .  $this->config['foraccrea_time_range'] . ' ' .  $this->config['foraccrea_time_range_type']));
// var_dump($event['login']['status']);
// var_dump($event['login']['user_row']);
// var_dump($user_row['user_type']);

// var_dump($exclude_groups);
// var_dump(group_memberships(false, $user_row['user_id']));
// var_dump('count: '. (count($exclude_groups) ? 'true' : 'false'));
// var_dump('empty: ' . (empty($exclude_groups) ? 'true' : 'false'));
// var_dump('group_memberships: ' . (group_memberships(json_decode($this->config['foraccrea_exclude_groups']), $user_row['user_id'], true) ? 'true' : 'false'));
// var_dump('excluded: ' . (count($exclude_groups) && group_memberships($exclude_groups, $user_row['user_id'], true) ? 'true' : 'false'));

// date_default_timezone_set('europe/berlin');
// var_dump('session_time  : ' . (isset($session['session_time']) ? $session['session_time'] . ' - ' . date('Y-m-d H:i:s', $session['session_time']) : '-'));
// var_dump('user_lastvisit: ' . $user_row['user_lastvisit'] . ' - ' . date('Y-m-d H:i:s', $user_row['user_lastvisit']));
// var_dump('user_last_time: ' . $user_last_time . ' - ' . date('Y-m-d H:i:s', $user_last_time));

		// Check conditions for forced reactivation
		if (count($exclude_groups) && group_memberships($exclude_groups, $user_row['user_id'], true)
			|| $user_last_time >= strtotime(' - ' .  $this->config['foraccrea_time_range'] . ' ' .  $this->config['foraccrea_time_range_type'])
			// || $user_row['username_clean'] != 'force_react_user'
		)
		{
			// if ($user_row['username_clean'] == 'force_react_user')
			// {
				// trigger_error('conditions false');
			// }
			// We don't have to act, so we go back to sleep.
			return;
		}

		// Set user language and (re)load language files.
		$this->language->set_user_language($user_row['user_lang'], true);
		$this->language->add_lang('common');
		$this->language->add_lang('foraccrea_login', 'lukewcs/forcereactivation');

		// Generate the reactivation key.
		$user_actkey = gen_rand_string(mt_rand(6, 10));

		// Deactivate the user account and set status to "Forced user account reactivation".
		user_active_flip('deactivate', $user_row['user_id'], INACTIVE_REMIND);

		// Update the user's last visit and add the reactivation key.
		$sql = 'UPDATE ' . USERS_TABLE . '
				SET user_lastvisit = ' . time() . ',
					user_actkey = "' . $this->db->sql_escape($user_actkey) . '"
				WHERE user_id = ' . (int) $user_row['user_id'];
		$this->db->sql_query($sql);

		// Prepare email and send link with reactivation key.
		if (!class_exists('messenger'))
		{
			include($this->phpbb_root_path . 'includes/functions_messenger.' . $this->php_ext);
		}
		$messenger = new \messenger(false);
		$server_url = generate_board_url();

		$messenger->template('user_reactivate_account', $user_row['user_lang']);
		$messenger->set_addresses($user_row);
		$messenger->anti_abuse_headers($this->config, $this->user);
		$messenger->assign_vars([
			'WELCOME_MSG'	=> html_entity_decode($this->language->lang('WELCOME_SUBJECT', $this->config['sitename']), ENT_COMPAT),
			'USERNAME'		=> html_entity_decode($user_row['username'], ENT_COMPAT),
			'U_ACTIVATE'	=> "{$server_url}/ucp.{$this->php_ext}?mode=activate&u={$user_row['user_id']}&k={$user_actkey}"
		]);

		$messenger->send(NOTIFY_EMAIL);

		// Add an entry to the user log.
		$this->log->add('user', $this->user->data['user_id'], $this->user->ip, 'LOG_USER_REACTIVATE_USER', false, [
			'reportee_id' => $user_row['user_id']
		]);

// 1707596986
// http://phpbb33/ucp.php?mode=activate&u=72&k=
// echo "<a href='{$server_url}/ucp.{$this->php_ext}?mode=activate&u={$user_row['user_id']}&k={$user_actkey}'>activate</a>";

		// Show the user a message and explain how they can reactivate their account.
		trigger_error($this->language->lang('FORACCREA_MSG_REACTIVATION_EXPLANATION'));
	}
}
