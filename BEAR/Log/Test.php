<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * BEAR_Log_Test
 *
 *
 *
 *
 *
 * @Singleton
 */
class BEAR_Log_Test extends BEAR_Log
{
    /**
     * リソースログ
     *
     * @var array
     */
    private $_resourceLog = array();

    /**
     * フォームログ
     *
     * @var array
     */
    private $_formLog = array();

    /**
     * ヘッダーにtest用ログを出力
     */
    public function __destruct()
    {
        header('X-bear-form-log: ' . json_encode($this->_formLog));
        header('X-bear-resource-log: ' . json_encode($this->_resourceLog));
    }

    /**
     * TEST用にformとresourceだけ別に記録
     *
     * @param string $logKey
     * @param null   $logValue
     */
    public function log($logKey, $logValue = null)
    {
        if ($logKey === 'form') {
            $this->_formLog[] = $logValue;
        }
        if ($logKey === 'resource') {
            $this->_resourceLog[] = $logValue;
        }
        parent::log($logKey, $logValue);
    }
}
