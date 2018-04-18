<?php
/**
 * BEAR
 *
 * PHP versions 5
 */

/**
 * Voidアダプター
 *
 * キャッシュが無効になります
 *
 *
 *
 *
 *
 * @Singleton
 */
final class BEAR_Cache_Adapter_Void extends BEAR_Base
{
    /**
     * @var BEAR_Log
     */
    protected $_log;

    /**
     * キャッシュ無効
     *
     * @param string $name キー
     * @param mixed  $args 値
     *
     * @return bool
     */
    public function __call($name, $args)
    {
        if ($this->_config['debug']) {
            $log = array('name' => $name, 'args' => $args);
            $this->_log->log('BEAR_Cache_Adapter_None', $log);
        }

        return null;
    }

    /**
     * Inject
     */
    public function onInject()
    {
        $this->_log = BEAR::dependency('BEAR_Log');
    }

    /**
     * Set Life
     *
     * @param int $life
     *
     * @return BEAR_Cache_Adapter_Void
     */
    public function setLife(
        /** @noinspection PhpUnusedParameterInspection */
        $life = null
    ) {
        return $this;
    }
}
