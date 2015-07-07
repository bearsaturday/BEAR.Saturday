<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Aspect
 * @subpackage Advice
 * @author     Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright  2008-2015 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id: Advice.php 2486 2011-06-06 07:44:05Z akihito.koriyama@gmail.com $
 * @link       http://www.bear-project.net/
 */

/**
 * アドバイス抽象クラス
 *
 * <pre>
 * リソースクラスに織り込まれるアドバイス（インターセプター）の抽象クラスです。
 *
 * ROリソースクラスに織り込まれるメソッド（アドバイス）を実装します。
 * アクセスを通じてROリソースに値をセットします。
 *
 * 以下のアドバイスメソッドがあります。BEAR_Aspect_<Type>_Interfaceインターフェイスをimplementします。
 *
 * <code>
 * around <メソッドの実行前後のアドバイス（メソッドを置き換える）>
 * before <メソッドの実行前のアドバイス>
 * after  <メソッドの実行後のアドバイス>
 * </code>
 *
 * @category   BEAR
 * @package    BEAR_Aspect
 * @subpackage Advice
 * @author     Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright  2008-2015 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id: Advice.php 2486 2011-06-06 07:44:05Z akihito.koriyama@gmail.com $
 * @link       http://www.bear-project.net/
 *
 */
abstract class BEAR_Aspect extends BEAR_Base
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
    protected function proceed(array $values)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $result = $this->_config['ref']['method']->invoke($this->_config['obj'], $values);

        return $result;
    }

    /**
     * joinpointの取得
     *
     * アスペクト対象のリフレクションを返す
     *
     * @return stdClass
     */
    protected function getJoinPoint()
    {
        return $this->_config['obj'];
    }

    /**
     * joinpointメソッドのリフレクションを返す
     *
     * @return array method リフレクション
     */
    protected function getJoinPonitMethod()
    {
        return $this->_config['method'];
    }
}
