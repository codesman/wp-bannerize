/**
 * @author Giovambattista Fazioli
 * @rev    2009-10-30
 */
jQuery(document).ready(function(){
	jQuery('table#list_bannerize tbody tr').css('width',jQuery('table#list_bannerize').width() );
	jQuery('table#list_bannerize tbody').sortable({
				axis:"y",
				cursor:"n-resize",
				stop:function() {
					jQuery.ajax({
					type: "POST",
					url: "<?=$this->ajax_url?>",
					data: jQuery("table#list_bannerize tbody").sortable("serialize")	})
				}
	});

	// edit
	jQuery('span.edit a').click(function() {
		jQuery('div#' + jQuery(this).attr('class') ).show();
	});
});

function delete_banner( id ) {
	if( confirm('WARINING!!\n\nDo you want delete this banner?') ) {
		var f = document.forms['delete_bannerize'];
		f.id.value = id;
		f.submit();
	}
}