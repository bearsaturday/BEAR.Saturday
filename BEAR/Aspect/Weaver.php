<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Aspect
 * @author    Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright 2008-2015 Akihito Koriyama All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version    @package_version@
 * @link      https://github.com/bearsaturday
 */

/**
 * アドバイスウイーバー
 *
 * ターゲットにアドバイスを織り込むのに用います。
 *
 * @category  BEAR
 * @package   BEAR_Aspect
 * @author    Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright 2008-2015 Akihito Koriyama All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version    @package_version@
 * @link      https://github.com/bearsaturday
 */
/** @noinspection PhpDocMissingThrowsInspection */
class BEAR_Aspect_Weaver extends BEAR_Base
{
    /**
     * beforeアドバイス
     */
    const ADVICE_BEFORE = 'before';

    /**
     * afterアドバイス
     */
    const ADVICE_AFTER = 'after';

    /**
     * aroundアドバイス
     */
    const ADVICE_AROUND = 'around';

    /**
     * throwアドバイス
     */
    const ADVICE_THROWING = 'throwing';

    /**
     * returnアドバイス
     */
    const ADVICE_RETURNING = 'returning';

    /**
     * アスペクト実行
     *
     * <pre>
     * 指定されたアスペクト（アドバイスタイプとアドバイスクラスのセット）を
     * 受け取ったオブジェクトのメソッドに織り込んで実行します。
     *
     * ジョインポイントの種類は以下のものがあります。
     *
     * before    事前実行
     * around    元のメソッドのオーバーライド
     * after     事後実行
     * returning 結果を返す前に実行
     * throwing  例外発生時
     * </pre>
     *
     * @param object $obj    ターゲットオブジェクト
     * @param array  $values メソッド引数
     *
     * @return mixed
     * @throws Exception $e
     */
    public function invoke($obj, array $values)
    {
        $result = array();
        $this->_config['object'] = $obj;
        $this->_config['values'] = $values;
        $this->_config['entry_values'] = $values;
        // joinPointをセット
        $joinPoint = BEAR::factory('BEAR_Aspect_JoinPoint', $this->_config);
        /** @var $joinPoint BEAR_Aspect_JoinPoint */
        //beforeアドバイス
        if (isset($this->_config['aspects'][self::ADVICE_BEFORE])) {
            foreach ($this->_config['aspects'][self::ADVICE_BEFORE] as $adviceClass) {
                $this->_adviceValidation($adviceClass, 'BEAR_Aspect_Before_Interface');
                $advice = BEAR::factory($adviceClass, $this->_config);
                /** @var $advice BEAR_Aspect_Before_Interface */
                $result = $advice->before($this->_config['values'], $joinPoint);
                if (is_array($result)) {
                    $this->_config['values'] = $result;
                }
            }
        }
        //aroudアドバイス
        $adviceClass = isset($this->_config['aspects'][self::ADVICE_AROUND]) ? $this->_config['aspects'][self::ADVICE_AROUND][0] : null;
        try {
            if (isset($adviceClass)) {
                // aroundメソッドでオーバライド
                $this->_adviceValidation(
                    $adviceClass,
                    'BEAR_Aspect_Around_Interface'
                );
                $advice = BEAR::factory($adviceClass, $this->_config);
                /** @var $advice BEAR_Aspect_Around_Interface */
                $result = $advice->around($this->_config['values'], $joinPoint);
            } else {
                // オリジナルメソッド
                $method = new ReflectionMethod($this->_config['class'], $this->_config['method']);
                $result = $method->invoke($this->_config['object'], $this->_config['values']);
            }
            $isException = false;
        } catch (Exception $e) {
            $isException = true;
        }
        //afterアドバイス
        if (isset($this->_config['aspects'][self::ADVICE_AFTER])) {
            foreach ($this->_config['aspects'][self::ADVICE_AFTER] as $adviceClass) {
                $this->_adviceValidation($adviceClass, 'BEAR_Aspect_After_Interface');
                $advice = BEAR::factory($adviceClass, $this->_config);
                /** @var $advice BEAR_Aspect_After_Interface */
                $return = $advice->after($result, $joinPoint);
                $result = $return ? $return : $result;
                // set
                $this->_config['values'] = $result;
            }
        }
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        if (isset($result) && (PEAR::isError($result) | $isException)) {
            if (isset($this->_config['aspects'][self::ADVICE_THROWING])) {
                foreach ($this->_config['aspects'][self::ADVICE_THROWING] as $adviceClass) {
                    $this->_adviceValidation($adviceClass, 'BEAR_Aspect_Throwing_Interface');
                    $advice = BEAR::factory($adviceClass, $this->_config);
                    /** @var $advice BEAR_Aspect_Throwing_Interface */
                    $return = $advice->throwing($result, $joinPoint);
                    $result = $return ? $return : $result;
                    // set
                    $this->_config['values'] = $result;
                }
            } elseif ($isException === true) {
                throw $this->_exception(__CLASS__);
            }
        } elseif (isset($this->_config['aspects'][self::ADVICE_RETURNING])) {
            foreach ($this->_config['aspects'][self::ADVICE_RETURNING] as $adviceClass) {
                $this->_adviceValidation($adviceClass, 'BEAR_Aspect_Returning_Interface');
                $advice = BEAR::factory($adviceClass, $this->_config);
                /** @var $advice BEAR_Aspect_Returning_Interface */
                $return = $advice->returning($result, $joinPoint);
                $result = $return ? $return : $result;
                // set
                $this->_config['values'] = $result;
            }
        }
        return $result;
    }

    /**
     * aroundアドバイスのバリデーション
     *
     * aroundアドバイスは一度しか呼べず、BEAR_Aspect_Around_Interfaceインターフェイスを実装している必要があります。
     *
     * @param string $adviceClass アドバイスクラス
     * @param string $interface   インターフェイス
     *
     * @return void
     * @throws BEAR_Aspect_Exception
     */
    private function _adviceValidation($adviceClass, $interface)
    {
        $isValid = !$this->_config['debug'] || class_exists($adviceClass, true) && array_search(
            $interface,
            class_implements($adviceClass)
        );
        if ($isValid === false) {
            $msg = "{$adviceClass} is not valid advice.";
            $info = array('advice' => $adviceClass, 'aspects' => $this->_config['aspects']);
            throw $this->_exception($msg, array('info' => $info));
        }
    }
}
