<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Ro
 * @subpackage Prototype
 * @author     Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright  2008-2017 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    @package_version@
 * @link       https://github.com/bearsaturday
 */

/**
 * Debugプロトタイプリソース
 *
 * リソースのプロトタイプ（リクエスト）のDebugクラスです。Debug時にBEAR_Ro_Prototypeとサービスロケータを使って入れ替えて使用します。
 *
 * @category   BEAR
 * @package    BEAR_Ro
 * @subpackage Prototype
 * @author     Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright  2008-2017 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    @package_version@
 * @link       https://github.com/bearsaturday
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
