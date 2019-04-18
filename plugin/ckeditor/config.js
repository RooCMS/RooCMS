/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	config.uiColor = '#f0f0f0';
	config.Defaultlanguage = 'ru';
	config.enterMode = CKEDITOR.ENTER_P;
	config.shiftenterMode = CKEDITOR.ENTER_BR;
	config.toolbarStartupExpanded = true;
	config.height = '270px';
	config.resize_dir = 'vertical';
	config.enableTabKeyTools = true;
	config.autoParagraph = false;
	config.ignoreEmptyParagraph = true;
	config.tabSpaces = 8;

	//config.startupMode = 'source';
	//config.toolbarLocation = 'bottom';

	config.allowedContent = true;
	config.protectedSource.push( /<i class[\s\S]*?\><\/i>/g ); // Font Awesome fix
	//config.fillEmptyBlocks = true
	//config.entities_processNumerical = true;
	//config.forceSimpleAmpersand = true;

	//config.extraPlugins='codemirror';

	//config.filebrowserBrowseUrl = '/browser/browse.php';
	//config.filebrowserUploadUrl = '/uploader/upload.php';
	//config.filebrowserImageWindowWidth = '640';
	//config.filebrowserImageWindowHeight = '480';

	// href="mailto:tester@ckeditor.com?subject=subject&body=body"
	//config.emailProtection = '';
	// href="<a href=\"javascript:void(location.href=\'mailto:\'+String.fromCharCode(116,101,115,116,101,114,64,99,107,101,100,105,116,111,114,46,99,111,109)+\'?subject=subject&body=body\')\">e-mail</a>"
	//config.emailProtection = 'encode';
	// href="javascript:mt('tester','ckeditor.com','subject','body')"
	//config.emailProtection = 'mt(NAME,DOMAIN,SUBJECT,BODY)';

	// Use the classes 'AlignLeft', 'AlignCenter', 'AlignRight', 'AlignJustify'
	//config.justifyClasses = [ 'pull-left', 'text-center', 'pull-right', 'justify' ];

	//config.protectedSource.push( /<\?[\s\S]*?\?>/g );                                           // PHP code
	//config.protectedSource.push( /<%[\s\S]*?%>/g );                                             // ASP code
	//config.protectedSource.push( /(<asp:[^\>]+>[\s|\S]*?<\/asp:[^\>]+>)|(<asp:[^\>]+\/>)/gi );  // ASP.Net code

	config.youtube_width = '640';
	config.youtube_height = '480';
	config.youtube_older = false;
	config.youtube_responsive = false;
	config.youtube_related = false;
	config.youtube_privacy = true;
	config.youtube_autoplay = false;
	config.youtube_controls = true;

	// TEMPLATES
	// STYLES
	// PARSER
	// ANOTHER CONFIG

	config.contentsCss = [
		'/plugin/bootstrap/css/bootstrap.min.css',
		'/plugin/font-awesome/css/font-awesome.min.css',
		'/skin/default/css/style.min.css'
	];
	// config.bodyId = 'content_editable';

	config.stylesSet = [
		{ name: 'block muted',		element: 'p', attributes: { class: 'text-muted' } },
		{ name: 'block primary',	element: 'p', attributes: { class: 'text-primary' } },
		{ name: 'block secondary',	element: 'p', attributes: { class: 'text-secondary' } },
		{ name: 'block success',	element: 'p', attributes: { class: 'text-success' } },
		{ name: 'block info',		element: 'p', attributes: { class: 'text-info' } },
		{ name: 'block warning',	element: 'p', attributes: { class: 'text-warning' } },
		{ name: 'block danger',		element: 'p', attributes: { class: 'text-danger' } },
		{ name: 'block dark',		element: 'p', attributes: { class: 'text-dark' } },
		{ name: 'block light',		element: 'p', attributes: { class: 'text-light' } },
		{ name: 'Context primary',	element: 'p', attributes: { class: 'bg-primary' } },
		{ name: 'Context secondary',	element: 'p', attributes: { class: 'bg-secondary' } },
		{ name: 'Context success',	element: 'p', attributes: { class: 'bg-success' } },
		{ name: 'Context info',		element: 'p', attributes: { class: 'bg-info' } },
		{ name: 'Context warning',	element: 'p', attributes: { class: 'bg-warning' } },
		{ name: 'Context danger',	element: 'p', attributes: { class: 'bg-danger' } },
		{ name: 'Context dark',		element: 'p', attributes: { class: 'bg-dark text-light' } },
		{ name: 'Context light',	element: 'p', attributes: { class: 'bg-light' } },
		{ name: 'Lead',			element: 'p', attributes: { class: 'lead' } },
		{ name: 'Jumbotron',		element: 'div', attributes: { class: 'jumbotron' } },
		{ name: 'Jumbotron Fluid',	element: 'div', attributes: { class: 'jumbotron jumbotron-fluid' } },
		{ name: 'Alert primary',	element: 'div', attributes: { class: 'alert alert-primary' } },
		{ name: 'Alert secondary',	element: 'div', attributes: { class: 'alert alert-secondary' } },
		{ name: 'Alert success',	element: 'div', attributes: { class: 'alert alert-success' } },
		{ name: 'Alert info',		element: 'div', attributes: { class: 'alert alert-info' } },
		{ name: 'Alert warning',	element: 'div', attributes: { class: 'alert alert-warning' } },
		{ name: 'Alert danger',		element: 'div', attributes: { class: 'alert alert-danger' } },
		{ name: 'Alert dark',		element: 'div', attributes: { class: 'alert alert-dark' } },
		{ name: 'Alert light',		element: 'div', attributes: { class: 'alert alert-light' } },
		{ name: 'Big',			element: 'big' },
		{ name: 'Small',		element: 'small' },
		{ name: 'Typewriter',		element: 'tt' },
		{ name: 'Computer code',	element: 'code' },
		{ name: 'Keyboard',		element: 'kbd' },
		{ name: 'Markered',		element: 'mark' },
		{ name: 'Sample text',		element: 'samp' },
		{ name: 'Variable',		element: 'var' },
		{ name: 'Deleted text',		element: 'del' },
		{ name: 'Inserted text',	element: 'ins' },
		{ name: 'Cite',			element: 'cite' },
		{ name: 'Quoted',		element: 'q' },
		{ name: 'Badge primary',	element: 'span', attributes: { class: 'badge badge-primary' } },
		{ name: 'Badge secondary',	element: 'span', attributes: { class: 'badge badge-secondary' } },
		{ name: 'Badge info',		element: 'span', attributes: { class: 'badge badge-info' } },
		{ name: 'Badge success',	element: 'span', attributes: { class: 'badge badge-success' } },
		{ name: 'Badge warning',	element: 'span', attributes: { class: 'badge badge-warning' } },
		{ name: 'Badge danger',		element: 'span', attributes: { class: 'badge badge-danger' } },
		{ name: 'Badge dark',		element: 'span', attributes: { class: 'badge badge-dark' } },
		{ name: 'Badge light',		element: 'span', attributes: { class: 'badge badge-light' } },
		{ name: 'text muted',		element: 'span', attributes: { class: 'text-muted' } },
		{ name: 'text primary',		element: 'span', attributes: { class: 'text-primary' } },
		{ name: 'text secondary',	element: 'span', attributes: { class: 'text-secondary' } },
		{ name: 'text success',		element: 'span', attributes: { class: 'text-success' } },
		{ name: 'text info',		element: 'span', attributes: { class: 'text-info' } },
		{ name: 'text warning',		element: 'span', attributes: { class: 'text-warning' } },
		{ name: 'text danger',		element: 'span', attributes: { class: 'text-danger' } },
		{ name: 'text dark',		element: 'span', attributes: { class: 'text-dark' } },
		{ name: 'text light',		element: 'span', attributes: { class: 'text-light' } },
		{ name: 'abbr Initialism',	element: 'abbr', attributes: { class: 'initialism', title: 'abbr' } }//,
		//{ name: 'PHP',		element: 'pre', attributes: { 'data-lang': 'text/x-php' } }
	];

	//config.colorButton_colors = '00923E,F8C100,28166F';

	config.coreStyles_bold		= { element : 'b', overrides : 'strong' };
	config.coreStyles_italic	= { element : 'i', overrides : 'em' };
	config.format_tags = 'h1;h2;h3;h4;h5;h6;p;pre;address';
	config.disableObjectResizing = true;

	config.toolbar = 'RooCMS';

	config.toolbar_Full =
		[
			{ name: 'document', items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
			{ name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
			{ name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
			{ name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
			'/',
			{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat' ] },
			{ name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
			{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
			{ name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
			'/',
			{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
			{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
			{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
			{ name: 'about', items: [ 'About' ] }
		];

	config.toolbar_RooCMS =
		[
			['Source','-','Preview','-','Templates'],
			['Undo','Redo'],
			['SelectAll', '-', 'Cut','Copy','Paste','PasteText','PasteFromWord'],
			['Find', 'Replace'],
			['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'],
			['CopyFormatting', 'RemoveFormat'],
			'/',
			['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
			['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
			['NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv'],
			['BidiLtr', 'BidiRtl'],
			['Link','Unlink','Anchor'],
			['Image','base64image','Youtube','Flash','Table','HorizontalRule','SpecialChar','ckawesome','Glyphicons','Iframe'],
			'/',
			['Styles','Format','Font','FontSize'],
			['TextColor','BGColor'],
			['Maximize', 'ShowBlocks','-','About']
		];

	config.toolbar_Basic =
		[
			['Bold', 'Italic', 'Underline','Strike', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink','-','About']
		];

	config.toolbar_HTML =
		[
			['Undo','Redo'],
			['Bold', 'Italic', 'Underline','Strike','-','Outdent','Indent','Blockquote','-','Subscript','Superscript', '-', 'NumberedList', 'BulletedList'],
			['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
			['Table'],
			['Link','Unlink','Anchor', '-', 'RemoveFormat'],
			'/',
			['Styles','Format','Font','FontSize'],
			['TextColor','BGColor'],
			['Maximize', 'ShowBlocks']
		];

	config.toolbar_Mail =
		[
			['Source','Preview','-','Maximize', 'ShowBlocks','-','About'],
			'/',
			['Undo','Redo'],
			['Bold', 'Italic', 'Underline','Strike','-','Outdent','Indent','-','Subscript','Superscript', '-', 'NumberedList', 'BulletedList'],
			['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
			['Table'],
			['Link','Unlink','Anchor']
		];
};
