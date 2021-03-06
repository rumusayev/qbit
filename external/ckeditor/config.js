/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */
 
CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	
	// Make dialogs simpler.
	config.removeButtons = 'Underline,Subscript,Superscript';
	// Se the most common block elements.
	config.format_tags = 'p;h1;h2;h3;pre';
	// ALLOW <i></i>
	config.protectedSource.push(/<i[^>]*><\/i>/g);
	//config.fullPage = true;
    //config.forcePasteAsPlainText = true;
	
	config.removeDialogTabs = 'image:advanced;link:advanced';
	config.resize_dir = 'vertical';
	//config.width = '780px';
	config.enterMode = CKEDITOR.ENTER_BR;
	config.allowedContent = true;
	config.entities = false;
	config.fillEmptyBlocks = false;	
	config.extraPlugins = 'codemirror';
	
	
	config.codemirror = {

		// Set this to the theme you wish to use (codemirror themes)
		theme: 'default',

		// Whether or not you want to show line numbers
		lineNumbers: true,

		// Whether or not you want to use line wrapping
		lineWrapping: true,

		// Whether or not you want to highlight matching braces
		matchBrackets: true,

		// Whether or not you want tags to automatically close themselves
		autoCloseTags: true,

		// Whether or not you want Brackets to automatically close themselves
		autoCloseBrackets: true,

		// Whether or not to enable search tools, CTRL+F (Find), CTRL+SHIFT+F (Replace), CTRL+SHIFT+R (Replace All), CTRL+G (Find Next), CTRL+SHIFT+G (Find Previous)
		enableSearchTools: true,

		// Whether or not you wish to enable code folding (requires 'lineNumbers' to be set to 'true')
		enableCodeFolding: true,

		// Whether or not to enable code formatting
		enableCodeFormatting: true,

		// Whether or not to automatically format code should be done when the editor is loaded
		autoFormatOnStart: false,

		// Whether or not to automatically format code should be done every time the source view is opened
		autoFormatOnModeChange: false,

		// Whether or not to automatically format code which has just been uncommented
		autoFormatOnUncomment: false,

		// Whether or not to highlight the currently active line
		highlightActiveLine: true,

		// Whether or not to show the search Code button on the toolbar
		showSearchButton: true,

		// Whether or not to highlight all matches of current word/selection
		highlightMatches: true,

		// Whether or not to show the format button on the toolbar
		showFormatButton: true,

		// Whether or not to show the comment button on the toolbar
		showCommentButton: true,

		// Whether or not to show the uncomment button on the toolbar
		showUncommentButton: true,
			// Whether or not to show the showAutoCompleteButton button on the toolbar
			showAutoCompleteButton: true

	};
};


