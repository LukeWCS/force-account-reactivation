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
	dimOptionGroup('[name="foraccrea_enable"]',
		!foraccrea.mail_enabled
	);
	dimOptionGroup('[name="foraccrea_time_range"],[name="foraccrea_exclude_groups[]"]',
		!$('[name="foraccrea_enable"]').prop('checked')
		|| !foraccrea.mail_enabled
	);
	dimOptionGroup('[name="foraccrea_consider_non_login"]',
		!$('[name="foraccrea_enable"]').prop('checked')
		|| !foraccrea.mail_enabled
	);
	dimOptionGroup('[name="foraccrea_exclude_nru"]',
		!$('[name="foraccrea_enable"]').prop('checked')
		|| !foraccrea.mail_enabled
		|| !foraccrea.nru_enabled
	);
};

function dimOptionGroup(selector, dimCondition) {
	const c = constants;

	$(selector).parents('dl').css('opacity', dimCondition ? c.OpacityDisabled : c.OpacityEnabled);
}

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
