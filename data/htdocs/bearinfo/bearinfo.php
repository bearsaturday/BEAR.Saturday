<?php
if (isset($_GET['phpinfo'])){
    phpinfo();
    exit();
}
/**
 * BEAR Info
 *
 * 各種ログ、管理画面を集めたPHP開発環境コントロールパネル機能のサイトです。
 *
 * オプションパッケージ
 *
 * <code>sudo pear install Text_Highlighter</code>
 *
 * トラブルシューティング
 *
 * failed to open stream: Permission denied エラーが出たファイルに644のパーミッションを与えてください
 *
 */

/**
 * Log file path
 *
 * このパスを環境に合わせて変更してください。
 *
 */
$file['apache_access'] = '/opt/local/apache2/logs/access_log';
$file['apache_error'] = '/opt/local/apache2/logs/error_log';
$file['mysql_query'] = '/opt/local/var/db/mysql5/query.log';
$file['mysql_slow'] = '/opt/local/var/db/mysql5/query-slow.log';
$file['mysql_no_index'] = '/opt/local/var/db/mysql5/query-no-index.log';
?>
<style type="text/css">
.hl-main {
	font-family: monospace;
}

.hl-default {
	color: #000000;
}

.hl-code {
	color: #7f7f33;
}

.hl-brackets {
	color: #009966;
}

.hl-comment {
	color: #7F7F7F;
}

.hl-quotes {
	color: #00007F;
}

.hl-string {
	color: #7F0000;
}

.hl-identifier {
	color: #000000;
}

.hl-reserved {
	color: #7F007F;
}

.hl-inlinedoc {
	color: #0000FF;
}

.hl-var {
	color: #0066FF;
}

.hl-url {
	color: #FF0000;
}

.hl-special {
	color: #0000FF;
}

.hl-number {
	color: #007F00;
}

.hl-inlinetags {
	color: #FF0000;
}
</style>
<?php

/**
 * make info table
 *
 * @param array $info
 * @return string
 */
function info($info)
{
    $result = "<br /><b>$info:</b><br />";
    $infoResult = $info();
    foreach($infoResult as $k => $v) {
        $v = ($v === true) ? 'Yes' : $v;
        $v = ($v === false) ? 'No' : $v;
        $result .= "$k => $v<br />";
    }
    return $result;
}

/**
 * print log file
 *
 * @param string $file
 */
function printFile($file, $type = "MYSQL")
{

    $hasClass = include_once 'Text/Highlighter.php';
    if ($_GET['full']) {
        $content = file_get_contents($file);
    } else {
        $content = file_exists($file) ? file($file) : 'no file';
        if (is_array($content)) {
            $more = isset($_GET['more']) ? $_GET['more'] : 0;

            $content = array_slice($content, -100 * ($more + 1), 100);
            $content = (string)implode('', $content);
        }
    }
    if ($hasClass) {
        require_once "Text/Highlighter/Renderer/Html.php";
        if ($type) {
            $hlSQL = @Text_Highlighter::factory($type);
            $renderer = new Text_Highlighter_Renderer_Html(array(
                    "numbers" => HL_NUMBERS_LI, 
                    "tabsize" => 4));
            $hlSQL->setRenderer($renderer);
            $content = $hlSQL->highlight($content);
        } else {
            $content = "<pre>$content</pre>";
        }
        echo '<div style="font-size:small;">';
        echo $content;
        echo '</div>';
        print '<ul><li><a href=?file=' . $_GET['file'] . '&more=' . (int)($more + 1) . '> 次の100件 (' . $more * 100 . '~' . ($more + 1) * 100 . ')</a></li><li><a href=?full=1&file=' . $_GET['file'] . '>全て表示</li></li><li><a href=?full>戻る</li></ul>';
    } else {
        print "<pre>$content</pre>";
        print '<ul><li><a href="?">戻る</li></ul>';
    }
}

/**
 * Log Print Mode
 */
if (isset($_GET['file'])) {
    printFile($file[$_GET['file']]);
    exit();
}

if (function_exists('memcache_get_version')) {
    $memcache = new Memcache();
    $memcache->addServer('localhost', 11211);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">
<html>
<head>
<style type="text/css">
body {
	background-color: #ffffff;
	color: #000000;
}

body,td,th,h1,h2 {
	font-family: sans-serif;
}

<
a href            ="bearinfo.php            " id            ="" title         
      ="bearinfo    
          ">bearinfo   
            </a>pre {
	margin: 0px;
	font-family: monospace;
}

a:link {
	color: #000099;
	text-decoration: none;
	background-color: #ffffff;
}

a:hover {
	text-decoration: underline;
}

table {
	border-collapse: collapse;
}

.center {
	text-align: center;
}

.center table {
	margin-left: auto;
	margin-right: auto;
	text-align: left;
}

.center th {
	text-align: center !important;
}

td,th {
	border: 1px solid #000000;
	font-size: 75%;
	vertical-align: baseline;
}

h1 {
	font-size: 150%;
}

h2 {
	font-size: 125%;
}

.p {
	text-align: left;
}

.e {
	background-color: #ccccff;
	font-weight: bold;
	color: #000000;
}

.h {
	background-color: #9999cc;
	font-weight: bold;
	color: #000000;
}

.v {
	background-color: #cccccc;
	color: #000000;
}

.vr {
	background-color: #cccccc;
	text-align: right;
	color: #000000;
}

img {
	float: right;
	border: 0px;
}

hr {
	width: 600px;
	background-color: #cccccc;
	border: 0px;
	height: 1px;
	color: #000000;
}
</style>
<title>bearinfo()</title>
<meta name="ROBOTS" content="NOINDEX,NOFOLLOW,NOARCHIVE" />
</head>
<body>
<div class="center">
<table border="0" cellpadding="3" width="600">
	<tr class="h">
		<td>
		<h1 class="p">BEAR Info</h1>
		</td>
	</tr>
</table>
<br />
<h2>Servers</h2>
<table border="0" cellpadding="3" width="600">
	<tr>
		<td class="e">Apache</td>
		<td class="v"><a href="/manual/">Manual</a> | <a
			href="/server-status/">Status</a> | <a href="/server-info/"> Info</a>
		| <a href="?file=apache_access">Aceess Log</a> | <a
			href="?file=apache_error">Error Log</a></td>
	</tr>
	<tr>
		<td class="e">PHP</td>
		<td class="v"><a href="http://www.php.net/manual/ja/">Manual</a> | <a
			href="?phpinfo">Info</a> | <a href="pear/">PEAR</a> | <a
			href="apc.php">APC</a> | <a href="memcache.php">Memcache</a></td>
	</tr>
	<tr>
		<td class="e">MySQL</td>
		<td class="v"><a
			href="http://dev.mysql.com/doc/refman/5.1/ja/index.html">Manual</a> |
		<a href="?file=mysql_query">All Query</a> | <a href="?file=mysql_slow">Slow
		Query</a></td>
	</tr>
</table>
<br />
<hr />

<h2>Extentions</h2>
<table border="0" cellpadding="3" width="600">
	<tr>
		<td class="e">APC</td>
		<td class="v"><?php
		echo function_exists('apc_sma_info') ? 'Yes' . info('apc_sma_info') : 'No';
		?></td>
	</tr>
	<tr>
		<td class="e">Memcache</td>
		<td class="v"><?php
		echo function_exists('memcache_get_version') ? 'Yes' . $memcache->getVersion() : 'No';
		?></td>
	</tr>
	<tr>
		<td class="e">GD</td>
		<td class="v"><?php
		echo function_exists('gd_info') ? 'Yes' . info('gd_info') : 'No';
		?></td>
	</tr>
	<tr>
		<td class="e">Imagick</td>
		<td class="v"><?php
		echo class_exists('Imagick') ? 'Yes' : 'No';
		?></td>
	</tr>
	<tr>
		<td class="e">Cairo</td>
		<td class="v"><?php
		echo function_exists('cairo_create') ? 'Yes' : 'No';
		?></td>
	</tr>
	<tr>
		<td class="e">Syck</td>
		<td class="v"><?php
		echo function_exists('syck_load') ? 'Yes' : 'No';
		?></td>
	</tr>
</table>
<br />
<h2>License</h2>
<table border="0" cellpadding="3" width="600">
	<tr class="v">
		<td>THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
		EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
		MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
		IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
		CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
		TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
		SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.</td>
	</tr>
</table>
<br />
</div>
</body>
</html>
