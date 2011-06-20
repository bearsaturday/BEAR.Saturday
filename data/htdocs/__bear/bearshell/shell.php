<?php
/**
 * App
 *
 * @package App
 * @subpackage page
 */
require_once 'App.php';
$_SERVER['__bear'] = 1;

require_once 'BEAR/vendors/debuglib.php';

/**
 * Shellサービス
 *
 * <pre>
 * </pre>
 *
 * @package     App
 * @subpackage  page
 * @author      $Author: koriyama@users.sourceforge.jp $
 * @version     $$
 */
class Page_Shell extends App_Page
{

    /**
     * Inject
     *
     * フォーマット:
     * array('form'=>array('フォーム名1'=>'フォーム1データ', 'フォーム名2'=>'フォーム1データ',
     *   'value'=>'Bear.Value');
     * </pre>
     */
    public function onInject()
    {
        $this->_ajax = BEAR::dependency('BEAR_Page_Ajax', array('security_check' => false));
        $val = $this->_ajax->getAjaxRequest();
        $this->injectArg('q', trim($val['form']['form']['q']));
    }

    /**
     * 初期化
     *
     * @return void
     */
    public function onInit(array $args)
    {
        $q = $args['q'];
        $argv = explode(' ', $q);
        switch (true) {
            case ($q == 'help') :
                $help = <<<END
Commands:
  clear        clear screen
  config       show application configulation.
  info         show server info.
  bear         bear command, type bear -h for more info.
END;
                $this->_ajax->addAjax('js', array('shell' => "<pre>$help</pre>"));
                break;
            case ($q == 'clear') :
                $this->_ajax->addAjax('js', array('clear' => ''));
                break;
            case ($q == 'config') :
                $app = BEAR::get('app');
                $info = '<strong>app<strong><br />' . print_a($app, 'return:1');
                $this->_ajax->addAjax('js', array('shell' => $info));
                break;
            case ($q == 'info') :
                $info = '<strong>$_SERVER<strong><br />';
                $info .= print_a($_SERVER, 'return:1');
                $info .= '<strong>$_ENV<strong><br />';
                $info .= print_a($_ENV, 'return:1');
                $info .= '<strong>$_COOKIE<strong><br />';
                $info .= print_a($_COOKIE, 'return:1');
                $this->_ajax->addAjax('js', array('shell' => $info));
                break;
            case (isset($argv[0]) && $argv[0] === 'bear') : //bearコマンド
                $this->_shell($argv);
                break;
            default :
                $this->_ajax->addAjax('js', array(
                'shell' => "BEAR: {$argv[0]}: Command not found<br/>"));
                break;
        }
    }

    /**
     * 出力
     *
     * @retun void
     */
    public function onOutput()
    {
        $this->output('ajax');
    }

    /**
     * bearシェルコマンド
     *
     * @param array $argv
     *
     * @return void
     */
    private function _shell(array $argv)
    {
        if (!isset($argv[1])) {
            $argv[1] = '--help';
        }
        $_SERVER['argv'] = $argv;
        $config = array('argv' => $argv, 'cli' => false);
        $shell = BEAR::dependency('BEAR_Dev_Shell', $config, true);
        $shell->execute();
        $display = $shell->getDisplay();
        $display = $display ? $display : 'Ok.';
        $buff = ob_get_clean();
        $result = '<pre>' . $display . $buff . '</pre>';
        $this->_ajax->addAjax('js', array('shell' => $result));
    }
}

BEAR_Main::run('Page_Shell');