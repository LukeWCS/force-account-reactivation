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
// $test_users = ['force_react_user', 'test2'];

		// Check requirements
		if ($event['login']['status'] != LOGIN_SUCCESS
			|| $user_row['user_type'] != USER_NORMAL
			|| !$this->config['foraccrea_enable']
			|| !$this->config['email_enable']
			|| $user_row['user_email'] == ''
		)
		{
			// if (array_search($user_row['username_clean'], $test_users) !== false)
			// {
				// trigger_error('requirements false');
			// }
			// Requirements not met, cancel process.
			return;
		}
// var_dump($user_row['username_clean']);

		// Determine the user's last visit.
		$sql = 'SELECT session_user_id, MAX(session_time) AS session_time
				FROM ' . SESSIONS_TABLE . '
				WHERE session_user_id = ' . (int) $user_row['user_id'];
		$result = $this->db->sql_query($sql);
		$user_last_session = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		$user_lastvisit = $user_last_session['session_time'] ?? $user_row['user_lastvisit'] ?? 1;
// var_dump('user_last_session', $user_last_session);
// var_dump('user_last_time', $user_lastvisit);

		// Determine the ID of the NRU group.
		$sql = 'SELECT group_id, group_type, group_name
				FROM ' . GROUPS_TABLE . '
				WHERE group_name = "NEWLY_REGISTERED"';
		$result = $this->db->sql_query($sql);
		$fetchrow = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		$nru_group_id = $fetchrow['group_id'] ?? null;
// var_dump('nru_group_id', $nru_group_id);

		// Check whether the user is excluded.
		$group_memberships = group_memberships(false, $user_row['user_id']);
		$user_group_ids = array_column($group_memberships, 'group_id');
// var_dump(array_search($nru_group_id, $user_group_ids) !== false);

		if ($nru_group_id !== null && array_search($nru_group_id, $user_group_ids) !== false)
		{
			$exclude_user = $this->config['foraccrea_exclude_nru'];
		}
		else
		{
			$exclude_group_ids = json_decode($this->config['foraccrea_exclude_groups']) ?? [];
			$intersect_group_ids = array_intersect($user_group_ids, $exclude_group_ids);
			$exclude_user = count($intersect_group_ids) > 0;
		}

// var_dump('exclude_group_ids', $exclude_group_ids ?? null);
// var_dump('user_group_ids', $user_group_ids);
// var_dump('intersect_group_ids', $intersect_group_ids ?? null);
// var_dump('exclude_user', $exclude_user == true);

// var_dump($user_row['user_lastvisit']);
// var_dump($user_row['user_lastvisit'] - strtotime('-' .  $this->config['foraccrea_time_range'] . ' ' .  $this->config['foraccrea_time_range_type']));
// var_dump($event['login']['status']);
// var_dump($event['login']['user_row']);
// var_dump($user_row['user_type']);

// date_default_timezone_set('europe/berlin');
// var_dump('session_time  : ' . (isset($session['session_time']) ? $session['session_time'] . ' - ' . date('Y-m-d H:i:s', $session['session_time']) : '-'));
// var_dump('user_lastvisit: ' . $user_row['user_lastvisit'] . ' - ' . date('Y-m-d H:i:s', $user_row['user_lastvisit']));
// var_dump('user_last_time: ' . $user_lastvisit . ' - ' . date('Y-m-d H:i:s', $user_lastvisit));

		// Check conditions for forced reactivation
		if ($exclude_user
			|| $user_lastvisit >= strtotime(' - ' .  $this->config['foraccrea_time_range'] . ' ' .  $this->config['foraccrea_time_range_type'])
			// || $user_row['username_clean'] != 'force_react_user'
		)
		{
			// if (array_search($user_row['username_clean'], $test_users) !== false)
			// {
				// trigger_error('allowed');
			// }
			// We don't have to act, user is allowed to pass.
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
