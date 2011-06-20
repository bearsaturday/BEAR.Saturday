<?php
ini_set('display_errors', 1);
require_once 'Panda/vendors/debuglib.php';
define('FULL_LIST_NUM', 200);
//require_once 'App.php';
//BEAR::disableAutoLoader();
//if (!isset($_GET['id'])) {
//    die('invalid id');
//}
require_once 'BEAR.php';
$traceLog = unserialize(file_get_contents(sys_get_temp_dir() . 'trace-' . $_GET['id'] . '.log'));
$traceLevels = array_keys($traceLog);
$levelNum = count($traceLevels);
$raw = '<pre>' . print_r($traceLog, true) . '</pre>';
$i = 0;
foreach ($traceLevels as $level) {
    $trace = $traceLog[$level];
    $line = $trace['line'];
    $file = $trace['file'];
    //    assert(is_string($line));
    $files = file($trace['file']);
    $fileArray = array_map('htmlspecialchars', $files);
    $fileArray[$line - 1] = "<span class=\"hit-line\">{$fileArray[$line - 1]}</span>";
    $shortListArray = array_slice($fileArray, $line - 6, 11);
    $shortListArray[5] = "<a href=\"#traceline{$i}\" id=\"traceline-back{$i}\">{$shortListArray[5]}</a>";
    $shortList = implode('', $shortListArray);
    $shortList = '<pre class="short-list">' . $shortList . '</pre>';
    $hitLine = $fileArray[$line - 1];
    $fileArray[$line - 1] = "<a href=\"#traceline-back{$i}\" id=\"traceline{$i}\">{$fileArray[$line - 1]}</a>";
    $listClass = "prettyprint lang-php";
    if (count($fileArray) > FULL_LIST_NUM) {
        if ($line > FULL_LIST_NUM) {
            $start = $line - FULL_LIST_NUM;
            $listClass = "cutprint";
        } else {
            $start = 0;
        }
        $fullList = implode('', array_slice($fileArray, $start, $start + FULL_LIST_NUM)) . '<i>more ' . (count($fileArray) - FULL_LIST_NUM - $start) . ' lines...</i>';
    } else {
        $fullList = implode('', $fileArray);
    }
    unset($fileArray);
    $fullList = "<pre class=\"{$listClass}\">" . $fullList . '</pre>';
    $args = array();
    foreach ($trace['args'] as $arg) {
        if (is_array($arg)) {
            $args[] = 'Array';
        } elseif (is_string($arg)) {
            $args[] = "'{$arg}'";
        } elseif (is_scalar($arg)) {
            $args[] = $arg;
        } else {
            $args[] = 'Object';
        }
    }
    $args = implode(',', $args);
    if (isset($trace['class'])) {
        $hitInfo = "{$trace['class']}{$trace['type']}{$trace['function']}({$args}) ";
    } elseif (isset($trace['function'])) {
        $hitInfo = "{$trace['function']}({$args}) ";
    } else {
        $hitInfo = '';
    }
    $traceSuammary .= '<li><span class="timeline-num">' . $i . '</span>';
    $traceSuammary .= '<span class="timeline-body">' . $hitLine . '</span>';
    $traceSuammary .= '<span class="timeline-info">' . $hitInfo . '<br />';
    $traceSuammary .= $trace['file'] . ' on line ' . $line . '</span></li>';
    // trace detail
    $tracePage[$i] .= "<h3 class='hit-head'>" . '<span class="timeline-num">' . $i . "</span>{$hitLine}</h3>";
    $tracePage[$i] .= '<span id="hit-info">' . $hitInfo . "<br/>{$file} on line {$line}" . '</span>';
    $tracePage[$i] .= $shortList;
    $tracePage[$i] .= '<h3>Args</h3>';
    $tracePage[$i] .= print_a($trace['args'], "return:1");
    $tracePage[$i] .= '<h3>Object</h3>';
    $tracePage[$i] .= print_a((array)$trace['object'], "return:1");
    //    // full list
    $tracePage[$i] .= "<h3>Source List</h3>";
    $tracePage[$i] .= '<span id="hit-info">' . $file . '</span>';
    $tracePage[$i] .= '<span id="edit-in-tm"><a href="txmt://open/?url=file://' . $file . '&line=' . $line . '&column=0">Edit in TextMate</a> | ';
    //    $tracePage[$i] .= '<a href="/__bear/ecoder/?file=' . str_replace(_BEAR_APP_HOME, '', $file) . '&line=' . $line . '">Ecoder</a></span>';
    if ($i < 0) {
        $tracePage[$i] .= $fullList;
    }
    $i++;
}

$summaryPage = "<h3>Sumamry</h3>";
$summaryPage .= $traceSuammary;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>BEAR Back Trace</title>
<link rel="shortcut icon" href="/__bear/favicon.ico">
<link type="text/css"
	href="/__bear/jquery-ui/css/smoothness/jquery-ui-1.7.custom.css"
	rel="stylesheet">
<link rel="stylesheet" href="/__bear/css/default.css" type="text/css"
	media="screen">
<link rel="stylesheet" href="/__bear/css/trace.css" type="text/css"
	media="screen">
<script type="text/javascript"
	src="/__bear/jquery-ui/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript"
	src="/__bear/jquery-ui/js/jquery-ui-1.7.custom.min.js"></script>
<script type="text/javascript"
	src="/__bear/jquery-ui/js/jquery.cookie.js"></script>
<link href="/__bear/prettify/prettify.css" type="text/css"
	rel="stylesheet" />
<script type="text/javascript" src="/__bear/prettify/prettify.js"></script>
<script type="text/javascript"><!--
    $(function(){
        // Tabs
        $('#tabs').tabs({ajaxOptions: { async : true, dataType : "html"}});
        prettyPrint();
    });
    --></script>
</head>
<body onload="">
<h1>BEAR Back Trace</h1>
<div id="tabs">
<ul>
	<li><a href="#index">Summary</a></li>
	<?php
	for ($i = 0; $i < $levelNum; $i++) {
	    echo "<li><a href=\"#tab-{$i}\">{$i}</a></li>";
	}
	?>
	<li><a href="#raw">Raw</a></li>
</ul>

<div id="index">
<ol id="trace-summary" class="timeline">
<?php
echo $summaryPage;
?>
</ol>
</div>
<?php
for ($i = 0; $i < $levelNum; $i++) {
    $page = isset($tracePage[$i]) ? $tracePage[$i] : 'n/a';
    echo " <div id=\"tab-{$i}\">" . $page . "</div>";
}
?>
<div id="raw"><?php
echo $raw;
?></div>
</div>
</body>
</html>
