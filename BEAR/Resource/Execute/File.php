<?php
/**
 * BEAR
 *
 * PHP versions 5
 */

/**
 * スタティクファイルリソース
 */
class BEAR_Resource_Execute_File extends BEAR_Resource_Execute_Adapter
{
    /**
     * リソースリクエスト実行
     *
     * スタティクファイルをリソースとして扱います。
     * XML, YAML, CSV, INIファイルをサポートしています。
     *
     * @throws BEAR_Resource_Execute_Exception
     *
     * @return mixed
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
        $noHeader = (is_array(
            $result
        )) || ((isset($this->_config['values']['header']) && ((bool) $this->_config['values']['header'] === true)));
        if (! $noHeader && is_array($result)) {
            $index = array_shift($result);
            $array = array();
            foreach ($result as $value) {
                $array[] = array_combine($index, array_values($value));
            }
        }

        return $result;
    }
}
