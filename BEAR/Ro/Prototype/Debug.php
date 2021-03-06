<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * Debugプロトタイプリソース
 *
 * リソースのプロトタイプ（リクエスト）のDebugクラスです。Debug時にBEAR_Ro_Prototypeとサービスロケータを使って入れ替えて使用します。
 */
class BEAR_Ro_Prototype_Debug extends BEAR_Ro_Prototype
{
    /**
     * Inject
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
        $log = ['Time' => $time];
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
