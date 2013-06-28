/*
	HUMANIZED MESSAGES 1.0
	idea - http://www.humanized.com/weblog/2006/09/11/monolog_boxes_and_transparent_messages
	home - http://humanmsg.googlecode.com
*/

var humanMsg = {
	setup: function(appendTo, logName, msgOpacity) {
		humanMsg.msgID = 'humanMsg';
		humanMsg.logID = 'humanMsgLog';

		// appendTo is the element the msg is appended to
		if (appendTo == undefined)
			appendTo = 'body';

		// The text on the Log tab
		if (logName == undefined)
			logName = 'Message Log';

		// Opacity of the message
		humanMsg.msgOpacity = 1;

		if (msgOpacity != undefined) 
			humanMsg.msgOpacity = parseFloat(msgOpacity);

		// Inject the message structure
		$(appendTo).append('<div id="'+humanMsg.msgID+'" class="humanMsg"><div class="round"></div><p></p><div class="round"></div></div> <div id="'+humanMsg.logID+'"><p>'+logName+'</p><ul></ul></div>')
		
		$('#'+humanMsg.logID+' p').click(
			function() { $(this).siblings('ul').slideToggle() }
		)
	},

	displayMsg: function(msg, params)
	{
		if (msg == '')	return;
		
		

		clearTimeout(humanMsg.t2);

		// Inject message
		$('#'+humanMsg.msgID+' p').html(msg)
	
		// Show message
		$('#'+humanMsg.msgID+'').show().animate({ opacity: humanMsg.msgOpacity}, 100, function() {
			$('#'+humanMsg.logID)
				.show().children('ul').prepend('<li>'+msg+'</li>')	// Prepend message to log
				.children('li:first').slideDown(200)				// Slide it down
		
			if ( $('#'+humanMsg.logID+' ul').css('display') == 'none') {
				$('#'+humanMsg.logID+' p').animate({ bottom: 40 }, 200, function() {
					$(this).animate({ bottom: 0 }, 300, function() { $(this).css({ bottom: 0 }) })
				})
			}
			
		})

		// Watch for mouse & keyboard in .5s
		humanMsg.t1 = setTimeout("humanMsg.bindEvents()", 700)
		// Remove message after 5s
		humanMsg.t2 = setTimeout("humanMsg.removeMsg()", 5000)
	},

	bindEvents: function() {
	// Remove message if mouse is moved or key is pressed
		$(window)
			.mousemove(humanMsg.removeMsg)
			.click(humanMsg.removeMsg)
			.keypress(humanMsg.removeMsg)
	},

	removeMsg: function() {
		// Unbind mouse & keyboard
		$(window)
			.unbind('mousemove', humanMsg.removeMsg)
			.unbind('click', humanMsg.removeMsg)
			.unbind('keypress', humanMsg.removeMsg)

		// If message is fully transparent, fade it out
		if ($('#'+humanMsg.msgID).css('opacity') == humanMsg.msgOpacity)
			$('#'+humanMsg.msgID).animate({ opacity: 0 }, 500, function() { $(this).hide() })
	}
};

$(document).ready(function(){
	humanMsg.setup();
})