<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Code Sniff</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="/__bear/code/page.css">

<?php
require_once 'vendor/autoload.php';
require_once 'BEAR/vendors/debuglib.php';
require_once 'App.php';
spl_autoload_unregister(array('BEAR', 'onAutoload'));
require_once 'CodeSniff.php';
require_once "Text/Highlighter.php";
require_once "Text/Highlighter/Renderer/Html.php";
// init
if (isset($_GET['bear'])) {
    $file = _BEAR_BEAR_HOME . DIRECTORY_SEPARATOR . $_GET['bear'];
} else {
    $file = _BEAR_APP_HOME . DIRECTORY_SEPARATOR . $_GET['do'];
}
// Code Sniffer
BEAR_Dev_CodeSniff::process($file);
// Source listを表示
echo "<div class='info'>Source:$file<div>";
$renderer = new Text_Highlighter_Renderer_Html(array("numbers" => HL_NUMBERS_TABLE, "tabsize" => 4));
$hlHtml = @Text_Highlighter::factory("PHP");
$hlHtml->setRenderer($renderer);
$fieStr = file_get_contents($file);
echo $hlHtml->highlight($fieStr);
?>


</body>
</html>
