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
	protected $language;
	protected $template;
	protected $config;
	protected $request;
	protected $db;
	protected $group_helper;
	protected $ext_manager;

	private $metadata;

	public $u_action;

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
		$this->set_meta_template_vars('FORACCREA');

		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key('lukewcs_forcereactivation'))
			{
				trigger_error($this->language->lang('FORM_INVALID') . adm_back_link($this->u_action), E_USER_WARNING);
			}

			$this->config->set('foraccrea_enable'				, $this->request->variable('foraccrea_enable'						, 0));
			$this->config->set('foraccrea_time_range'			, $this->request->variable('foraccrea_time_range'					, 2));
			$this->config->set('foraccrea_time_range_type'		, $this->request->variable('foraccrea_time_range_type'				, 'years'));
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

		$exclude_group_ids = json_decode($this->config['foraccrea_exclude_groups']) ?? [];
		$exclude_groups = [];
		foreach ($db_groups as $group)
		{
			$exclude_groups[] = [
				'label'		=> $this->group_helper->get_name($group['group_name']),
				'value'		=> $group['group_id'],
				'selected'	=> in_array($group['group_id'], $exclude_group_ids),
			];
		}

		$time_range_types = [
			[
				'label'		=> 'FORACCREA_TIME_RANGE_YEARS',
				'value'		=> 'years',
				'selected'	=> $this->config['foraccrea_time_range_type'] == 'years',
			],
			[
				'label'		=> 'FORACCREA_TIME_RANGE_MONTHS',
				'value'		=> 'months',
				'selected'	=> $this->config['foraccrea_time_range_type'] == 'months',
			],
		];

		$lang_outdated_msg = $this->lang_ver_check_msg('FORACCREA_LANG_VER', 'FORACCREA_MSG_LANGUAGEPACK_OUTDATED');
		if ($lang_outdated_msg)
		{
			$notes[] = $lang_outdated_msg;
		}

		$this->template->assign_vars([
			'FORACCREA_NOTES'				=> $notes,

			'FORACCREA_ENABLE'				=> $this->config['foraccrea_enable'],
			'FORACCREA_TIME_RANGE'			=> $this->config['foraccrea_time_range'],
			'FORACCREA_TIME_RANGE_TYPES'	=> $time_range_types,
			'FORACCREA_EXCLUDE_GROUPS'		=> $exclude_groups,
			'FORACCREA_EXCLUDE_NRU'			=> $this->config['foraccrea_exclude_nru'],
		]);

		add_form_key('lukewcs_forcereactivation');
	}

	public function set_page_url(string $u_action): void
	{
		$this->u_action = $u_action;
	}

	private function set_meta_template_vars(string $tpl_prefix): void
	{
		$this->template->assign_vars([
			$tpl_prefix . '_METADATA' => [
				'EXT_NAME'		=> $this->metadata['extra']['display-name'],
				'EXT_VER'		=> $this->language->lang($tpl_prefix . '_VERSION_STRING', $this->metadata['version']),
				'LANG_DESC'		=> $this->language->lang($tpl_prefix . '_LANG_DESC'),
				'LANG_VER'		=> $this->language->lang($tpl_prefix . '_VERSION_STRING', $this->language->lang($tpl_prefix . '_LANG_VER')),
				'LANG_AUTHOR'	=> $this->language->lang($tpl_prefix . '_LANG_AUTHOR'),
				'CLASS'			=> strtolower($tpl_prefix) . '_footer',
			],
		]);
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
