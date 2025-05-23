<?php
/**
*
* Force Account Reactivation. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2024, LukeWCS, https://www.wcsaga.org/
* @license GNU General Public License, version 2 (GPL-2.0)
*
* Note: This extension is 100% genuine handcraft and consists of selected
*       natural raw materials. There was no AI involved in making it.
*
*/

namespace lukewcs\forcereactivation\controller;

class acp_foraccrea_controller
{
	protected object $language;
	protected object $template;
	protected object $config;
	protected object $request;
	protected object $db;
	protected object $group_helper;
	protected object $ext_manager;

	public    string $u_action;
	private   array  $metadata;

	public function __construct(
		\phpbb\language\language $language,
		\phpbb\template\template $template,
		\phpbb\config\config $config,
		\phpbb\request\request $request,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\group\helper $group_helper,
		\phpbb\extension\manager $ext_manager
	)
	{
		$this->language		= $language;
		$this->template		= $template;
		$this->config		= $config;
		$this->request		= $request;
		$this->db			= $db;
		$this->group_helper	= $group_helper;
		$this->ext_manager	= $ext_manager;

		$this->metadata		= $this->ext_manager->create_extension_metadata_manager('lukewcs/forcereactivation')->get_metadata('all');
	}

	public function module_settings(): void
	{
		$notes = [];

		$this->language->add_lang(['acp_foraccrea', 'acp_foraccrea_lang_author'], 'lukewcs/forcereactivation');
		$this->set_meta_template_vars('FORACCREA', 'LukeWCS');

		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key('lukewcs_forcereactivation'))
			{
				trigger_error($this->language->lang('FORM_INVALID') . adm_back_link($this->u_action), E_USER_WARNING);
			}

			$this->config->set('foraccrea_enable'				, $this->request->variable('foraccrea_enable'						, 0));
			$this->config->set('foraccrea_time_range'			, $this->request->variable('foraccrea_time_range'					, 2));
			$this->config->set('foraccrea_time_range_type'		, $this->request->variable('foraccrea_time_range_type'				, 'years'));
			$this->config->set('foraccrea_consider_non_login'	, $this->request->variable('foraccrea_consider_non_login'			, 0));
			$this->config->set('foraccrea_exclude_groups'		, json_encode($this->request->variable('foraccrea_exclude_groups'	, [0])));
			$this->config->set('foraccrea_exclude_nru'			, $this->request->variable('foraccrea_exclude_nru'					, 0));

			trigger_error($this->language->lang('FORACCREA_MSG_SAVED_SETTINGS') . adm_back_link($this->u_action));
		}

		$sql = 'SELECT group_id, group_type, group_name
				FROM ' . GROUPS_TABLE . '
				WHERE group_name NOT IN ("BOTS", "GUESTS", "NEWLY_REGISTERED")
				ORDER BY group_type DESC, group_name ASC';
		$result = $this->db->sql_query($sql);
		$db_groups = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		$exclude_groups = [];
		foreach ($db_groups as $group)
		{
			$exclude_groups += [
				$this->group_helper->get_name($group['group_name']) => [
					$group['group_id'],
					$group['group_type'] == GROUP_SPECIAL,
				],
			];
		}

		$lang_outdated_msg = $this->lang_ver_check_msg('FORACCREA_LANG_VER', 'FORACCREA_MSG_LANGUAGEPACK_OUTDATED');
		if ($lang_outdated_msg)
		{
			$notes[] = $lang_outdated_msg;
		}

		$this->template->assign_vars([
			'FORACCREA_NOTES'					=> $notes,

			'FORACCREA_MAIL_ENABLED'			=> (bool) $this->config['email_enable'],
			'FORACCREA_NRU_ENABLED'				=> (bool) $this->config['new_member_post_limit'],

			'FORACCREA_ENABLE'					=> (bool) $this->config['foraccrea_enable'],
			'FORACCREA_TIME_RANGE'				=> (int) $this->config['foraccrea_time_range'],
			'FORACCREA_TIME_RANGE_TYPE_OPTS'	=> $this->select_struct($this->config['foraccrea_time_range_type'], [
				'FORACCREA_TIME_RANGE_YEARS'	=> 'years',
				'FORACCREA_TIME_RANGE_MONTHS'	=> 'months',
			]),
			'FORACCREA_CONSIDER_NON_LOGIN'		=> (bool) $this->config['foraccrea_consider_non_login'],
			'FORACCREA_EXCLUDE_GROUPS'			=> $this->select_struct(json_decode($this->config['foraccrea_exclude_groups']) ?? [],
				$exclude_groups
			),
			'FORACCREA_EXCLUDE_NRU'				=> (bool) $this->config['foraccrea_exclude_nru'],
		]);

		add_form_key('lukewcs_forcereactivation');
	}

	public function set_page_url(string $u_action): void
	{
		$this->u_action = $u_action;
	}

	private function select_struct($cfg_value, array $options): array
	{
		$options_tpl = [];

		foreach ($options as $opt_key => $opt_value)
		{
			if (!is_array($opt_value))
			{
				$opt_value = [$opt_value];
			}
			$options_tpl[] = [
				'label'		=> $opt_key,
				'value'		=> $opt_value[0],
				'bold'		=> $opt_value[1] ?? false,
				'selected'	=> is_array($cfg_value) ? in_array($opt_value[0], $cfg_value) : $opt_value[0] == $cfg_value,
			];
		}

		return $options_tpl;
	}

	private function set_meta_template_vars(string $tpl_prefix, string $copyright): void
	{
		$template_vars = [
			'ext_name'		=> $this->metadata['extra']['display-name'],
			'ext_ver'		=> $this->language->lang($tpl_prefix . '_VERSION_STRING', $this->metadata['version']),
			'ext_copyright'	=> $copyright,
			'class'			=> strtolower($tpl_prefix) . '_footer',
		];
		$template_vars += $this->language->is_set($tpl_prefix . '_LANG_VER') ? [
			'lang_desc'		=> $this->language->lang($tpl_prefix . '_LANG_DESC'),
			'lang_ver'		=> $this->language->lang($tpl_prefix . '_VERSION_STRING', $this->language->lang($tpl_prefix . '_LANG_VER')),
			'lang_author'	=> $this->language->lang($tpl_prefix . '_LANG_AUTHOR'),
		] : [];

		$this->template->assign_vars([$tpl_prefix . '_METADATA' => $template_vars]);
	}

	// Check the language pack version for the minimum version and generate notice if outdated
	private function lang_ver_check_msg(string $lang_version_var, string $lang_outdated_var): string
	{
		$lang_outdated_msg = '';
		preg_match('/^([0-9]+\.[0-9]+\.[0-9]+)/', $this->language->lang($lang_version_var), $matches);
		$ext_lang_ver = $matches[1] ?? '0.0.0';
		$ext_lang_min_ver = $this->metadata['extra']['lang-min-ver'];

		if (phpbb_version_compare($ext_lang_ver, $ext_lang_min_ver, '<'))
		{
			if ($this->language->is_set($lang_outdated_var))
			{
				$lang_outdated_msg = $this->language->lang($lang_outdated_var);
			}
			else // Fallback if the current language package does not yet have the required variable.
			{
				$lang_outdated_msg = 'Note: The language pack for the extension <strong>%1$s</strong> is no longer up-to-date. (installed: %2$s / needed: %3$s)';
			}
			$lang_outdated_msg = sprintf($lang_outdated_msg, $this->metadata['extra']['display-name'], $ext_lang_ver, $ext_lang_min_ver);
		}

		return $lang_outdated_msg;
	}
}
