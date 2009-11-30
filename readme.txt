 === WP-BANNERIZE ===
Contributors: Giovambattista Fazioli
Donate link: http://labs.saidmade.com
Tags: Banner, Manage, Image, ADV, Random
Requires at least: 2.8
Tested up to: 2.8.6
Stable tag: 2.3.1

WP_BANNERIZE, banner-image manager.

== Description ==

WP_BANNERIZE is an Amazing Banner Image Manager. In your template insert: `<?php wp_bannerize(); ?>` or set it like Widget

**FEATURES**

* Create your list (group/key) Banners image
* Show your banners list by php code or Widget
* Set random, limit and catories filters
* Wordpress Admin Contextual HELP
* Wordpress MU compatible

**NEW FROM 2.3.2**

* Add alt class in HTML output
* Add additional class for link TAG A
* Fix Widget HTML output

  See CHANGELOG for full history version

**HOW TO**

When you insert a banner you can give a group (key) code. In this way you can "group" a block of banners. For examples if your theme is a 3 columns you can put in left sidebar:

`<?php wp_bannerize('group=left_sidebar'); ?>`

and put in right sidebar:

`<?php wp_bannerize('group=right_sidebar'); ?>`

However wp-bannerize provides a filter by category, for example:

`<?php wp_bannerize('group=right_sidebar&categories=13,14'); ?>`

The code above shows only banners in the categories 13 or 14, for the "right_sidebar" group key.


**params:**

`
* group               If '' show all groups, else show the selected group code (default '')
* container_before    Main tag container open (default <ul>)
* container_after     Main tag container close (default </ul>)
* before              Before tag banner open (default <li %alt%>) see alt_class below
* after               After tag banner close (default </li>) 
* random              Show random banner sequence (default '')
* categories          Category ID separated by commas. (default '')
* alt_class           class alternate for "before" TAG (use before param)
* link_class          Additional class for link TAG A
* limit               Limit rows number (default '' - show all rows) 
`

= Related Links =

* [Saidmade](http://www.saidmade.com/ "Creazione siti Web")
* [Undolog](http://www.undolog.com/ "Author's Web")
* [Labs Saidmade](http://labs.saidmade.com/ "More Wordpress Plugin info")
* [Tutorial Video](http://www.youtube.com/watch?v=sAZOyAwXu-U "Tutorial Video")

== Screenshots ==

1. Options
2. New inline edit
3. Contextual HELP
4. Widget support

* [Tutorial Video](http://www.youtube.com/watch?v=sAZOyAwXu-U "Tutorial Video")

== Installation ==

1. Upload the entire content of plugin archive to your `/wp-content/plugins/` directory, 
   so that everything will remain in a `/wp-content/plugins/wp-bannerize/` folder
2. Open the plugin configuration page, which is located under `Options -> wp-bannerize`
3. Activate the plugin through the 'Plugins' menu in WordPress (deactivate and reactivate if you're upgrading).
4. Insert in you template php `<?php wp_bannerize(); ?>` function
5. Done. Enjoy.

See [Tutorial Video](http://www.youtube.com/watch?v=sAZOyAwXu-U "Tutorial Video")

== Thanks ==

* [Ivan](http://www.bobmarleymagazine.com/) for bugs report
* [rotunda](http://wordpress.org/support/profile/2123029) for beta test
* [labs.saidmade.com](http://labs.saidmade.com/ "Creazione siti Web")
* [Tutorial Video](http://www.youtube.com/watch?v=sAZOyAwXu-U "Tutorial Video") 
* [marsev](http://wordpress.org/support/profile/5368431 "marsev") for prefix table suggest and beta test

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

= Can I customize arguments TAG? =

Yes, you can cistomize alternate class on "before" TAG and class on link A:

`<?php wp_bannerize('container_before=<div>&container_after=</div>&before=<span %alt%>&after=</span>&link_class=myclass'); ?>`

`
<div>
<span><a href=".."><img src="..." /></a></span>
<span class="alt"><a class="myclass" href=".."><img src="..." /></a></span>
...
</div>`

OR

`<?php wp_bannerize('alt_class=pair&link_class=myclass'); ?>`


<ul>
<li><a href=".."><img src="..." /></a></li>
<li class="pair"><a class="myclass" href=".."><img src="..." /></a></li>
...
</ul>`


== Changelog ==

History release:

`
* 2.3.2     Add "alt" class in HTML output, Add additional class for link TAG A, Fix Widget output
* 2.3.0     Add Wordpress Categories Filter - Show Banner Group for Categories ID, improve admin
* 2.2.2     Fix minor bugs + prepare major release
* 2.2.1     Fix to Wordpress MU compatibilities, Fix minor bugs
* 2.2.0     Add Widget support, fix compatibility with Wordpress 2.8.6
* 2.1.0     Add thickbox support for preview thumbnail, resize key field to 128 chars, Minor Fix
* 2.0.8     Minor Fix, improve admin
* 2.0.3     Minor Fix
* 2.0.2     Add random option, fix minor bugs
* 2.0.1     Fix bugs on varchar size in create table
* 2.0.0     Add edit banner, combo menu for group/key and target, contextual HELP, Icon, limit oprion, rev list and rev code!
* 1.4.3     Fix patch on old version database table
* 1.4.2	    Add screenshot
* 1.4.1     Clean code
* 1.4.0     Rev UI, change database, Fix upload path server bug
* 1.3.2	    Fix bug to sort order with Ajax call
* 1.3.1     Update jQuery to last version
* 1.3.0	    New improve class object structure
* 1.2.5	    Remove a conflict with a new class object structure
* 1.2	    Do doc :)
* 1.1       Rev, Fix and stable release
* 1.0       First release
`

For more information on the roadmap for future improvements please e-mail: g.fazioli@saidmade.com