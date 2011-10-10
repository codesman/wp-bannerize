/**
 * Javascript functions
 *
 * @package         WP Bannerize
 * @subpackage      wpBannerizeFrontend.min.js
 * @author          =undo= <g.fazioli@undolog.com>, <g.fazioli@saidmade.com>
 * @copyright       Copyright © 2008-2010 Saidmade Srl
 * @version         3.0
 */

var WPBannerizeJavascript = {
	version : "1.0",

	incrementClickCount : function(id) {
		jQuery.post(wpBannerizeJavascriptLocalization.ajaxURL, {
				action: 'wpBannerizeClickCounter',
                id: id
            }
        );
	}
};