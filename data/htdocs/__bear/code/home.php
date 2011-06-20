<?php
require_once 'App.php';
spl_autoload_unregister(array('BEAR', 'onAutoload')); // to avoid auto loader run by unserialize
$pageLog = @unserialize(file_get_contents(_BEAR_APP_HOME . '/logs/page.log'));
$includes = $pageLog['include'];

foreach ($includes as $file) {
    if (strpos($file, _BEAR_APP_HOME) !== false) {
        $file = str_replace(_BEAR_APP_HOME . '/', '', $file);
        if (strpos($file, 'tmp/') !== false) {
            $link = "<ul><a href=\"/__bear/code/source.php?do=$file\" target=\"phpcs\">";
            $link .= "<span style=\"color:gray; font-style:italic;\">{$file}</span></a></ul>";
        } else {
            $link = "<ul><a href=\"/__bear/code/source.php?do=$file\" target=\"phpcs\">{$file}</a></ul>";
        }
        echo $link;
    }
}
?>
</li>
