<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 *
 * @link       https://github.com/bearsaturday
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
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 *
 * @link       https://github.com/bearsaturday
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
