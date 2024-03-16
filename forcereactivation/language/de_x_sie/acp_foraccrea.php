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
//
$lang = array_merge($lang, [
	'FORACCREA_CONFIG_TITLE'			=> 'Konto-Reaktivierung erzwingen',
	'FORACCREA_CONFIG_DESC'				=> 'Hier können Sie die Einstellungen für die Erweiterung <strong>%s</strong> ändern.',

	'FORACCREA_SETTINGS_TITLE'			=> 'Einstellungen',
	'FORACCREA_ENABLE'					=> 'Funktion aktivieren',
	'FORACCREA_ENABLE_EXPLAIN'			=> 'Mit diesem Schalter können Sie die Funktionalität dieser Erweiterung deaktivieren, ohne die Erweiterung komplett deaktivieren zu müssen.',
	'FORACCREA_TIME_RANGE'				=> 'Gültiger Zeitraum',
	'FORACCREA_TIME_RANGE_EXPLAIN'		=> 'Hier können Sie festlegen, wie lange die letzte Anmeldung eines Benutzerkontos maximal her sein darf, bevor eine Reaktivierung erzwungen wird.',
	'FORACCREA_TIME_RANGE_YEARS'		=> 'Jahre',
	'FORACCREA_TIME_RANGE_MONTHS'		=> 'Monate',
	'FORACCREA_EXCLUDE_GROUPS'			=> 'Gruppen ausschließen',
	'FORACCREA_EXCLUDE_GROUPS_EXPLAIN'	=> 'Hier können Sie Gruppen auswählen, die von einer erzwungenen Konto Reaktivierung ausgeschlossen werden sollen. Gründer und Bots sind generell ausgeschlossen. Mit der gedrückten Taste „Strg“ („cmd“ bei macOS) können Sie eine Mehrfachauswahl vornehmen oder einzelne Einträge abwählen.',

	'FORACCREA_MSG_SAVED_SETTINGS'		=> 'Force Account Reactivation: Einstellungen erfolgreich gespeichert',
]);