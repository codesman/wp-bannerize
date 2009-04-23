 === WP-BANNERIZE ===
Contributors: Giovambattista Fazioli
Donate link: http://labs.saidmade.com
Tags: Banner, Manage, Image, ADV
Requires at least: 2.7.1
Tested up to: 2.7.1
Stable tag: 1.4.1

WP_BANNERIZE, banner-image manager.

== Description ==

WP_BANNERIZE is a banner-image manager. In your template insert: `<?php wp_bannerize(); ?>`

**FROM 1.4.0**
Due to fix an upload's trouble the database table is changed. So, I apologies, but you have to update a new database table and discard old database.
In this way you have to re-insert your banners.

When you insert a banner you can give a group code (8 char). In this way you can "group" a block of banner. For examples if your theme is a 3 columns you can put in left sidebar:

`<?php wp_bannerize('group=left_sidebar'); ?>`

and put in right sidebar:

`<?php wp_bannerize('group=right_sidebar'); ?>`

**params:**

`
* group               If '' show all groups, else show the selected group code (default '')
* container_before    Main tag container open (default <ul>)
* container_after     Main tag container close (default </ul>)
* before              Before tag banner open (default <li>) 
* after               After tag banner close (default </li>) 
`

= Related Links =

* [Saidmade](http://www.saidmade.com/ "Creazione siti Web")
* [Undolog](http://www.undolog.com/ "Author's Web")

== Screenshots ==

None

== Installation ==

1. Upload the entire content of plugin archive to your `/wp-content/plugins/` directory, 
   so that everything will remain in a `/wp-content/plugins/wp-bannerize/` folder
2. Open the plugin configuration page, which is located under `Options -> wp-bannerize`
3. Activate the plugin through the 'Plugins' menu in WordPress (deactivate and reactivate if you're upgrading).
4. Insert in you template php `<?php wp_bannerize(); ?>` function
5. Done. Enjoy.

== Thanks ==

* [rotunda](http://wordpress.org/support/profile/2123029) for beta test
* [labs.saidmade.com](http://labs.saidmade.com/ "Creazione siti Web")

== Frequently Asked Questions == 

= Can I customize output? =

Yes, use the `args` for set "container" and "before" and "after" tagging.
For example the default output is:
`
<ul>
<li><a href=".."><img src="..." /></a></li>
<li><a href=".."><img src="..." /></a></li>
...
</ul>`  
You can change `<ul>` (container) and `<li>` (before) 

`<?php wp_bannerize('container_before=<div>&container_after=</div>&before=<span>&after=</span>'); ?>`

`
<div>
<span><a href=".."><img src="..." /></a></span>
<span><a href=".."><img src="..." /></a></span>
...
</div>`  

== Changelog ==

History release:

`
* 1.4.1     Clean code
* 1.4.0     Rev UI, change database, Fix upload path server bug
* 1.3.2		Fix bug to sort order with Ajax call
* 1.3.1     Update jQuery to last version
* 1.3.0		New improve class object structure
* 1.2.5		Remove a conflict with a new class object structure 
* 1.2		Do doc :)
* 1.1		Rev, Fix and stable release
* 1.0		First release
`
