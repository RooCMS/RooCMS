/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:

		config.uiColor = '#f0f0f0';
		config.Defaultlanguage = 'ru';
		config.enterMode = CKEDITOR.ENTER_P;
		config.shiftenterMode = CKEDITOR.ENTER_BR;

		//config.extraPlugins='codemirror';

		config.toolbarStartupExpanded = true;

		// config.stylesSet = 'my_styles:/styles.js';

        //config.filebrowserBrowseUrl = '/browser/browse.php';
        //config.filebrowserUploadUrl = '/uploader/upload.php';
        //config.filebrowserImageWindowWidth = '640';
        //config.filebrowserImageWindowHeight = '480';

		config.contentsCss = ['/skin/default/style.css',
							  '/plugin/bootstrap/css/bootstrap.min.css',
							  '/plugin/bootstrap/css/bootstrap_moreclasses.min.css',
							  '/plugin/bootstrap/css/bootstrap-select.min.css',
							  '/plugin/bootstrap/css/font-awesome.min.css'];
		//config.bodyId = 'content';

		config.coreStyles_bold = { element : 'b', overrides : 'strong' };
		config.coreStyles_italic = { element : 'i', overrides : 'em' };

		config.height = '250px';

		config.resize_dir = 'vertical';

		config.enableTabKeyTools = true;

		//config.startupMode = 'source';
		//config.toolbarLocation = 'bottom';

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
			['RemoveFormat'],
			'/',
			['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
			['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
			['NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv'],
			['BidiLtr', 'BidiRtl'],
			['Link','Unlink','Anchor'],
			['Image','Flash','Table','HorizontalRule','SpecialChar','Iframe'],
			'/',
			['Styles','Format','Font','FontSize'],
			['TextColor','BGColor'],
			['Maximize', 'ShowBlocks','-','About']
		];

		config.toolbar_Basic =
		[
			['Bold', 'Italic', 'Underline','Strike', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink','-','About']
		];
};
