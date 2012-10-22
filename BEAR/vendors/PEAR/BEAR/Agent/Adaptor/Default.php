<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Agent
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Adapter.php 687 2009-07-03 14:49:14Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Agent/BEAR_Agent.html
 */
/**
/**
 * Default(PC)エージェントアダプター
 *
 * エージェントに依存する入出力関連の依存と、エージェント別テンプレート選定に使われるエージェントのロールを設定するoエージェントアダプタークラスです。
 *
 * @category  BEAR
 * @package   BEAR_Agent
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Adapter.php 687 2009-07-03 14:49:14Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Agent/BEAR_Agent.html
 * @abstract
 *  */
class BEAR_Agent_Adaptor_Default extends BEAR_Agent_Adaptor implements BEAR_Agent_Adaptor_Interface
{
    /**
     * コンストラクタ
     *
     * @param array $config 設定
     */
    public function __construct(array $config)
	{
		parent::__construct($config);
        $this->_config['role'] = array(BEAR_Agent::UA_DEFAULT);
		$this->_config['agent_filter'] = true;
        $this->_config['charset'] = 'utf-8';
        $this->_config['enable_js'] = true;
        $this->_config['enable_inline_css'] = false;
        $this->_config['enable_css'] = true;
        $this->_config['enable_session'] = true;
        $this->_config['session_trans_sid'] = false;
    }
}