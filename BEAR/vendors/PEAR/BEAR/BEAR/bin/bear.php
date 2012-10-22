<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_WARNING);

/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR
 * @subpackage bin
 * @author     Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright  2008 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: $Id: bear.php 1317 2010-01-05 07:41:02Z koriyama@users.sourceforge.jp $
 * @link       http://api.bear-project.net/BEAR_Dev/BEAR_Dev.html
 */
/**
 * BEARコマンド
 *
 * <pre>
 * 開発時にコマンドラインインターフェイスです
 *
 * Commands:
 create       create resource.
 read         show resource.
 update       update resource.
 delete       delete resource.
 init-app     create new application.
 set-app      set application path.
 show-app     show application path.
 clear-cache  clear all cache.
 clear-log    clear all log.
 clear-all    clear cache and log.
 make-doc     make application documents.
 * </pre>
 *
 * @category   BEAR
 * @package    BEAR_Dev
 * @subpackage Shell
 * @author     Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright  2008 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: $Id: bear.php 1317 2010-01-05 07:41:02Z koriyama@users.sourceforge.jp $
 * @link       http://api.bear-project.net/BEAR_Dev/BEAR_Dev.html
 */

// BEAR Path
$bearPath = realpath(dirname(dirname(dirname(dirname(__FILE__)))));
set_include_path($bearPath . PATH_SEPARATOR . get_include_path());

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
        $this->checkAppPath();
    }

    /**
     * 初期化
     *
     * @return void
     */
    public function init()
    {
        $bearrc = getenv('HOME') . '/.bearrc';
        if (file_exists($bearrc)) {
            $bearrc = unserialize(file_get_contents($bearrc));
            $appPath = $bearrc['app'];
            if (file_exists("{$appPath}/App.php")) {
                ini_set('include_path', $appPath . ':' . get_include_path());
                $bearMode = $this->getBearMode($appPath);
                $_SERVER['bearmode'] = $bearMode;
                require_once "{$appPath}/App.php";
            } else {
                $this->_initBear();
            }
        } else {
            $this->_initBear();
        }
    }

    /**
     * BEAR初期化
     *
     * @return bool
     */
    private function _initBear()
    {
        if (!class_exists('BEAR')) {
            require_once 'BEAR.php';
        }
        BEAR::init(array());
    }

    /**
     * 実行
     *
     * @return void
     */
    public function exec()
    {
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
        $display = $display ? $display : 'Ok.';
        echo $display .  "\n";
        exit();
    }

    /**
     * bearmodeの取得
     *
     * htaccessファイルからモードを読みます
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

    private function _checkAppPath()
    {
        $bearrc = getenv('HOME') . '/.bearrc';
        if (file_exists($bearrc)) {
            $bearrc = unserialize(file_get_contents($bearrc));
            $appPath = $bearrc['app'];
            if (file_exists("{$appPath}/App.php")) {
                echo "(App set is not valid [{$appPath}]\n, Use 'bear set-app' to set app path.)\n\n";
            }
        }
    }
}

//bearコマンド実行
$bin = new BEAR_Bin_Bear();
$bin->run();