Version: 1.3.4 alpha
========================
**release date:	30.12.2019**

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
- CKEditor              `v4.11.4`
- Codemirror            `v5.42`
- Fancybox	            `v3.5.6`
- PHP QR Code           `v1.1.4`
- aRCaptcha             `v3.2`


Release information:
-------------
	[add] added option $config->global_https (and $site['protocol']) for secure view of site
	[add] added aRCaptcha v3.1 to lib for protect form
	[add] added spam protection with aRCaptcha
	
	[upd] updated module popular_feed & last_feed (added category label)
	[upd] in Mailing messages added additional headers for quick unsubscribe
	[upd] update usability/skin/style
		- added lock icon in navigation for pages to which user does not have access
		- added blinked icon for pm on userbar
	[upd] added user ip in logs
	
	[fix] fixit bugs
	[fix] translate comments in code to english international
	[fix] optimisation code
	[fix] fix trouble path in tpls header
	
	[inf] extended help system in ACP
	[inf] remove default title for uploaded images
	
	[del] deleted SOD folder