<?php
require '../../BEAR.php';

error_reporting(E_ERROR | E_WARNING | E_PARSE);

$config['package'] = 'BEAR';
$config['channel'] = 'bearsaturday.github.io/pear';
$config['release_ver'] = BEAR::VERSION;
$config['api_ver'] = $config['release_ver'];
$config['stability'] = 'beta';
$config['summery'] = 'PHP Framework Package';
$config['description'] = 'BEAR is web application framework package.';
$config['dep_php'] = '5.2.0';
$config['pear_ver'] = '1.6.0';
$config['note'] = 'BEAR.Saturday (for PHP 5.2)';
/**
 * This is the only setup function needed
 */
require_once __DIR__ . '/PEAR/PackageFileManager2.php';
// recommended - makes PEAR_Errors act like exceptions (kind of)
PEAR::setErrorHandling(PEAR_ERROR_DIE);

$packagexml = new PEAR_PackageFileManager2();
//$packagexml->specifySubpackage($p2, false, true);
$packagexml->setOptions(array('filelistgenerator' => 'file',
      'packagedirectory' => dirname(dirname(dirname(__FILE__))),
      'baseinstalldir' => '/',
      'ignore' => array('CVS/', '.svn/', 'package/', '.git/'),
'exceptions' => array('BEAR/BEAR/bin/bear.sh' => 'script'),
'installexceptions' => array('BEAR/BEAR/bin/bear.sh' => '/'),
'installas' => array('BEAR/BEAR/bin/bear.sh' => 'bear'),
'dir_roles' => array('data'=>'data', 'bin'=>'script', 'vendors'=>'php'),
      'simpleoutput' => true));


$packagexml->setPackageType('php');
$packagexml->addRelease();
$packagexml->setPackage($config['package']);
$packagexml->setChannel($config['channel']);
$packagexml->setReleaseVersion($config['release_ver']);
$packagexml->setAPIVersion($config['api_ver']);
$packagexml->setReleaseStability($config['stability']);
$packagexml->setAPIStability($config['stability']);
$packagexml->setSummary($config['summery']);
$packagexml->setDescription($config['description']);
$packagexml->setNotes($config['note']);
$packagexml->setPhpDep($config['dep_php']);
$packagexml->setPearinstallerDep($config['pear_ver']);
$packagexml->addMaintainer('lead', 'koriyama' , 'Koriyama', 'akihito.koriyama@gmail.com');
$packagexml->setLicense('The BSD License', 'http://www.opensource.org/licenses/bsd-license.php');

$packagexml->addRole('sh', 'script');
$packagexml->addRole('conf', 'php');
$packagexml->addRole('yml', 'php');
$packagexml->addRole('tpl', 'php');
$packagexml->addInstallAs('BEAR/BEAR/bin/bear.sh', 'bear');

// dependency pear.bear-project.net
$packagexml->addPackageDepWithChannel('required', 'Panda', 'bearsaturday.github.io/pear', '0.3.38');
// dependency pear.zfcampus.org1
//$packagexml->addPackageDepWithChannel('required', 'zf', 'pear.zfcampus.org', '1.10.2');
// dependency pear.php.net
$packagexml->addPackageDepWithChannel('required', 'Cache_Lite', 'pear.php.net', '1.7.0');
$packagexml->addPackageDepWithChannel('required', 'HTML_QuickForm', 'pear.php.net', '3.2.5');
$packagexml->addPackageDepWithChannel('required', 'HTML_QuickForm_Renderer_Tableless', 'pear.php.net', '0.6.0');
$packagexml->addPackageDepWithChannel('required', 'HTTP_Request2', 'pear.php.net', '1.1.0');
$packagexml->addPackageDepWithChannel('required', 'HTTP_Session2', 'pear.php.net', '0.7.2');
$packagexml->addPackageDepWithChannel('required', 'Log', 'pear.php.net', '1.9.3');
$packagexml->addPackageDepWithChannel('required', 'Net_UserAgent_Mobile', 'pear.php.net', '0.26.0');
$packagexml->addPackageDepWithChannel('required', 'Pager', 'pear.php.net', '2.3.6');
$packagexml->addPackageDepWithChannel('required', 'Spreadsheet_Excel_Writer', 'pear.php.net', '0.9.0');
$packagexml->addPackageDepWithChannel('required', 'XML_RPC', 'pear.php.net', '1.4.5');
$packagexml->addPackageDepWithChannel('required', 'XML_Serializer', 'pear.php.net', '0.18.0');
$packagexml->addPackageDepWithChannel('required', 'I18N_UnicodeString', 'pear.php.net', '0.2.1');
$packagexml->addPackageDepWithChannel('required', 'XML_RSS', 'pear.php.net', '0.9.10');
$packagexml->addPackageDepWithChannel('required', 'MDB2', 'pear.php.net', '2.4.0');
$packagexml->addPackageDepWithChannel('required', 'MDB2_Driver_mysqli', 'pear.php.net', '1.4.0');
$packagexml->addPackageDepWithChannel('required', 'XML_RSS', 'pear.php.net', '0.7.2');
$packagexml->addPackageDepWithChannel('required', 'Var_Dump', 'pear.php.net', '1.0.3');
$packagexml->addPackageDepWithChannel('required', 'Text_Highlighter', 'pear.php.net', '0.7.1');
$packagexml->addPackageDepWithChannel('required', 'Config', 'pear.php.net', '1.10.0');
$packagexml->addPackageDepWithChannel('required', 'File', 'pear.php.net', '1.3.0');
$packagexml->addPackageDepWithChannel('required', 'Console_CommandLine', 'pear.php.net', '1.0.6');
$packagexml->addPackageDepWithChannel('required', 'Console_Color', 'pear.php.net', '1.0.2');
$packagexml->addPackageDepWithChannel('required', 'Console_Table', 'pear.php.net', '1.1.3');
$packagexml->addPackageDepWithChannel('required', 'File_SearchReplace', 'pear.php.net', '1.1.2');
$packagexml->addPackageDepWithChannel('required', 'HTML_CSS', 'pear.php.net', '1.5.4');
$packagexml->addPackageDepWithChannel('required', 'Net_Server', 'pear.php.net', '1.0.2');
//$packagexml->addPackageDepWithChannel('required', 'FirePHPCore', 'pear.firephp.org', '0.3.1');
$packagexml->addPackageDepWithChannel('required', 'Services_JSON', 'pear.php.net', '1.0.2');

// optional (for developper)

// $packagexml->addGlobalReplacement('package-info', '@PEAR-VER@', 'version');
$packagexml->addGlobalReplacement('pear-config', '@PEAR-DIR@', 'php_dir');
$packagexml->addGlobalReplacement('package-info', '@package_version@', 'version');
$packagexml->addGlobalReplacement('pear-config', '@DATA-DIR@', 'data_dir');
$packagexml->addGlobalReplacement('pear-config', '@PHP-BIN@', 'bin_dir');



$packagexml->generateContents();
//$pkg = &$packagexml->exportCompatiblePackageFile1();

if (isset($_GET['make']) || (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')) {
    //    $pkg->writePackageFile();
    $packagexml->writePackageFile();
    //    $packagexml->debugPackageFile();
} else {
    //    $pkg->debugPackageFile();
    //    $packagexml->writePackageFile();
    $packagexml->debugPackageFile();
}
