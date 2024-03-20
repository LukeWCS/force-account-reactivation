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

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ « » “ ” … „ “

$lang = array_merge($lang, [
	'FORACCREA_CONFIG_TITLE'			=> 'Force Account Reactivation',
	'FORACCREA_CONFIG_DESC'				=> 'Here you can change the settings for the <strong>%s</strong> extension.',

	'FORACCREA_SETTINGS_TITLE'			=> 'Settings',
	'FORACCREA_ENABLE'					=> 'Enable function',
	'FORACCREA_ENABLE_EXPLAIN'			=> 'This switch allows you to disable the functionality of this extension without having to disable the extension completely.',
	'FORACCREA_TIME_RANGE'				=> 'Valid time period',
	'FORACCREA_TIME_RANGE_EXPLAIN'		=> 'Here you can specify the maximum time ago that a user account was last logged in before reactivation is forced.',
	'FORACCREA_TIME_RANGE_YEARS'		=> 'Years',
	'FORACCREA_TIME_RANGE_MONTHS'		=> 'Months',
	'FORACCREA_EXCLUDE_GROUPS'			=> 'Exclude groups',
	'FORACCREA_EXCLUDE_GROUPS_EXPLAIN'	=> 'Here you can select groups to exclude from a forced account reactivation. Founders and bots are generally excluded. If a user is a member of an excluded group, they will be excluded even if they are not excluded in other groups.',
	'FORACCREA_EXCLUDE_NRU'				=> 'Exclude “Newly registered users”',
	'FORACCREA_EXCLUDE_NRU_EXPLAIN'		=> 'If a user is a member of this group, it does not matter which selection is made for “Exclude groups”, as only this switch is relevant.',

	'FORACCREA_MULTISELECT_EXPLAIN'		=> 'You can make multiple selections or deselect individual entries by holding down the “Ctrl” key (“cmd” on macOS).',

	'FORACCREA_MSG_SAVED_SETTINGS'		=> 'Force Account Reactivation: Settings saved successfully',
]);
