tinyMCE.init({
	mode 			: "textareas",
	theme 			: "advanced",
//	skin 			: "o2k7",
//	skin_variant 	: "silver",
	editor_selector	: "rte-default",
	
	plugins 		: "ibrowser,safari,inlinepopups,style,paste,fullscreen,tabfocus",
	
	theme_advanced_buttons1 : "formatselect,|,image,ibrowser,|,bold,italic,underline,|,bullist,numlist,|,outdent,indent,blockquote,|,link,unlink,anchor,charmap,|,cleanup,pastetext,fullscreen,code",
	theme_advanced_buttons2 : "",
	theme_advanced_buttons3 : "",
	theme_advanced_buttons4 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_blockformats : "p,h2,h3,h4", 
	
	width: '100%',
	height: 300,
	content_css : "/back/styles/tinymce.css",
	
	relative_urls: false,
	elements: 'absurls'
	
//	extended_valid_elements : "a[href|target|title],img[class|src|alt|title|width|height|align],span[class]",
});

tinyMCE.init({
	mode 			: "textareas",
	theme 			: "advanced",
//	skin 			: "o2k7",
//	skin_variant 	: "silver",
	editor_selector	: "rte-autoresize",
	
	plugins 		: "ibrowser,safari,inlinepopups,style,paste,fullscreen,tabfocus, autoresize",
	
	theme_advanced_buttons1 : "formatselect,|,image,ibrowser,|,bold,italic,underline,|,bullist,numlist,|,outdent,indent,blockquote,|,link,unlink,charmap,|,cleanup,pastetext,fullscreen,code",
	theme_advanced_buttons2 : "",
	theme_advanced_buttons3 : "",
	theme_advanced_buttons4 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_blockformats : "p,h2,h3,h4", 
	
	width: '100%',
	content_css : "/back/styles/tinymce.css",
	
	relative_urls: false,
	elements: 'absurls'
	
//	extended_valid_elements : "a[href|target|title],img[class|src|alt|title|width|height|align],span[class]",
});


