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

(function ($) {	// IIFE start

'use strict';

const constants = Object.freeze({
	OpacityEnabled	: '1.0',
	OpacityDisabled	: '0.35',
});

function setState() {
	const c = constants;

	// dimOptionGroup('foraccrea_time_range'		, !$('[name="foraccrea_enable"]').prop('checked'));
	// dimOptionGroup('foraccrea_exclude_groups[]'	, !$('[name="foraccrea_enable"]').prop('checked'));
	// dimOptionGroup('foraccrea_exclude_nru'		, !$('[name="foraccrea_enable"]').prop('checked'));
	$('dl:nth-of-type(1n+2)').css('opacity',
		$('[name="foraccrea_enable"]').prop('checked')
		? c.OpacityEnabled : c.OpacityDisabled
	);
};

// function dimOptionGroup(elememtName, dimCondition) {
	// const c = constants;

	// $('[name="' + elememtName + '"]').parents('dl').css('opacity', dimCondition ? c.OpacityDisabled : c.OpacityEnabled);
// }

function formReset() {
	setTimeout(function() {
		setState();
	});
};

function disableEnter(e) {
	if (e.key == 'Enter' && e.target.type != 'textarea') {
		return false;
	}
};

$(function() {
	setState();

	$('#foraccrea_settings')		.on('keypress'	, disableEnter);
	$('#foraccrea_settings')		.on('reset'		, formReset);
	$('[name="foraccrea_enable"]')	.on('change'	, setState);
});

})(jQuery);	// IIFE end
