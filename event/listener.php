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
	protected $template;
	protected $config;
	protected $user;
	protected $log;
	protected $db;
	protected $phpbb_root_path;
	protected $php_ext;

	public function __construct(
		\phpbb\language\language $language,
		\phpbb\template\template $template,
		\phpbb\config\config $config,
		\phpbb\user $user,
		\phpbb\log\log $log,
		\phpbb\db\driver\driver_interface $db,
		$phpbb_root_path,
		$php_ext
	)
	{
		$this->language			= $language;
		$this->template			= $template;
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
			'core.acp_users_display_overview'		=> 'user_mgr_template_vars',
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
			// Requirements not met, return control to phpBB.
			return;
		}

		// Determine the user's last visit.
		$sql = 'SELECT MAX(session_time) AS session_time
				FROM ' . SESSIONS_TABLE . '
				WHERE session_user_id = ' . (int) $user_row['user_id'];
		$result = $this->db->sql_query($sql);
		$user_last_session = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		$user_lastvisit = $user_last_session['session_time'] ?? $user_row['user_lastvisit'];

		// Determine the user's groups.
		$group_memberships = group_memberships(false, $user_row['user_id']) ?: [];
		$user_group_ids = array_column($group_memberships, 'group_id');

		// Check whether a user account without login should be taken into account.
		if ($user_lastvisit == 0 && $this->config['foraccrea_consider_non_login'])
		{
			$user_lastvisit = $user_row['user_regdate'];
		}

		// Check whether the user is excluded if NRU is enabled and the user is a member of the NRU group.
		if ($this->config['new_member_post_limit'])
		{
			$sql = 'SELECT group_id
					FROM ' . GROUPS_TABLE . '
					WHERE group_name = "NEWLY_REGISTERED"
						AND group_type = ' . GROUP_SPECIAL;
			$result = $this->db->sql_query($sql);
			$nru_group_id = $this->db->sql_fetchfield('group_id');
			$this->db->sql_freeresult($result);

			if ($nru_group_id !== false && array_search($nru_group_id, $user_group_ids) !== false)
			{
				$exclude_user = (bool) $this->config['foraccrea_exclude_nru'];
			}
		}

		// Check whether the user is excluded if the user is not a member of the NRU group.
		if (!isset($exclude_user))
		{
			$exclude_group_ids = json_decode($this->config['foraccrea_exclude_groups']) ?? [];
			$intersect_group_ids = array_intersect($user_group_ids, $exclude_group_ids);
			$exclude_user = count($intersect_group_ids) > 0;
		}

		// Check conditions for forced reactivation.
		if ($exclude_user
			|| $user_lastvisit == 0
			|| $user_lastvisit >= strtotime("- {$this->config['foraccrea_time_range']} {$this->config['foraccrea_time_range_type']}")
		)
		{
			// We don't have to act, user is allowed to pass.
			return;
		}

		// Set user language and (re)load language files.
		$this->language->set_user_language($user_row['user_lang'], true);
		$this->language->add_lang('common');
		$this->language->add_lang('foraccrea_login', 'lukewcs/forcereactivation');

		// Deactivate the user account and set status to "Forced user account reactivation".
		user_active_flip('deactivate', $user_row['user_id'], INACTIVE_REMIND);

		// Update the user's last visit and add the reactivation key.
		$user_actkey = gen_rand_string(mt_rand(6, 10));

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

		$messenger->template('@lukewcs_forcereactivation/user_reactivate_account', $user_row['user_lang']);
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

		// Show the user a message and explain how they can reactivate their account.
		trigger_error($this->language->lang('FORACCREA_MSG_REACTIVATION_EXPLANATION'));
	}

	public function user_mgr_template_vars($event)
	{
		$user_row = $event['user_row'];

		$this->language->add_lang('acp_foraccrea_user_mgr', 'lukewcs/forcereactivation');

		$this->template->assign_vars([
			'FORACCREA_PASSCHANGE_TIME'	=> $this->user->format_date($user_row['user_passchg']),
			'FORACCREA_INACTIVE_TIME'	=> $user_row['user_inactive_time'] ? $this->user->format_date($user_row['user_inactive_time']) : false,
		]);
	}
}
