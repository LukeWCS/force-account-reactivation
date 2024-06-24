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

namespace lukewcs\forcereactivation\acp;

class acp_foraccrea_module
{
	public $page_title;
	public $tpl_name;
	public $u_action;

	public function main()
	{
		global $phpbb_container;

		$language = $phpbb_container->get('language');
		$this->tpl_name = 'acp_foraccrea_settings';
		$this->page_title = $language->lang('FORACCREA_NAV_TITLE') . ' - ' . $language->lang('FORACCREA_NAV_CONFIG');

		$acp_controller = $phpbb_container->get('lukewcs.forcereactivation.controller.acp');
		$acp_controller->set_page_url($this->u_action);
		$acp_controller->module_settings();
	}
}
