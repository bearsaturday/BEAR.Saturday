<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>BEAR Dev</title>
<!-- Ext JS -->
<script type="text/javascript"
	src="/__bear/ext-2.1/adapter/ext/ext-base.js">
</script>
<script type="text/javascript" src="/__bear/ext-2.1/ext.js">
</script>
<link rel="stylesheet" type="text/css"
	href="/__bear/ext-2.1/resources/css/ext-all.css">
<link rel="stylesheet" type="text/css"
	href="/__bear/ext-2.1/tabs-example.css">
<link rel="stylesheet" type="text/css"
	href="/__bear/ext-2.1/examples.css">
<!-- BEARDev JS - Log-->
<script type="text/javascript" src="extjs.php">
</script>
</head>
<body>
<h2>BEAR Dev</h2>
<p><a href="/__bear/Log/">Log<a> | <a href="/__bear/code/">Code</a> | <a
	href="/__bear/info/">Info</a> | <a href="/__bear/shell/">Shell</a>
<p><?php
$_SERVER['bearmode'] = 0;
$_SERVER['__bear'] = 1;
$_SESSION_['_BEAR_DEV_REQUEST_URI'] = $_SERVER['REQUEST_URI'];
require_once 'App.php';
?>


<div id="ajaxvar"></div>

</body>
</html>
