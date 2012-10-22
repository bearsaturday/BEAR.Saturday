<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Execute
 * @author     Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright  2008 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: $Id: StaticFile.php 845 2009-08-19 10:00:26Z koriyama@users.sourceforge.jp $
 * @link       http://api.bear-project.net/BEAR_Resource/BEAR_Resource.html
 */
/**
 * スタティクファイルリソースクラス
 *
 * <pre>
 * スタティクファイルをリソースとして扱うクラスです。XML, YAML, CSV, INIファイルをサポートしています。
 * </pre>
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Execute
 * @author     Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright  2008 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: $Id: StaticFile.php 845 2009-08-19 10:00:26Z koriyama@users.sourceforge.jp $
 * @link       http://api.bear-project.net/BEAR_Resource/BEAR_Resource.html
 *  */
class BEAR_Resource_Execute_StaticFile extends BEAR_Resource_Execute_Adaptor
{

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
     * リソースアクセス
     *
     * リソースを使用します。
     *
     * @param array  $uri    URI
     * @param array  $values 引数
     * @param string $method リクエストメソッド（無効)
     *
     * @return mixed
     */
    public function request()
    {
        $method = $this->_config['method'];
        if ($method === BEAR_Resource::METHOD_READ) {
            $result = BEAR::loadValues($this->_config['file']);
        } else {
            $config = array('info' => compact('method'), 'code' => 400);
            throw new BEAR_Resource_Exception('Method not allowed with static file', $config);
        }
        return $result;
    }
}