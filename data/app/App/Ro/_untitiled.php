<?php
/**
 * App
 *
 * @category   BEAR
 * @package    BEAR.app
 * @subpackage Ro
 * @author     $Author:$ <username@example.com>
 * @license    @license@ http://@license_url@
 * @version    Release: @package_version@ $Id:$
 * @link       http://@link_url@
 */

/**
 * Sample resource
 *
 * @category   BEAR
 * @package    BEAR.app
 * @subpackage Ro
 * @author     $Author:$ <username@example.com>
 * @license    @license@ http://@license_url@
 * @version    Release: @package_version@ $Id:$
 * @link       http://@link_url@
 */
class App_Ro_Untitled extends BEAR_Ro
{
    /**
     * table
     *
     * @var string
     */
    private $_table = 'table';

    /**
     * Constructor
     */
    public function __construct($config)
    {
        parent::__construct($config);
    }

    /**
     * Create
     *
     * @param array $values
     *
     * @return array
     * @throws Panda_Exception
     * @required name 名前
     * @required age  年齢
     */
    public function onCreate($values)
    {
        $extended = $this->_db->extended;
        /** @param $extended MDB2_Extended */
        $values['created_at'] = _BEAR_DATETIME; //現在時刻
        $result = $extended->autoExecute($this->_table, $values, MDB2_AUTOQUERY_INSERT);
        if (MDB2::isError($result, MDB2_ERROR_CONSTRAINT)) {
            throw new Panda_Exception('IDが重複しています', 409);
        } elseif (MDB2::isError($result)) {
            throw new Panda_Exception('登録できませんでした', 500);
        }
        return $result;
    }

    /**
     * Update
     *
     * @param array $values 引数
     *
     * @return mixed
     * @required id
     */
    public function onUpdate($values)
    {
        $extended = $this->_db->extended;
        $values['updated_at'] = _BEAR_DATETIME;
        /* @var $extended MDB2_Extended */
        $where = 'id = ' . $this->_db->quote($values['id'], 'integer');
        $extended->autoExecute($this->_table, $values, MDB2_AUTOQUERY_UPDATE, $where);
        if (!isset($values['profile'])) {
            return;
        }
        // profile
        $params = $values['profile'];
        $where = 'user_id = ' . $this->_db->quote($values['id'], 'integer');
        $params['updated_at'] = _BEAR_DATETIME;
        $result = $extended->autoExecute(App_DB::TABLE_PROFILE, $params, MDB2_AUTOQUERY_UPDATE, $where);
        if (!$result) {
            // updateできなかったらinsert
            unset($params['updated_at']);
            $params['user_id'] = $this->_db->quote($values['id'], 'integer');
            $params['created_at'] = _BEAR_DATETIME;
            $result = $extended->autoExecute(App_DB::TABLE_PROFILE, $params, MDB2_AUTOQUERY_INSERT);
        }
        return $result;
    }

    /**
     * Read
     *
     * @param array $values
     *
     * @return array
     * @optional id ID,未指定の場合は全県
     */
    public function onRead($values)
    {
        //db
        $where = isset($values['id']) ? ' WHERE id = ' . $this->_db->quote($values['id'], 'integer') : "";
        $sql = "SELECT * FROM {$this->_table}{$where}";
        if (isset($values['id'])) {
            $result = $this->_db->queryRow($sql);
        } else {
            $result = $this->_db->queryAll($sql);
        }
        return $result;
    }

    /**
     * Delete
     *
     * @param array $values 引数
     *
     * @return MDB2_Result
     *
     * @required id
     */
    public function onDelete($values)
    {
        $extended = $this->_db->extended;
        $values['deleted_at'] = _BEAR_DATETIME;
        /* @var $extended MDB2_Extended */
        $where = 'id = ' . $this->_db->quote($values['id'], 'integer');
        $result = $extended->autoExecute($this->_table, $values, MDB2_AUTOQUERY_UPDATE, $where);
        return $result;
    }

    /**
     * Link
     *
     * @return string
     *
     * @required id
     */
    public function onLink($values)
    {
        $links = array('profile' => "user/profile?user_id={$values['id']}");
        return $links;
    }
}