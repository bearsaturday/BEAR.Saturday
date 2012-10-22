<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Aspect
 * @subpackage Aspect
 * @author     Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright  2008 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: $Id: Weaver.php 871 2009-09-12 20:52:15Z koriyama@users.sourceforge.jp $
 * @link       http://api.bear-project.net/BEAR_Aspect/BEAR_Aspect.html
 */
/**
 * アドバイスウイーバー
 *
 * <pre>
 * AOPでアドバイスメソッドを織り込むクラスです。
 * </pre>
 *
 * @category   BEAR
 * @package    BEAR_Aspect
 * @subpackage Aspect
 * @author     Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright  2008 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: $Id: Weaver.php 871 2009-09-12 20:52:15Z koriyama@users.sourceforge.jp $
 * @link       http://api.bear-project.net/BEAR_Aspect.html
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
    const ADVICE_THROW = 'throw';

    /**
     * returnアドバイス
     */
    const ADVICE_RETURN = 'return';

    /**
     * コンストラクタ
     *
     * @param array $config 設定
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * アスペクト実行
     *
     * <pre>
     * 指定されたアスペクト（アドバイスタイプとアドバイスクラスのセット）を
     * 受け取ったオブジェクトのメソッドに織り込んで実行します。
     *
     * ジョインポイントの種類は以下のものがあります。
     *
     * before 事前実行
     * around 元のメソッドのオーバーライド
     * after  事後実行
     * return 結果を返す前に実行
     * </pre>
     *
     * @param object $obj    ターゲットオブジェクト
     * @param array  $values メソッド引数
     *
     * @return mixed
     */
    public function invoke($obj, array $values)
    {
        $this->_config['object'] = $obj;
        $this->_config['values'] = $values;
        $this->_config['entry_values'] = $values;
        // joinPointをセット
        $joinPoint = BEAR::dependency('BEAR_Aspect_JoinPoint', $this->_config);
        $aspects = $this->_config['aspects'];
        //beforeアドバイス
        if (isset($aspects[self::ADVICE_BEFORE])) {
            foreach ($aspects[self::ADVICE_BEFORE] as $adviceClass) {
                $this->_adviceValidation($adviceClass, 'BEAR_Aspect_Before_Interface');
                $advice = BEAR::dependency($adviceClass, $this->_config);
                $result = $advice->before($this->_config['values'], $joinPoint);
                if (is_array($result)) {
                    $this->_config['values'] = $result;
                }
            }
        }
        //aroudアドバイス
        $adviceClass = $aspects[self::ADVICE_AROUND][0];
        if ($adviceClass) {
            // aroundメソッドでオーバライド
            $this->_adviceValidation($adviceClass, 'BEAR_Aspect_Around_Interface');
            $advice = BEAR::dependency($adviceClass, $this->_config);
            $result = $advice->around($this->_config['values'], $joinPoint);
        } else {
            // オリジナルメソッド
            $method = new ReflectionMethod($this->_config['class'], $this->_config['method']);
            $result = $method->invoke($this->_config['object'], $this->_config['values']);
        }
        //afterアドバイス
        if (isset($aspects[self::ADVICE_AFTER])){
            foreach ($aspects[self::ADVICE_AFTER] as $adviceClass) {
                $this->_adviceValidation($adviceClass, 'BEAR_Aspect_After_Interface');
                $advice = BEAR::dependency($adviceClass, $this->_config);
                $return = $advice->after($result, $joinPoint);
                $result = $return ? $return : $result;
                // set
                $this->_config['values'] = $result;
            }
        }
        //returnアドバイス
        $adviceClass = isset($aspects[self::ADVICE_RETURN][0]) ? $aspects[self::ADVICE_RETURN][0] : false;
        if ($adviceClass) {
            $this->_adviceValidation($adviceClass, 'BEAR_Aspect_Return_Interface');
            $advice = BEAR::dependency($adviceClass, $this->_config);
            $result = $advice->return($this->_config['values'], $joinPoint);
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
     *
     */
    private function _adviceValidation($adviceClass, $interface)
    {
        $isValid = $this->_config['debug'] && class_exists($adviceClass, true) && array_search($interface, class_implements($adviceClass));
        if (!$isValid) {
            $msg = "$adviceClass is not valid";
            $info = array('advice' => $adviceClass);
            throw $this->_exception($msg, array('info' => $info));
        }
    }
}

