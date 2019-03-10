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
	config.ProtectedTags = 'i' ;
	config.protectedSource.push( /<i[\s\S]*?\>/g ); //allows beginning <i> tag
	config.protectedSource.push( /<\/i[\s\S]*?\>/g ); //allows ending </i> tag
	//config.fillEmptyBlocks = true
	//config.entities_processNumerical = true;
	//config.forceSimpleAmpersand = true;

	//config.extraPlugins='codemirror';

	config.fontawesomePath = '/plugin/bootstrap/css/font-awesome.min.css';

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

	config.contentsCss = ['/skin/default/css/style.min.css',
		'/plugin/bootstrap/css/bootstrap.min.css',
		'/plugin/bootstrap/css/bootstrap-select.min.css',
		'/plugin/bootstrap/css/font-awesome.min.css'];
	// config.bodyId = 'content_editable';

	config.stylesSet = [
		{ name: 'block muted',		element: 'p', attributes: { class: 'text-muted' } },
		{ name: 'block primary',	element: 'p', attributes: { class: 'text-primary' } },
		{ name: 'block success',	element: 'p', attributes: { class: 'text-success' } },
		{ name: 'block info',		element: 'p', attributes: { class: 'text-info' } },
		{ name: 'block warning',	element: 'p', attributes: { class: 'text-warning' } },
		{ name: 'block danger',		element: 'p', attributes: { class: 'text-danger' } },
		{ name: 'Context primary',	element: 'p', attributes: { class: 'bg-primary' } },
		{ name: 'Context success',	element: 'p', attributes: { class: 'bg-success' } },
		{ name: 'Context info',		element: 'p', attributes: { class: 'bg-info' } },
		{ name: 'Context warning',	element: 'p', attributes: { class: 'bg-warning' } },
		{ name: 'Context danger',	element: 'p', attributes: { class: 'bg-danger' } },
		{ name: 'Lead',			element: 'p', attributes: { class: 'lead' } },
		{ name: 'Alert success',	element: 'div', attributes: { class: 'alert alert-success' } },
		{ name: 'Alert info',		element: 'div', attributes: { class: 'alert alert-info' } },
		{ name: 'Alert warning',	element: 'div', attributes: { class: 'alert alert-warning' } },
		{ name: 'Alert danger',		element: 'div', attributes: { class: 'alert alert-danger' } },
		{ name: 'Well',			element: 'div', attributes: { class: 'well' } },
		{ name: 'Well Large',		element: 'div', attributes: { class: 'well well-lg' } },
		{ name: 'Well Small',		element: 'div', attributes: { class: 'well well-sm' } },
		{ name: 'Jumbotron',		element: 'div', attributes: { class: 'jumbotron' } },
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
		{ name: 'Label default',	element: 'span', attributes: { class: 'label label-default' } },
		{ name: 'Label primary',	element: 'span', attributes: { class: 'label label-primary' } },
		{ name: 'Label info',		element: 'span', attributes: { class: 'label label-info' } },
		{ name: 'Label success',	element: 'span', attributes: { class: 'label label-success' } },
		{ name: 'Label warning',	element: 'span', attributes: { class: 'label label-warning' } },
		{ name: 'Label danger',		element: 'span', attributes: { class: 'label label-danger' } },
		{ name: 'Badge',		element: 'span', attributes: { class: 'badge' } },
		{ name: 'text muted',		element: 'span', attributes: { class: 'text-muted' } },
		{ name: 'text primary',		element: 'span', attributes: { class: 'text-primary' } },
		{ name: 'text success',		element: 'span', attributes: { class: 'text-success' } },
		{ name: 'text info',		element: 'span', attributes: { class: 'text-info' } },
		{ name: 'text warning',		element: 'span', attributes: { class: 'text-warning' } },
		{ name: 'text danger',		element: 'span', attributes: { class: 'text-danger' } },
		{ name: 'abbr Initialism',	element: 'abbr', attributes: { class: 'initialism', title: 'abbr' } }//,
		//{ name: 'PHP',			element: 'pre', attributes: { 'data-lang': 'text/x-php' } }
	];

	//config.colorButton_colors = '00923E,F8C100,28166F';

	config.coreStyles_bold		= { element : 'b', overrides : 'strong' };
	config.coreStyles_italic	= { element : 'i', overrides : 'em' };
	config.format_tags = 'h1;h2;h3;h4;h5;h6;p;pre;address';
	config.disableObjectResizing = true;

	config.toolbar = 'RooCMS';

	config.toolbar_Full =
		[
			['Source','-','Save','NewPage','Preview','-','Templates'],
			['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt'],
			['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
			['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'],
			'/',
			['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
			['NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv'],
			['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
			['BidiLtr', 'BidiRtl'],
			['Link','Unlink','Anchor'],
			['Image','Flash','Table','HorizontalRule','SpecialChar','PageBreak','Iframe'],
			'/',
			['Styles','Format','Font','FontSize'],
			['TextColor','BGColor'],
			['Maximize', 'ShowBlocks','-','About']
		];

	config.toolbar_RooCMS =
		[
			['Source','-','Preview','-','Templates'],
			['Undo','Redo'],
			['SelectAll', '-', 'Cut','Copy','Paste','PasteText','PasteFromWord'],
			['Find','Replace'],
			['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'],
			['CopyFormatting','RemoveFormat'],
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
