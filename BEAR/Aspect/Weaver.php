<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Aspect
 * @author    Akihito Koriyama <koriyama@bear-project.net>
 * @copyright 2008-2011 Akihito Koriyama All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id: Weaver.php 2485 2011-06-05 18:47:28Z koriyama@bear-project.net $
 * @link      http://www.bear-project.net/
 */

/**
 * アドバイスウイーバー
 *
 * ターゲットにアドバイスを織り込むのに用います。
 *
 * @category  BEAR
 * @package   BEAR_Aspect
 * @author    Akihito Koriyama <koriyama@bear-project.net>
 * @copyright 2008-2011 Akihito Koriyama All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   Release: @package_version@ $Id: Weaver.php 2485 2011-06-05 18:47:28Z koriyama@bear-project.net $
 * @link      http://www.bear-project.net
 */
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
        $this->_config['aspects'] = $this->_config['aspects'];
        //beforeアドバイス
        if (isset($this->_config['aspects'][self::ADVICE_BEFORE])) {
            foreach ($this->_config['aspects'][self::ADVICE_BEFORE] as $adviceClass) {
                $this->_adviceValidation($adviceClass, 'BEAR_Aspect_Before_Interface');
                $advice = BEAR::factory($adviceClass, $this->_config);
                $result = $advice->before($this->_config['values'], $joinPoint);
                if (is_array($result)) {
                    $this->_config['values'] = $result;
                }
            }
        }
        //aroudアドバイス
        $adviceClass = isset($this->_config['aspects'][self::ADVICE_AROUND]) ?
        $this->_config['aspects'][self::ADVICE_AROUND][0] : null;
        try {
            if (isset($adviceClass)) {
                // aroundメソッドでオーバライド
                $this->_adviceValidation(
                    $adviceClass,
                    'BEAR_Aspect_Around_Interface'
                );
                $advice = BEAR::factory($adviceClass, $this->_config);
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
                $return = $advice->after($result, $joinPoint);
                $result = $return ? $return : $result;
                // set
                $this->_config['values'] = $result;
            }
        }
        if (isset($result) && (PEAR::isError($result) | $isException)) {
            if (isset($this->_config['aspects'][self::ADVICE_THROWING])) {
                foreach ($this->_config['aspects'][self::ADVICE_THROWING] as $adviceClass) {
                    $this->_adviceValidation($adviceClass, 'BEAR_Aspect_Throwing_Interface');
                    $advice = BEAR::factory($adviceClass, $this->_config);
                    $return = $advice->throwing($result, $joinPoint);
                    $result = $return ? $return : $result;
                    // set
                    $this->_config['values'] = $result;
                }
            } elseif ($isException === true) {
                throw $e;
            }
        } elseif (isset($this->_config['aspects'][self::ADVICE_RETURNING])) {
            foreach ($this->_config['aspects'][self::ADVICE_RETURNING] as $adviceClass) {
                $this->_adviceValidation($adviceClass, 'BEAR_Aspect_Returning_Interface');
                $advice = BEAR::factory($adviceClass, $this->_config);
                $return = $advice->returning($result, $joinPoint);
                $result = $return ? $return : $result;
                // set
                $this->_config['values'] = $result;
            }
        }
        return $result;
    }

    /**
     * retruningアドバイスの実行
     *
     * @param object $obj
     * @param array  $values
     * @param string $adviceType
     * @param mixed  $inteface
     *
     * @return mixed
     */
    public function invokeRetruning($obj, array $values, $adviceType, $inteface)
    {
        //returnアドバイス
        $adviceClass = isset($this->_config['aspects'][self::ADVICE_AFTER_RETURNING][0]) ?
        $this->_config['aspects'][self::ADVICE_AFTER_RETURNING][0] : null;
        if (isset($adviceClass)) {
            $this->_adviceValidation($adviceClass, 'BEAR_Aspect_Returning_Interface');
            $advice = BEAR::factory($adviceClass, $this->_config);
            $result = $advice->returning($result, $joinPoint);
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
        $isValid = !$this->_config['debug'] || class_exists($adviceClass, true)
        && array_search($interface, class_implements($adviceClass));
        if ($isValid === false) {
            $msg = "{$adviceClass} is not valid advice.";
            $info = array('advice' => $adviceClass, 'aspects' => $this->_config['aspects']);
            throw $this->_exception($msg, array('info' => $info));
        }
    }
}
