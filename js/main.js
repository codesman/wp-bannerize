/**
 * Javascript functions
 *
 * @package         wp-bannerize
 * @subpackage      main.js
 * @author          =undo= <g.fazioli@saidmade.com>
 * @copyright       Copyright (C) 2010 Saidmade Srl
 * @version         1.2.0
 */
jQuery(document).ready(function(){
	jQuery('table#list_bannerize tbody tr').css('width',jQuery('table#list_bannerize').width() );
	jQuery('table#list_bannerize tbody').sortable({
				axis:"y",
				cursor:"n-resize",
				stop:function() {
					jQuery.ajax({
					type: "POST",
					url: wpBannerizeMainL10n.ajaxURL,
					data: jQuery("table#list_bannerize tbody").sortable("serialize")	})
				}
	});
	
	// edit
	jQuery('span.edit a').click(function() {
		jQuery('div#' + jQuery(this).attr('class') ).show();
	});
});

function delete_banner( id ) {
	if( confirm( wpBannerizeMainL10n.messageConfirm ) ) {
		var f = document.forms['delete_bannerize'];
		f.id.value = id;
		f.submit();
	}
}