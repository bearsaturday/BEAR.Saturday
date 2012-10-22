<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Ro
 * @subpackage Prototype
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id:$ Ro.php 1416 2010-02-26 12:18:11Z koriyama@bear-project.net $
 * @link       http://www.bear-project.net/
 */

/**
 * Debugプロトタイプリソース
 *
 * リソースのプロトタイプ（リクエスト）のDebugクラスです。Debug時にBEAR_Ro_Prototypeとサービスロケータを使って入れ替えて使用します。
 *
 * @category   BEAR
 * @package    BEAR_Ro
 * @subpackage Prototype
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id:$ Ro.php 1416 2010-02-26 12:18:11Z koriyama@bear-project.net $
 * @link       http://www.bear-project.net/
 */
class BEAR_Ro_Prototype_Debug extends BEAR_Ro_Prototype
{
    /**
     * Inject
     *
     * @return void
     */
    public function onInject()
    {
        parent::onInject();
        $this->_log = BEAR::dependency('BEAR_Log');
    }

    /**
     * リソースリクエスト実行
     *
     * @return BEAR_Ro
     */
    public function request()
    {
        $start = microtime(true);
        $this->_log->start();
        parent::request();
        $time = microtime(true) - $start;
        $this->_ro->setHeader('_time', $time);
        $log = array('Time' => $time);
        $appLog = $this->_log->stop();
        if ($appLog) {
            $log['Log'] = array_values($appLog);
        }
        if ($log) {
            $this->_ro->setHeader('_log', $log);
        }
        return $this->_ro;
    }
}
