<?php
ini_set('display_errors', 0);
require_once 'App.php';
require_once 'BEAR.php';
require_once 'Tree.php';

include 'BEAR/Page/Header/Interface.php';
$db = BEAR::dependency('BEAR_Log')->getPageLogDb();
if (isset($_GET['id'])) {
    $stmt = $db->prepare("SELECT log FROM pagelog WHERE ROWID = :id");
    $stmt->bindParam(":id", $_GET['id'], PDO::PARAM_INT);
} else {
    $stmt = $db->prepare("SELECT log FROM pagelog where ROWID = last_insert_rowid()");
}
$stmt->execute();
$log = $stmt->fetchAll();
$pageLog = unserialize($log[0]['log']);
// ファイル情報を取得
$logFile = _BEAR_APP_HOME . '/logs/page.log';
spl_autoload_unregister(array('BEAR', 'onAutoload'));
$includeFiles = $pageLog['include'];
$files = array('page' => array(), 'ro' => array(), 'app' => array());
foreach ($includeFiles as $includeFile) {
    if (strpos($includeFile, _BEAR_APP_HOME . '/htdocs') !== false) {
        $files['page'][] = $includeFile;
    } elseif (strpos($includeFile, _BEAR_APP_HOME . '/App/Ro') !== false) {
        $files['ro'][] = $includeFile;
    } elseif (strpos($includeFile, _BEAR_APP_HOME . '/App/') !== false) {
        $files['app'][] = $includeFile;
    }
}
$pageFile = $files['page'][0];
$path = dirname(str_replace(_BEAR_APP_HOME . '/htdocs/', '', $pageFile));
$pathDir = ($path === '.') ? '' : $path . '/';
$app = BEAR::get('app');
$pageDir = _BEAR_APP_HOME . $app['BEAR_View']['path'] . 'pages/' . $pathDir;
if ($dir = opendir($pageDir)) {
    while (($ifile = readdir($dir)) !== false) {
        // is_dirでなぜかディレクトリが判別できない
        if(substr($ifile, 0, 1) == '.' || is_dir($ifile) || strpos($ifile, '.') === false){
            continue;
        }
        $files['view'][] = $pageDir . $ifile;
    }
    closedir($dir);
}
// Treeを描画
$tree = new BEAR_Tree();
$tree->tree('#container_id1', $files['page'], '<span class=\"tree_label\">Page</span>');
$tree->tree('#container_id2', $files['ro'], '<span class=\"tree_label\">Resource</span>');
$tree->tree('#container_id3', $files['view'], '<span class=\"tree_label\">View template</span>');
$tree->tree('#container_id', _BEAR_APP_HOME, '<hr /><span class=\"tree_label\">Project</span>');
$initialOpeningFile = str_replace(_BEAR_APP_HOME, '', $pageFile);
$tree->exec($initialOpeningFile);