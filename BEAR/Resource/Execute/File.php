<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Execute
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id: File.php 2557 2011-06-15 15:49:51Z koriyama@bear-project.net $
 * @link      http://www.bear-project.net/
 */

/**
 * スタティクファイルリソース
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Execute
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    Release: @package_version@ $Id: File.php 2557 2011-06-15 15:49:51Z koriyama@bear-project.net $
 * @link       http://www.bear-project.net
 */
class BEAR_Resource_Execute_File extends BEAR_Resource_Execute_Adapter
{
    /**
     * リソースリクエスト実行
     *
     * スタティクファイルをリソースとして扱います。
     * XML, YAML, CSV, INIファイルをサポートしています。
     *
     * @return mixed
     * @throws BEAR_Resource_Execute_Exception
     */
    public function request()
    {
        if ($this->_config['method'] === BEAR_Resource::METHOD_READ) {
            $result = BEAR::loadValues($this->_config['file']);
        } else {
            $config = array('info' => compact('method'), 'code' => 400);
            throw new BEAR_Resource_Execute_Exception('Method "read" is only allowed with static file', $config);
        }
        // valuesでheader=trueが指定されてると一行目はキーに
        $noHeader = (is_array($result)) || ((isset($this->_config['values']['header']) && ((bool)$this->_config['values']['header'] === true )));
        if (!$noHeader && is_array($result)) {
            $index = array_shift($result);
            $array = array();
            foreach ($result as $value) {
                $array[] = array_combine($index, array_values($value));
            }
        }
        return $result;
    }
}