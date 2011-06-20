<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR
 * @author    $Author: $ <username@example.com>
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id:$$
 * @link      http://www.bear-project.net/
 * @ignore
 */
/**
 * アプリケーションパッケージ作成用定義
 *
 */
define('APP_ID', 'app');
define('APP_NAME', 'BEAR App');
define('APP_PACKAGE_NAME', 'App');
define('APP_VERSION', '0.5.61');
define('APP_RELEASE_STATUS', 'beta');
define('APP_DESCRIPTION', 'BEAR web application.');
define('DEVELOPER_NAME_SHORT', 'anonymous');
define('DEVELOPER_NAME_LONG', 'anonymous');
define('DEVELOPER_NAME_EMAIL', 'anonymous@example.com');
define('PEAR_PATH', '@PHP-BIN@');
/**
 * package.xml生成
 *
 * BEARのパッケージ生成のためのpackage.xmlファイルを生成します。
 * <pre>
 * -------------------------------------
 * <pre>
 * パッケージ生成
 *
 * </pre><code>
 * $ php make_package.php
 * $ php make_package.php __uri make
 * $ cd ..
 * $ pear package-validate package.xml
 * $ pear package
 * </code>
 * <pre>
 * パッケージインストール
 *
 * </pre><code>
 * $ pear install <Application>-x.x.x.tar.gz
 *
 * または
 *
 * $pear channel-discovier <Channel URI>
 * $pear install <Channel Alias>/<Application>
 *
 * </code>
 *
 * @package     App
 * @subpackage  Script
 * @author      $Author: koriyama@bear-project.net $
 * @version     $Id: make_package.php 1041 2009-10-14 12:41:44Z koriyama@bear-project.net $
 *
 */
/**
 * PackageFileManager2読み込み
 */
set_include_path(get_include_path() . PATH_SEPARATOR . PEAR_PATH);
/**
 * App読み込み
 */
require_once '../../App.php';
App::$debug = false;
require_once 'PEAR/PackageFileManager2.php';
require_once 'PEAR/PackageFileManager/File.php'; // avoid bugs
PEAR::setErrorHandling(PEAR_ERROR_DIE);
$releaseStatus = "beta";
$notes = APP_NAME;
$version = APP_VERSION;
$config = array('filelistgenerator' => 'file',
    'packagedirectory' => dirname(dirname(dirname(__FILE__))) . '/',
    'baseinstalldir' => APP_ID,
    'ignore' => array('CVS/',
        'package.xml',
        '.svn/',
        '.htaccess',
        'beardev/',
        '.DS_Store',
        'error.log',
        'page.log'),
    'changelogoldtonew' => false,
    'description' => APP_DESCRIPTION,
    'dir_roles' => array('data' => 'data'),
    'roles' => array('php' => 'data', 'tpl' => 'data', 'txt' => 'data'));
$packagexml = new PEAR_PackageFileManager2();
$packagexml->setOptions($config);
$packagexml->setPackage(APP_PACKAGE_NAME);
$packagexml->setSummary($notes);
$packagexml->setDescription(APP_DESCRIPTION);
$packagexml->setAPIVersion($version);
$packagexml->setReleaseVersion($version);
$packagexml->setReleaseStability(APP_RELEASE_STATUS);
$packagexml->setAPIStability(APP_RELEASE_STATUS);
$packagexml->setNotes($notes);
$packagexml->setLicense('The BSD License', 'http://www.opensource.org/licenses/bsd-license.php');
$packagexml->setPackageType('php');
$packagexml->addRole('*', 'data');
$packagexml->setPhpDep('5.2.0');
$packagexml->setPearinstallerDep('1.4.0');
// Maintainer
$packagexml->addMaintainer('lead', DEVELOPER_NAME_SHORT, DEVELOPER_NAME_LONG, DEVELOPER_NAME_EMAIL);
$packagexml->addRelease();
// dependency
// dependency
$packagexml->addPackageDepWithChannel('required', 'BEAR', 'pear.bear-project.net', '0.2.0');
//$packagexml->addPackageDepWithChannel('required', 'Spreadsheet_Excel_Writer', 'pear.php.net', '0.9.0');
/**
 * user if no arg
 */
if (!isset($_SERVER['argv'][1])) {
    print("Usage: php{$_SERVER['argv'][0]} package_uri uri|channel [make]\n");
    print("ex)\n1) php {$_SERVER['argv'][0]} pear.example.co.jp channel \n");
    print("2) php {$_SERVER['argv'][0]} pear.example.co.jp channel make\n");
    print("3) cd ../../\n");
    print("4) pear package-validate\n");
    print("5) pear package\n");
    exit();
} else {
    $uri = $_SERVER['argv'][1];
}
/**
 * network install package or local package
 */
if ($_SERVER['argv'][2] == 'uri') {
    $packagexml->setUri($uri);
} else {
    $packagexml->setChannel($uri);
}
/**
 * this makes local install package
 */
$packagexml->generateContents();
// note use of debugPackageFile() - this is VERY important
if ($_SERVER['argv'][3] != 'debug') {
    _debugPrint("writePackageFile\n");
    $result = $packagexml->writePackageFile();
} else {
    $result = $packagexml->debugPackageFile();
    _debugPrint("debugPackageFile\n");
}
if (PEAR::isError($result)) {
    _debugPrint($result->getMessage() . "\n");
    exit();
}
_debugPrint("End Script\n");

/**
 * デバック表示
 *
 * @param string $message でバックメッセージ
 *
 * @return void
 */
function _debugPrint($message)
{
    return print($message);
}
