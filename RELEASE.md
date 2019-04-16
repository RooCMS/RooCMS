Version: 1.3.3 beta
========================
**release date:	16.04.2019**

Plugins
-------
- Smarty                `v3.1.33`
- Smarty plugins:       `get_params`
- Smarty modifiers:     `highlight`, `topsecret`
- Smarty postfilter:    `correct4pu`
- jQuery                `v3.3.1`
- jQuery-Migrate        `v3.0.0`
- jQuery TouchSwipe     `v1.6.18`
- Bootstrap             `v4.3.1`
- Bootstrap Select      `v1.13.7`
- Bootstrap Datepicker  `v1.6.4`
- Bootstrap Colorpicker `v3.0.3`
- Bootstrap TagsInput   `v0.8.0`
- bsCustomFileInput     `v1.3.1`
- Font Awesome          `v5.8.1`
- CKEditor              `v4.11.1`
- Codemirror            `v5.42`
- Fancybox	        `v3.5.6`
- PHP QR Code           `v1.1.4`


Release information:
-------------
	[add] added plugin bsCustomFileInput v1.3.1
	[add] added unsubscribe function in one click for registered users
	[add] created Mailing class
	[add] created mailing archive
	[add] added web-viewer mailing messages for user
	[add] added urgent unsubscribe
	[add] added config option "global email"
	[add] added smarty modifier "top secret" (this is fun)
	
	[upd] updated Bootstrap 3.4 -> 4.3.1 
	[upd] updated Bootstrap Select 1.12.4 -> 1.13.7 
	[upd] updated Font Awesome 4.7 -> 5.7.2 
	[upd] changed Bootstrap Colorpicker plugin
	[upd] updated user interface (fixit bugs, update styles, update layot)
	[upd] css migrated from less to scss
	[upd] increased strength of password hashes
	[upd] more control over meta title (use new options in tpl: $site['pageination']['page'] and $site['pageination']['pages'])
	
	[fix] fixit error
	[fix] translate comments in code to english international
	[fix] restructure tpl files
	[fix] optimisation code
	
	[inf] 
	
	[del] Removed IEPNGFIX (Older browsers will not cope with Bootstrap 4, so there is no need to use PNGFIX for IE in the distribution)
	[del] Removed Step checking files permission from install script