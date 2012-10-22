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
 * @version    SVN: Release: $Id: JoinPoint.php 889 2009-09-16 00:22:54Z koriyama@users.sourceforge.jp $
 * @link       http://api.bear-project.net/BEAR_Aspect/BEAR_Aspect.html
 */
/**
 * ジョインポイントクラス
 *
 * <pre>
 * アドバイスが織り込まれるターゲットクラスのプロパティやリフレクションを返します。
 *
 * @category  BEAR
 * @package   BEAR_Aspect
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: JoinPoint.php 889 2009-09-16 00:22:54Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Annotate/BEAR_Annotate.html
 *
 */
class BEAR_Aspect_JoinPoint extends BEAR_Base
{

    /**
     * コンストラクタ
     *
     * @param array $config コンフィグ
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * 折り込み元のオリジナルメソッドを実行
     *
     * アドバイス織り込み対象元のメソッドを実行します。
     *
     * @param array $values 引数
     *
     * @return mixed
     */
    public function proceed(array $values)
    {
        $result = $this->_config['ref']['method']->invoke($this->_config['object'], $values);
        return $result;
    }

    /**
     * 引数の取得
     *
     * @return array
     */
    public function getArgument()
    {
        return $this->_config['values'];
    }

    /**
     * joinpointの取得
     *
     * jointpointを取得します。
     *
     *　@return object
     */
    public function getObject()
    {
        return $this->_config['object'];
    }

    /**
     * joinpointのメソッドリフレクションを返す
     *
     * @return ReflectionMethod
     */
    public function getMethodReflection()
    {
        return $this->_config['ref']['method'];
    }

    /**
     * joinpointのクラスリフレクションを返す
     *
     * @return ReflectionClass
     */
    public function getClassReflection()
    {
        return $this->_config['ref']['class'];
    }
}