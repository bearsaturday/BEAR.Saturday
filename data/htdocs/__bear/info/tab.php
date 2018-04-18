<?php
/**
 * BEAR Info
 *
 * 各種ログ、管理画面を集めたPHP開発環境コントロールパネル機能
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
require_once 'vendor/autoload.php';
require_once 'App.php';
$_SERVER['__bear'] = 1;
$configPath = _BEAR_APP_HOME . '/App/data/dev.config.php';
if (!file_exists($configPath)) {
    Panda::error('現在利用できません。', 'debug用設定ファイルが設置されてるか確認してください', array('設定ファイル'=>_BEAR_APP_HOME . '/App/data/dev.config.php'));
} else {
    include_once ($configPath);
    if (!isset($isSet) || !$isSet) {
        Panda::error('現在利用できません。', 'debug用設定ファイルを編集して設定を完了させてください', array('設定ファイル'=>_BEAR_APP_HOME . '/App/data/dev.config.php'));
        exit();
    }
}

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
    foreach ($infoResult as $k => $v) {
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

    $more = 0;
    $hasClass = include_once 'Text/Highlighter.php';
    if (isset($_GET['full'])) {
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
            $reporting = error_reporting( E_ALL & ~E_STRICT );
            $hlSQL = Text_Highlighter::factory($type);
            error_reporting( $reporting );
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
        echo '<ul><li>';
        if ($more) {
            echo '<a href="tab.php?file=' . $_GET['file'] . '&more=' . (int)($more + 1) . '"target="' . "_blank" . '"> 次の100件 (' . $more * 100 . '~' . ($more + 1) * 100 . ')</a></li>';
        }
    } else {
        print "<pre>$content</pre>";
    }
}

/**
 * Log Print Mode
 */
if (isset($_GET['file'])) {
    @printFile($file[$_GET['file']]);
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
#bearinfo body,td,th,h1,h2 {
	font-family: sans-serif;
}

pre {
	margin: 0px;
	font-family: monospace;
}

#bearinfo a:link {
	color: #000099;
	text-decoration: none;
	background-color: #ffffff;
}

#bearinfo a:hover {
	text-decoration: underline;
}

#bearinfo table {
	border-collapse: collapse;
}

#bearinfo .center {
	text-align: center;
}

#bearinfo .center table {
	margin-left: auto;
	margin-right: auto;
	text-align: left;
}

#bearinfo .center th {
	text-align: center !important;
}

#bearinfo td,th {
	border: 1px solid #000000;
	font-size: 75%;
	vertical-align: baseline;
}

#bearinfo h1 {
	font-size: 150%;
}

#bearinfo h2 {
	font-size: 125%;
}

#bearinfo .p {
	text-align: left;
}

#bearinfo .e {
	background-color: #ccccff;
	font-weight: bold;
	color: #000000;
}

#bearinfo .h {
	background-color: #9999cc;
	font-weight: bold;
	color: #000000;
}

#bearinfo .v {
	background-color: #cccccc;
	color: #000000;
}

#bearinfo .vr {
	background-color: #cccccc;
	text-align: right;
	color: #000000;
}

#bearinfo img {
	float: right;
	border: 0px;
}

#bearinfo hr {
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
<div id="bearinfo">
<div class="center">
<table border="0" cellpadding="3" width="600">
	<tr class="h">
		<td>
		<h1 class="p">BEAR Version <?php
		echo BEAR::VERSION;
		?></h1>
		</td>
	</tr>
</table>
<br />
<h2>Path</h2>
<table border="0" cellpadding="3" width="600">
	<tr>
		<td class="e">BEAR</td>
		<td class="v"><?php
		echo _BEAR_BEAR_HOME;
		?></td>
	</tr>
	<td class="e">App</td>
	<td class="v"><?php
	echo _BEAR_APP_HOME;
	?></td>
	</tr>
</table>
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
<h2>Link</h2>
<table border="0" cellpadding="3" width="600">
	<tr class="v">
		<td>
		<ul>
			<li><a href="http://code.google.com/p/bear-project/" target="others">Official
			Site</a></li>
		</ul>
		</td>
	</tr>
</table>
</div>
</div>
</body>
</html>
