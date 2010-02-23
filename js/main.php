/**
 * Javascript functions

 * @package         wp-bannerize
 * @subpackage      wp-bannerize_class
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
					url: "<?php echo $this->ajax_url?>",
					data: jQuery("table#list_bannerize tbody").sortable("serialize")	})
				}
	});
	
	// edit
	jQuery('span.edit a').click(function() {
		jQuery('div#' + jQuery(this).attr('class') ).show();
	});
});

function delete_banner( id ) {
	if( confirm( '<?php _e("WARINING!!\n\nDo you want delete this banner?") ?>' ) {
		var f = document.forms['delete_bannerize'];
		f.id.value = id;
		f.submit();
	}
}