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

namespace lukewcs\forcereactivation\migrations;

class v_1_1_0 extends \phpbb\db\migration\migration
{
	public static function depends_on()
	{
		return ['\lukewcs\forcereactivation\migrations\v_1_0_0'];
	}

	public function update_data()
	{
		return [
			['config.add', ['foraccrea_consider_non_login', 0]],
		];
	}
}
