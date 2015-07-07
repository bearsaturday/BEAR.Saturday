/*
 * bear.js - JS library for BEAR
 * Author: Akihito Koriyama (https://github.com/bearsaturday)
 * Version: 0.0.1
 */

/*
 * Init - make Ajax link from <a rel='ajax'> and <form rel="ajax">tag.
 */
( function($) {
	// set the defaults
	var defaults = {};

	/**
	 * Set BEAR Ajax with rel=ajax[loader]
	 */
	$.fn.bearAjaxLink = function() {
		var options = $.extend( {}, $.fn.bearAjax.defaults, options);
		$(this).each(
				function() {
					var match = this.rel.match(/ajax\[(\w+)\]/);
					var loading = (match && match[1]) ? options.loader + "[id=" + match[1] + "]" : options.loader;
					var url = (options.url) ? options.url : $(this).attr('href'); 
					var config = {
						"url" : url,
						"event" : options.event,
						"server" : 'ajax',
						"loading" : loading
					};
					$(this).bearAjax(config);
				}
		);
	}
	
	/**
	 * BEAR Ajax Form
	 */
	$.fn.bearAjaxForm = function(options) {
		var options = $.extend( {}, $.fn.bearAjax.defaults, options);
		$(this).each(function(){
			var match = $(this).attr("rel").match(/ajax\[(\w+)\]/);
			var loading = (match) ? ".loading[id=" + match[1] + "]"
					: ".loading";
			var config = {
				"url" : $(this.parentNode).attr('action'),
				"form" : $(this.parentNode),
				"event" : options.event,
				"server": 'quickform',
				"loading" : loading,
			}; 
			$(this).bearAjax(config);
		});
	}

	/**
	 * BEAR Ajax Request
	 */
	$.fn.bearAjax = function(options) {
		var options = $.extend( {}, $.fn.bearAjax.defaults, options);
		$(this).bind(options.event, function() {
			if (options.server == 'quickform') {
				var data = $(options.form).serialize();
				// css reset
				var formid = this.parentNode.id;
				$("#" + formid + " *").removeClass("form_error");
			} else {
				var data = '';
			}

			$.ajax( {
				url :options.url,
				type :options.type,
				dataType :'json',
				timeout :1000,
				data : data,
				global : false,
				cache : options.cache,
				beforeSend : function(XMLHttpRequest) {
					if (options.beforeSend) {
						try {
							var res = $.app[options.beforeSend](XMLHttpRequest);
						} catch(e){
							console.error(e);
						}
					}
					if (res != false){
						XMLHttpRequest.setRequestHeader('X-Bear-Ajax-Request', options.server);
						var clientData = {};
						clientData["_form"] = $("form").formsSelializer();
						clientData["_value"] = $.bear.value;
						clientData["_click"] = options.click;
						XMLHttpRequest.setRequestHeader('X-Bear-Ajax-Args', $.param(clientData));
						$(options.loader).show();
					}
				},
				error : function(XMLHttpRequest, textStatus, errorThrown) {
					try {
						if (options.error) {
							try {
								$.app[options.error](XMLHttpRequest, textStatus, errorThrown);
							} catch(e){
								console.error(e);
							}
						} else {
							try{
								console.error('Ajax Link Error', XMLHttpRequest, textStatus, errorThrown);
							} catch(e){}
						} 
					} catch(e){}
				},
				success : function(json) {
					if (options.success) {
						try {
							var res = $.app[options.success](XMLHttpRequest);
						} catch(e){
							console.error(e);
						}
					}
					if (res != false){
						$(json).BearAjaxHandler();
						$(options.loader).hide();
					}
				}
			});
			return false;
		});
	}

	/*
	 * Ajax Commands Handler
	 */
	$.fn.BearAjaxHandler = function() {
		this.each( function() {
			$.each(this, function(command, values) {
				try {
					$.bear[command](values);
				} catch (e) {
					try {
						console.debug(e);
					} catch(e){}
				}
			});
		});
		return this;
	}

	/*
	 * Multi form value serializer
	 * 
	 * NOTE: This *MAY* break the $ chain
	 */
	$.fn.formsSelializer = function() {
		var formData = {};
		$(this.get()).each( function(index, values) {
			var data = $("#" + values.id).serialize();
			formData[values.id] = data;
		});
		return $.param(formData);
	};

	/*
	 * BEAR Ajax Command Executer
	 * 
	 * 'html' - insert html 'val' - change form value 'js' - callback function
	 */
	$.bear = {
		// ajax requrest client values
		value : {},
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
						$().print_b(e);
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
	jQuery.fn.p = function(){
		var bg = this.css("background-color");
		this.css("background-color", "#FFFFCC");
	     this.each(function(init){ 
	    	  $().print_b(this, 'p');
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
				if (label == undefined) {
					label = 'log';
				}
				try {
					console.log('[' + label + ']', object);
				} catch (e) {
				}
				;
			} catch (e) {
			}
		}
	}
	
	$.fn.aboffset = function ( elem ) {
	    var left = 0, top = 0, offsetParent = null;
	    if ( elem.getBoundingClientRect ) {
	       var box = elem.getBoundingClientRect();
	       left = box.left + Math.max( document.documentElement.scrollLeft, document.body.scrollLeft ) + ( -document.documentElement.clientLeft || 0 );
	       top  = box.top  + Math.max( document.documentElement.scrollTop,  document.body.scrollTop )  + ( -document.documentElement.clientTop || 0 );
	    } else {
	       left = elem.offsetLeft, top  = elem.offsetTop, offsetParent = elem.offsetParent;
	       while ( offsetParent ) {
	             left += offsetParent.offsetLeft;
	             top  += offsetParent.offsetTop;             
	             offsetParent = offsetParent.offsetParent;
	       }
	    }
	    return { left : left, top  : top }
	}
})($);


/**
 * Ajax Request Defaults
 */
$.fn.bearAjax.defaults = {
	event :'click',
	type :'POST',
	form :'',
	server :'ajax',
	click: '',
	loader :'.loading',
	errorMsg :'An ajax error occured.',
	global : 'false',
	beforeSend : false,
	success : false,
	error : false,
	cache : false
};
