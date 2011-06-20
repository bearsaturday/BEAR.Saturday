$(document).ready( function() {
	// Ajax Form
	$("#form").p().bearAjaxLink({event : "submit", url : "/__bear/bearshell/shell.php", error: "onerror", form: true});
	document.form.q.focus();
	$(document).click(function(){
		document.form.q.focus();
	});
	$("input[type='text']").click();
	$("#q").focus();
	$.bear.log("shell ready...");
});
