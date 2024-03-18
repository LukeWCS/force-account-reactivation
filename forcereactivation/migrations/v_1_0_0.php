<?php
/**
*
* Force Account Reactivation. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2024, LukeWCS, https://www.wcsaga.org/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace lukewcs\forcereactivation\migrations;

class v_1_0_0 extends \phpbb\db\migration\migration
{
	public static function depends_on()
	{
		return ['\phpbb\db\migration\data\v330\v330'];
	}

	public function update_data()
	{
		return [
			['config.add', ['foraccrea_enable'				, 0]],
			['config.add', ['foraccrea_time_range'			, 2]],
			['config.add', ['foraccrea_time_range_type'		, 'years']],
			['config.add', ['foraccrea_exclude_groups'		, '']],
			['config.add', ['foraccrea_exclude_nru'			, 0]],
			['module.add', [
				'acp',
				'ACP_CAT_DOT_MODS',
				'FORACCREA_NAV_TITLE'
			]],
			['module.add', [
				'acp',
				'FORACCREA_NAV_TITLE', [
					'module_basename'	=> '\lukewcs\forcereactivation\acp\acp_foraccrea_module',
					'module_langname'	=> 'FORACCREA_NAV_CONFIG',
					'module_mode'		=> 'settings',
					'module_auth'		=> 'ext_lukewcs/forcereactivation && acl_a_board',
				],
			]],
		];
	}
}
