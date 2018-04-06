<?php
if (!preg_match("/Firefox/", getenv("HTTP_USER_AGENT"))) {
    // tweak for safari or ...
    echo '<html><head><script type="text/javascript" src="/__bear/bearshell/refresh.js"></script></head><body></body></html>';
}
require_once 'vendor/autoload.php';
require_once 'App.php';
$_SERVER['__bear'] = 1;
$app = BEAR::get('app');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
<title>BEAR Shell</title>
<link rel="stylesheet" href="/__bear/css/bearshell.css" type="text/css"	media="screen">
<script type="text/javascript" src="/__bear/js/jquery.bear.min.js.php"></script>
<script type="text/javascript" src="/__bear/js/app.js"></script>
<script type="text/javascript" src="/__bear/bearshell/page.js"></script>
</head>
<body>
<div id="msg"></div>
<div id="output">BEAR Version <?php
echo BEAR::VERSION . ' ' . date('r');
?>
<br /><br />
<div class="info">type 'help' for help</div>
<div class="example">ex) bear read http://www.excite.co.jp/News/xml/rss_excite_news_odd_index_utf_8.dcg</div>
<br />
</div>
<div id="input">
<form class='cmdline' action="/__bear/bearshell/shell.php" method="post" name="form" id="form">
<table class="inputtable">
	<tr>
		<td class="inputtd"><span class="prompt" id="prompt">
		<?php echo $app['core']['info']['id'] . '-' . $app['core']['info']['version']?>@BEAR
		$</span> <input id='q' name='q' type='text' class='cmdline' value="" autocomplete='off'>
		<input name="_submit" type="submit"><br />
		</td>
	</tr>
</table>
</form>
</div>
</body>
</html>