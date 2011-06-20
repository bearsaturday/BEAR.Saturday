// ==ClosureCompiler==
// @output_file_name jquery.bear.min.js
// @compilation_level SIMPLE_OPTIMIZATIONS
// @code_url http://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js
// @code_url http://plugins.jquery.com/files/jquery.cookie.js.txt
// ==/ClosureCompiler==

/*
 * jquery.bear.js - jQuery Plug in for BEAR
 * Author: Akihito Koriyama (http://code.google.com/p/bear-project/)
 * Version: 0.2.2
 */

( function($) {
	// BEAR Ajax Link
	$.fn.bearAjaxLink = function(config) {
		$(this).each(
				function(key, value){
					var options = $.extend({}, $.bear.ajaxDefaults, config);
					var loading;
					try {
						var match = this.rel.match(/ajax\[(\w+)\]/);
						loading = (match && match[1]) ?  match[1]  : options.loading;
					} catch (e) {
						loading = options.loading;
					}
					if (config !== undefined && config.url !== undefined) {
						options.url = config.url;
					} else {
						try {
							options.url = $(this).attr('href');
						} catch (e2) {
							$.bear.log('Invalid options: URL');
						}
					}
					options.loading = loading;
					$(this).bind(options.event, options, $.bear.ajaxEvent);
				}
		);
		return this;
	};
	
	// BEAR Ajax Form
	$.fn.bearAjaxForm = function(config) {
		$(this).each(
			function(key, value){
				var options = $.extend({}, $.bear.ajaxDefaults, config);
				var loading;
				try {
					var match = $(this).attr('rel').match(/ajax\[(\w+)\]/);
					loading = (match && match[1]) ?  match[1]  : options.loading;
				} catch (e) {
					loading = options.loading;
				}
				var url = (options.url) ? options.url : $(this).attr('action'); 
				options.url = url;
				options.server = 'quickform';
				options.loading = loading;
				options.formData = $(this);
				$(this + "input[type='submit'],input[type='image']").bind(options.event, options, $.bear.ajaxEvent);
				$(this + "a[rel='form']").bind(options.event, options, $.bear.ajaxEvent);
			}
		);
		return this;
	};

	$.bear = {
	  ajax : function(options) {
		options = $.extend( {}, $.bear.ajaxDefaults, options);
		$.bear.ajaxReq(options);
		return false;
	  },
	  ajaxEvent : function(event) {
		var options = event.data;
		$.bear.ajaxReq(options);
		return false;
	  },
	  ajaxReq : function(options) {
		var data;
		if (options.url === undefined) {
			this.log('Invalid URL [' + options.url + ']');
			this.log(options);
		}
		if (options.server == 'quickform') {
			data = $(options.formData).serialize();
			// css reset
			var formid = this.id;
			$("#" + formid + " *").removeClass("form_error");
		} else {
			data = {};
			data._form = (options.form) ? $.bear.formsSelializer($('form')) : '';
			var values =  (options.values) ? options.values : $.bear.values;
			data._values = $.param(values);
			data._click = options.click;
		}
		$.ajax( {
			url :options.url,
			type :options.type,
			dataType :'json',
			timeout :options.requestTimeout,
			data : data,
			global : false,
			cache : options.cache,
			beforeSend : function(XMLHttpRequest) {
				if (options.beforeSend) {
					try {
						$.app[options.beforeSend](XMLHttpRequest);
					} catch(e){
						$.bear.log(e);
					}
				}
				XMLHttpRequest.setRequestHeader('X-Bear-Ajax-Request', options.server);
				if($.cookie(options.sessionKey)){
					XMLHttpRequest.setRequestHeader('X-Bear-Session-Verify', $.cookie(options.sessionKey));
				}
				$("#" + options.loading).show();
			},
			error : function(XMLHttpRequest, textStatus, errorThrown) {
				try {
					if (options.error) {
						try {
							$.app[options.error](XMLHttpRequest, textStatus, errorThrown);
						} catch(e){
							$.$.bear.log(e);
						}
					} else {
						try{
							$.$.bear.log('Ajax Link Error (Invalid JSON cmd)', XMLHttpRequest, textStatus, errorThrown);
						} catch(e2){}
					} 
				} catch(e){
				}
			},
			success : function(json, textStatus, errorThrown) {
				if (options.success) {
					// app success
					try {
						$.app[options.success](json, textStatus, errorThrown);
					} catch(e){
						$.$.bear.log(e);
					}
				}
				$.bear.bearCmdParser(json);
				$("#" + options.loading).hide();
			}
		});
		return false;
	  },
	  bearCmdParser : function(json) {
			$(json).each( function() {
				$.each(this, function(command, values) {
					try {
						$.bearCmd[command](values);
					} catch (e) {
						try {
							$.bear.log('bear command error, command=[' + command + ']');
							$.bear.log(values);
							$.bear.log(e);
						} catch(e2){}
					}
				});
			});
	  },
	  formsSelializer : function(form) {
			var formData = {};
			$(form.get()).each( function(index, values) {
				var data = $("#" + values.id).serialize();
				formData[values.id] = data;
			});
			return $.param(formData);
	  },
	  log : function(values) {
		try {
			console.error(values);
	  } catch (e) {
	  }
}
	};
	
	/*
	 * BEAR Ajax Command Executer
	 * 
	 * 'html' - insert html 'val' - change form value 'js' - callback function
	 */
	$.bearCmd = {
		// ajax requrest client values
		values : [],
		html : function(values) {
			$.each(values, function(index, data) {
				$.each(data.body, function(div_id, div_html) {
					$("#" + div_id).hide();
					$("#" + div_id).html(div_html);
					if (data.options.effect == 'none') {
						$("#" + div_id).show();
					} else if (data.options.effect == 'slide') {
						$("#" + div_id).slideDown();
					} else {
						$("#" + div_id).fadeIn();
					}
				});
			});
		},
		val : function(values) {
			$.each(values, function(index, data) {
				$.each(data, function(name, value) {
					try {
						$('#' + name).fadeTo("fast", 0.1).fadeTo("slow", 1.0).val(value);
					} catch (e) {
						$.bear.log(e);
					}
					$("input[name='" + name + "'],select[name='" + name + "']")
							.each( function() {
								switch (this.nodeName.toLowerCase()) {
								case "input":
									switch (this.type) {
									case "radio":
									case "checkbox":
										if (this.value == value) {
											$(this).click();
										}
										break;
									default:
										$(this).val(value);
										break;
									}
									break;
								case "select":
									$("option", this).each( function() {
										if (this.value == value) {
											this.selected = true;
										}
									});
									break;
								default:
								}
							});
				});
			});
		},
		js : function(values) {
			$.each(values, function(index, data) {
				$.each(data, function(name, value) {
					$.app[name](value);
				});
			});
		},
		quickform : function(values) {
			$.each(values, function(index, data) {
				$.each(data.errors, function(error_name, error_msg) {
					// create error msg baloon.
					$("#form_error_msg_" + error_name).remove();
					$("body").append("<div class='form_error_msg' id='form_error_msg_" + error_name + "'><p>" + error_msg + "</p></div>");
					var error_baloon = $("#form_error_msg_" + error_name);
					// bind baloon event.
					$("input[name=" + error_name + "]").mouseover(function(){
						error_baloon.css({opacity:0.9, left:"-0", display:"block"}).fadeIn(400);
					}).mousemove(function(kmouse){
						var border_top = $(window).scrollTop(); 
						var border_right = $(window).width();
						var left_pos;
						var top_pos;
						var offset = 20;
						if(border_right - (offset *2) >= error_baloon.width() + kmouse.pageX){
								left_pos = kmouse.pageX + offset;
							} else{
								left_pos = border_right - error_baloon.width()-offset;
							}
						if(border_top + (offset *2) >= kmouse.pageY - error_baloon.height()){
								top_pos = border_top + offset;
							} else{
								top_pos = kmouse.pageY - error_baloon.height()-offset;
							}	
						error_baloon.css({left:left_pos, top:top_pos});
				}).mouseout(function(){
						error_baloon.css({left:"-9999px"});				  
					});
					// form err class
					$("input[name=" + error_name + "]").fadeTo("fast", 0.1).fadeTo("slow", 1.0).addClass("form_error");
				});
			});
		}
	};
	
	/*
	 * debug display - jQuery Object
	 */
	$.fn.p = function(){
		var i;
		var bg = this.css("background-color");
		this.css("background-color", "#FFFFCC");
		 this.each(function(init){ 
			  var jqNode = $(this); 
			  jqNode.css({position: 'relative'}); 
			  for (i = 0; i < 3; i++) {
				  jqNode.animate({ left: -6 },10) 
				  .animate({ left: 0 },50) 
				  .animate({ left: 6 },10) 
				  .animate({ left: 0 },50); 
			  }
		 }); 
		 this.css("background-color", bg);
		return this;
	};

	/*
	 * debug display - non jQuery Object
	 */
	$.fn.print_b = function(object, label) {
		// return;
		if (navigator.userAgent.toUpperCase().indexOf("FIREFOX") >= 0) {
			try {
				if (label === undefined) {
					label = 'log';
				}
				try {
					$.bear.log('[' + label + ']', object);
				} catch (e) {
				}
			} catch (e2) {
			}
		}
	};

})($);


// bearAjax default options
$.bear.ajaxDefaults = {
	url : false,
	event :'click',
	type :'POST',
	form :false,
	formData : '',
	server :'ajax',
	click: '',
	values: [],
	loading :'loading',
	errorMsg :'An ajax error occured.',
	global : 'false',
	beforeSend : false,		
	success : false,
	error : false,
	cache : false,
	sessionKey: '_s',
	requestTimeout: 1000
};
