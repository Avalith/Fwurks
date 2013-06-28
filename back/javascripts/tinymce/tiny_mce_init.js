// ALL
/*
tinyMCE.init({
	mode 			: "textareas",
	theme 			: "advanced",
	editor_selector	: "rte-all",
	
	plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
	
	theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
	theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
	theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
	theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,
	theme_advanced_blockformats: "p,h2,h3,h4", 
	
	extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
	template_external_list_url : "example_template_list.js"
});
*/


tinyMCE.init({
	mode 			: "textareas",
	theme 			: "advanced",
	skin 			: "thebigreason",
	editor_selector	: "rte-default",
	plugins 		: "ibrowser,safari,style,contextmenu,paste,fullscreen,nonbreaking,xhtmlxtras",
	
	theme_advanced_buttons1 : "formatselect,|,image,ibrowser,|,bold,italic,underline,|,bullist,numlist,|,outdent,indent,blockquote,|,link,unlink,anchor,pages,charmap,|,cleanup,pastetext,fullscreen,code",
	theme_advanced_buttons2 : "",
	theme_advanced_buttons3 : "",
	theme_advanced_buttons4 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_blockformats : "p,h2,h3,h4", 
	
	width: '99%',
	height: 300,
	content_css : "/back/styles/tinymce.css",
	relative_urls: false,
	elements: 'absurls'
	// extended_valid_elements : "a[href|target|title],img[class|src|alt|title|width|height|align],span[class]",
});