/*
 * app.js - JS library for BEAR
 */
( function($) {
	$.app = {
		clear : function(values){
			$('#output').html("");
			$("form input[type=text]").val("");
			$("form input[type=text]").focus();
		},
		shell : function(values) {
			var input_line = '<span class="prompt">' + $("#prompt").html() + '</span>&nbsp;' + $("#q").val() + '<br/>';
			$("#output").append(input_line + values);
			//clear 
			$("form input[type=text]").val("");
			$("form input[type=text]").focus();
		},
		onerror : function(XMLHttpRequest, textStatus, errorThrown) {
			var txt = XMLHttpRequest.responseText;
			txt = txt.replace(/(\n|\r)/g, "<br />").replace(/(\s\s)/g, "&nbsp;&nbsp;"); 
			this.shell(txt);
			$().print_b('[notice] server output no json');
		}
	}
})($);