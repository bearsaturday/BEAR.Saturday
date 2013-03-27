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
 * @version   SVN: Release: @package_version@ $Id: JoinPoint.php 2485 2011-06-05 18:47:28Z koriyama@bear-project.net $
 * @link      http://www.bear-project.net/
 */

/**
 * ジョインポイント
 *
 * アドバイスが織り込まれるターゲットクラスのリフレクションを用いて
 * 情報を取得したりメソッドを実行したりできます。
 *
 * @category  BEAR
 * @package   BEAR_Aspect
 * @author    Akihito Koriyama <koriyama@bear-project.net>
 * @copyright 2008-2011 Akihito Koriyama All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   Release: @package_version@ $Id: JoinPoint.php 2485 2011-06-05 18:47:28Z koriyama@bear-project.net $
 * @link      http://www.bear-project.net
 */
class BEAR_Aspect_JoinPoint extends BEAR_Base
{
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
        /** @noinspection PhpUndefinedMethodInspection */
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
     * @return stdClass
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
