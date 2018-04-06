<?php
require_once 'vendor/autoload.php';
require_once 'BEAR/vendors/debuglib.php';
$q = (isset($_GET['id'])) ? "&id={$_GET['id']}" : '';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>BEAR Dev</title>
<link rel="shortcut icon" href="/__bear/favicon.ico">
<link type="text/css" href="/__bear/jquery-ui/css/smoothness/jquery-ui-1.7.custom.css" rel="stylesheet">
<link rel="stylesheet" href="/__bear/css/default.css" type="text/css" media="screen">
<link rel="stylesheet" href="/__bear/css/bearshell.css" type="text/css" media="screen">
<script type="text/javascript" src="/__bear/js/jquery.bear.min.js.php"></script>
<script type="text/javascript" src="/__bear/jquery-ui/js/jquery-ui-1.7.custom.min.js"></script>
<script type="text/javascript" src="/__bear/jquery-ui/js/jquery.cookie.js"></script>
<script type="text/javascript"><!--
    $(function(){
                // Tabs
	    var tab_opt = { cookie: { expires: 30 } , ajaxOptions: { async : false, dataType : "html"}};
	    $('#tabs').tabs(tab_opt);
	    $('#tab-1-1').tabs(tab_opt);
	    $('#tab-2-1').tabs(tab_opt);
	    $('#tab-3-1').tabs(tab_opt);
            });
        --></script>
<?php
echo DbugL::html_prefix();
?>
</head>
<body>
<h1>BEAR Dev</h1>
<div id="tabs">
<ul>
	<li><a href="#tab-1">Log</a></li>
	<li><a href="#tab-2">Code</a></li>
	<li><a href="#tab-3">Info</a></li>
</ul>

<div id="tab-1">
<div id="tab-1-1">
<ul>
	<li><a href="log/tab.php?var=page<?php echo $q; ?>"><span>Page</span></a></li>
	<li><a href="log/tab.php?var=smarty<?php echo $q; ?>"><span>Smarty</span></a></li>
	<li><a href="log/tab.php?var=reg<?php echo $q; ?>"><span>Registry</span></a></li>
	<li><a href="log/tab.php?var=var<?php echo $q; ?>"><span>$_GLOBALS</span></a></li>
	<li><a href="log/tab.php?var=include<?php echo $q; ?>"><span>Includes</span></a></li>
</ul>
</div>
</div>

<div id="tab-2">
<p>コーディング規則(<a href="http://pear.php.net/manual/ja/standards.php">PEAR規約</a>/<a
	href="http://framework.zend.com/manual/ja/coding-standard.html">Zend規約</a>)にしたがっているかチェックができます。</p>
<div id="tab-2-1">
<ul>
	<li><a href="code/home.php"><span>Current</span></a></li>
	<li><a href="code/tab.php?page=htdocs"><span>Htdocs</span></a></li>
	<li><a href="code/tab.php?page=app"><span>App</span></a></li>
	<li><a href="code/tab.php?page=bear"><span>BEAR</span></a></li>
</ul>
</div>
</div>

<div id="tab-3">
<div id="tab-3-1">
<ul>
	<li><a href="info/phpinfo/"><span>PHP Info</span></a></li>
	<li><a href="info/tab.php"><span>BEAR Info</span></a></li>
	<li><a href="info/others.html"><span>Others</span></a></li>
</ul>
</div>
</div>
</div>
</body>
</html>
