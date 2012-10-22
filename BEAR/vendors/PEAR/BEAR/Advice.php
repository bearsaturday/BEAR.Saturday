<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Aop
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Advice.php 889 2009-09-16 00:22:54Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Annotate/BEAR_Annotate.html
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
 * @category  BEAR
 * @package   BEAR_Aspect
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Advice.php 889 2009-09-16 00:22:54Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Annotate/BEAR_Annotate.html
 * @todo      アドバイス追加
 *
 */
abstract class BEAR_Aspect extends BEAR_Base
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
    protected function proceed(array $values)
    {
        $result = $this->_config['ref']['method']->invoke($this->_config['obj'], $values);
        return $result;
    }

    /**
     * joinpointの取得
     *
     * アスペクト対象のリフレクションを返す
     *
     * @return object
     */
    protected function getJoinPoint()
    {
        return $this->_config['obj'];
    }

    /**
     * joinpointメソッドのリフレクションを返す
     *
     * @return array methodリフレクション
     */
    protected function getJoinPonitMethod()
    {
        return $this->_config['method'];
    }
}