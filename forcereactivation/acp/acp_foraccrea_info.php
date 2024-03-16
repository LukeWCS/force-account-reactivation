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

class acp_foraccrea_info
{
	function module()
	{
		return [
			'filename'	=> '\lukewcs\forcereactivation\acp\acp_foraccrea_module',
			'title'		=> 'FORACCREA_NAV_TITLE',
			'modes'		=> [
				'settings'	=> [
					'title'	=> 'FORACCREA_NAV_CONFIG',
					'auth'	=> 'ext_lukewcs/forcereactivation && acl_a_board',
					'cat'	=> ['FORACCREA_NAV_TITLE'],
				],
			],
		];
	}
}
