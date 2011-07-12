<?php
ini_set('include_path', dirname(dirname(dirname(dirname(__FILE__)))) . ':' . get_include_path());
ini_set('display_errors', 0);
ini_set('log_errors', 1);

/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR
 * @subpackage Bin
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id: bear.php 2551 2011-06-14 09:32:14Z koriyama@bear-project.net $
 * @link      http://www.bear-project.net/
 */


// BEAR Path
$bearPath = realpath(dirname(dirname(dirname(dirname(__FILE__)))));
set_include_path($bearPath . PATH_SEPARATOR . get_include_path());

/**
 * BEAR CLI
 *
 * <pr>
 * Commands:
 * create       create resource.
 * read         show resource.
 * update       update resource.
 * delete       delete resource.
 * init-app     create new application.
 * set-app      set application path.
 * show-app     show application path.
 * clear-cache  clear all cache.
 * clear-log    clear all log.
 * clear-all    clear cache and log.
 * make-doc     make application documents.
 * </pre>
 *
 * @category   BEAR
 * @package    BEAR_Dev
 * @subpackage Shell
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id: bear.php 2551 2011-06-14 09:32:14Z koriyama@bear-project.net $ bear.php 2458 2011-06-02 13:09:42Z koriyama@bear-project.net $
 * @link      http://www.bear-project.net/
 */
class BEAR_Bin_Bear
{

    /**
     * bearシェル実行
     *
     * @return void
     */
    public function run()
    {
        // アプリケーションパスが正しく設定されているなら実行
        $this->init();
        $this->exec();
    }

    /**
     * 初期化
     *
     * @return void
     */
    public function init()
    {
        $appPath = $this->_getAppPath();
        ob_start();
        if ($appPath) {
            ini_set('include_path', $appPath . ':' . get_include_path());
            $bearMode = $this->getBearMode($appPath);
            $_SERVER['bearmode'] = $bearMode;
            include_once "{$appPath}/App.php";
            // CLI用ページをセット
            BEAR::set('page', new BEAR_Page_Cli(array()));
        }
        $this->_initBear();
        error_reporting(0);
    }

    /**
     * Get App path by -app or ~/.bearrc
     *
     * @return mixed
     *
     */
    private function _getAppPath()
    {
        $argv = $_SERVER["argv"];
        $count = count($argv);
        $count--;
        if ($argv[$count - 1] === '--app' || $argv[$count - 1] === '-a') {
            $appPath = realpath($argv[$count]);
            if (isset($argv[$count]) && $appPath && file_exists($appPath . '/App.php')) {
                return $appPath;
            } else {
                die("Invalid app path [{$path}]\n");
            }
        }
        $bearrc = getenv('HOME') . '/.bearrc';
        if (file_exists($bearrc)) {
            $bearrc = unserialize(file_get_contents($bearrc));
            $appPath = $bearrc['app'];
            if (file_exists("{$appPath}/App.php")) {
                return $appPath;
            }
        }
        return false;
    }

    /**
     * BEAR初期化
     *
     * @return void
     */
    private function _initBear()
    {
        if (!class_exists('BEAR')) {
            include_once 'BEAR.php';
        }
        BEAR::init();
    }

    /**
     * 実行
     *
     * @return void
     */
    public function exec()
    {
        ini_set('display_errors', 0);
        ini_set('log_errors', 1);
        error_reporting(E_ERROR);
        if ($_SERVER['argc'] == 1) {
            $_SERVER['argc'] == 2;
            $argv = array('bear.php', '--help');
        } else {
            $argv = $_SERVER['argv'];
        }
        $config = array('argv' => $argv, 'cli' => true);
        $shell = BEAR::dependency('BEAR_Dev_Shell', $config, true);
        /* @var $shell BEAR_Dev_Shell */
        $shell->execute();
        $display = $shell->getDisplay();
        $display = $display ? $display  :"\nOk.";
        echo $display .  "\n";
    }

    /**
     * bearmodeの取得
     *
     * htaccessファイルからモードを読みます
     *
     * @param string $appPath アプリケーションルートパス
     *
     * @return int
     */
    public function getBearMode($appPath)
    {
        $matches = array();
        $path = $appPath . '/htdocs/.htaccess';
        if (!file_exists($path)) {
            return 0;
        }
        $htaccess = file_get_contents($path);
        preg_match('/bearmode (\d+)/is', $htaccess, $matches);
        return (isset($matches[1])) ? $matches[1] : 0;
    }
}
//bearコマンド実行
$bin = new BEAR_Bin_Bear();
$bin->run();
