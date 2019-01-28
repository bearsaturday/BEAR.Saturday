<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * BEARシェル
 */
class BEAR_Dev_Shell extends BEAR_Base
{
    /**
     * Appパス設定コマンド
     */
    const CMD_SET_APP = 'set-app';

    /**
     * Appパス表示コマンド
     */
    const CMD_SHOW_APP = 'show-app';

    /**
     * リソース作成表示コマンド
     */
    const CMD_CREATE = 'create';

    /**
     * リソース読み込み表示コマンド
     */
    const CMD_READ = 'read';

    /**
     * リソース変更表示コマンド
     */
    const CMD_UPDATE = 'update';

    /**
     * リソース削除表示コマンド
     */
    const CMD_DELETE = 'delete';

    /**
     * キャッシュクリアコマンド
     */
    const CMD_CLEAR_CACHE = 'clear-cache';

    /**
     * ログクリアコマンド
     */
    const CMD_CLEAR_LOG = 'clear-log';

    /**
     * キャッシュ＆ログクリアコマンド
     */
    const CMD_CLEAR_ALL = 'clear-all';

    /**
     * リソース1アイテム最大表示幅
     */
    const STRING_LENGTH = 300;

    /**
     * コマンド結果
     *
     * @var string
     */
    private $_result = '';

    /**
     * コマンド
     *
     * @var string
     */
    private $_command;

    /**
     * コマンドの実行
     */
    public function execute()
    {
        $argv = $this->_config['argv'];
        if (! isset($argv[1])) {
            return;
        }
        // parse
        $cli = $this->_config['cli'];
        $parser = new Console_CommandLine([
            'name' => 'bear',
            'description' => 'BEAR command line interface',
            'version' => BEAR::VERSION,
            'add_help_option' => true,
            'add_version_option' => true
        ]);
        // create resource
        $subCmd = $parser->addCommand(
            self::CMD_CREATE,
            ['description' => 'create resource.']
        );
        $subCmd->addOption(
            'file',
            [
                'short_name' => '-g',
                'long_name' => '--file',
                'action' => 'StoreString',
                'description' => 'load arguments file.'
            ]
        );
        $subCmd->addOption(
            'app',
            [
                'short_name' => '-a',
                'long_name' => '--app',
                'action' => 'StoreString',
                'description' => 'specify application path. *Notice* use this on the end of line.'
            ]
        );
        $subCmd->addArgument(
            'uri',
            ['description' => 'resource URI']
        );
        // read resource
        $subCmd = $parser->addCommand(
            self::CMD_READ,
            ['description' => 'show resource.']
        );
        $subCmd->addOption(
            'file',
            [
                'short_name' => '-g',
                'long_name' => '--file',
                'action' => 'StoreString',
                'description' => 'load arguments file.'
            ]
        );
        $subCmd->addOption(
            'length',
            [
                'short_name' => '-l',
                'long_name' => '--len',
                'action' => 'StoreInt',
                'description' => 'filter specific lenght each data.'
            ]
        );
        $subCmd->addOption(
            'format',
            [
                'short_name' => '-f',
                'long_name' => '--format',
                'action' => 'StoreString',
                'description' => 'default | table | php | json | csv | printa '
            ]
        );
        $subCmd->addOption(
            'app',
            [
                'short_name' => '-a',
                'long_name' => '--app',
                'action' => 'StoreString',
                'description' => 'specify application path. *Notice* use this on the end of line.'
            ]
        );
        $subCmd->addArgument(
            'uri',
            ['description' => 'resource URI']
        );
        // update resource
        $subCmd = $parser->addCommand(
            self::CMD_UPDATE,
            ['description' => 'update resource.']
        );
        $subCmd->addOption(
            'file',
            [
                'short_name' => '-g',
                'long_name' => '--file',
                'action' => 'StoreString',
                'description' => 'load arguments file.'
            ]
        );
        $subCmd->addOption(
            'app',
            [
                'short_name' => '-a',
                'long_name' => '--app',
                'action' => 'StoreString',
                'description' => 'specify application path. *Notice* use this on the end of line.'
            ]
        );
        $subCmd->addArgument(
            'uri',
            ['description' => 'resource URI']
        );
        // delete resource
        $subCmd = $parser->addCommand(
            self::CMD_DELETE,
            ['description' => 'delete resource.']
        );
        $subCmd->addOption(
            'file',
            [
                'short_name' => '-a',
                'long_name' => '--file',
                'action' => 'StoreString',
                'description' => 'load arguments file.'
            ]
        );
        $subCmd->addOption(
            'app',
            [
                'short_name' => '-a',
                'long_name' => '--app',
                'action' => 'StoreString',
                'description' => 'specify application path. *Notice* use this on the end of line.'
            ]
        );
        $subCmd->addArgument('uri', ['description' => 'resource URI']);
        // clear-cache
        $parser->addCommand(
            'clear-cache',
            ['description' => 'clear all cache.']
        );
        // clear-log
        $parser->addCommand(
            'clear-log',
            ['description' => 'clear all log.']
        );
        // clear-all
        $parser->addCommand(
            'clear-all',
            ['description' => 'clear cache and log.']
        );
        if ($cli) {
            $subCmd->addArgument(
                'path',
                ['description' => 'destination path. ex) /var/www/bear.test']
            );
            $subCmd->addOption(
                'pearrc',
                [
                    'short_name' => '-c',
                    'long_name' => '--pearrc',
                    'action' => 'StoreString',
                    'description' => 'find user configuration in `file`'
                ]
            );
            // set app
            $subCmd = $parser->addCommand(
                self::CMD_SET_APP,
                ['description' => 'set application path.']
            );
            $subCmd->addArgument(
                'path',
                ['description' => 'application path. ex) /var/www/bear.test']
            );
            // show app
            $subCmd = $parser->addCommand(
                self::CMD_SHOW_APP,
                ['description' => 'show application path.']
            );
        }
        //exec
        try {
            ob_start();
            $this->_command = $parser->parse(count($argv), $argv);
            ob_get_clean();
            $commandName = $this->_command->command_name;
            switch ($this->_command->command_name) {
                case self::CMD_SET_APP:
                    $path = $this->_command->command->args['path'];
                    $path = $this->_makeFullPath($path);
                    $this->_setApp($path);

                    break;
                case self::CMD_SHOW_APP:
                    $this->_checkAppExists();
                    $this->_showApp();

                    break;
                case self::CMD_CLEAR_CACHE:
                    $this->_checkAppExists();
                    $this->clearCache();

                    break;
                case self::CMD_CLEAR_LOG:
                    $this->_checkAppExists();
                    $this->clearLog();

                    break;
                case self::CMD_CLEAR_ALL:
                    $this->_checkAppExists();
                    $this->clearCache();
                    $this->clearLog();

                    break;
                case self::CMD_CREATE:
                case self::CMD_READ:
                case self::CMD_UPDATE:
                case self::CMD_DELETE:
                    $this->_checkAppExists();
                    $uri = $this->_command->command->args['uri'];
                    $values = $this->_command->command->options['file'] ? BEAR::loadValues(
                        $this->_command->command->options['file']
                    ) : [];
                    $this->_result = $this->_request($commandName, $uri, $values)->getRo();
                    $this->_config['debug'] = true;

                    break;
                default:
                    if ($this->_config['cli']) {
                        $this->_result = "BEAR: {$argv[1]}: command not found, try 'bear --help'";
                    } else {
                        $this->_result = "BEAR: {$argv[1]}: command not found, try 'help'";
                    }

                    return;
            }
        } catch (Exception $e) {
            $parser->displayError($e->getMessage());
        }
    }

    /**
     * 表示文字列の取得
     *
     * resultプロパティの結果を表示用文字列にします。
     *
     * @return string
     */
    public function getDisplay()
    {
        $result = '';
        if ($this->_result instanceof BEAR_Ro) {
            $code = $this->_result->getCode();
            $header = $this->_result->getHeaders();
            $body = $this->_result->getBody();
            if ($this->_config['cli'] != true) {
                if (is_array($body)) {
                    array_walk_recursive(
                        $body,
                        create_function('&$val, $key', '$val = htmlspecialchars($val);')
                    );
                } elseif (is_string($body)) {
                    $body = htmlspecialchars($body);
                }
            }
            $result .= $this->printStrong("code\n");
            $result .= "${code}\n";
            $result .= $this->printStrong("header\n");
            $result .= ($header) ? $this->_printR($header) : "n/a\n";
            $result .= $this->printStrong("body\n");
            $len = (isset($this->_command->command->options['length'])) ? $this->_command->command->options['length'] : self::STRING_LENGTH;
            if (is_array($body)) {
                array_walk_recursive(
                    $body,
                    create_function(
                        '&$val,
                        $key',
                        '$val = (is_string($val) && strlen($val) >= ' . $len . ')?
                        substr($val, 0, ' . $len . ' - 2) . "…" : $val;'
                    )
                );
            }
            if (is_array($body) || is_object($body)) {
                switch ($this->_command->command->options['format']) {
                    case 'var':
                        $result = var_export($body, true);

                        break;
                    case 'php':
                        $result = serialize($body);

                        break;
                    case 'json':
                        $result = json_encode($body, true);

                        break;
                    case 'table':
                        $result = $this->_getTextTable($body);

                        break;
                    case 'csv':
                        $result = $this->_getCsv($body);

                        break;
                    case 'printa':
                        $result .= print_a($body, 'return:1');

                        break;
                    case 'default':
                    default:
                        $ajax = BEAR::dependency('BEAR_Page_Ajax');
                        if ($ajax->isAjaxRequest()) {
                            $result .= print_a($body, 'return:1');
                        } else {
                            $result .= $this->_getVarExport($body);
                        }

                        break;
                }
            } elseif (is_scalar($body)) {
                $result .= $body;
            } else {
                $result .= $this->_printR($body, true);
            }
        } elseif (is_scalar($this->_result)) {
            $result .= "{$this->_result}";
        } else {
            $result .= $this->_printR($this->_result);
        }

        return $result;
    }

    /**
     * 強調文字表示
     *
     * @param string $str   文字列
     * @param string $color 色
     *
     * @return string
     *
     * @internal CLIとHTMLで表示を変えています
     */
    public function printStrong($str, $color = 'b')
    {
        if ($this->_config['cli']) {
            return Console_Color::convert("%{$color}{$str}%n");
        }

        return '<strong>' . $str . '</strong>';
    }

    /**
     * キャッシュクリア
     */
    public function clearCache()
    {
        clearstatcache();
        BEAR::factory('BEAR_Cache')->deleteAll();
        BEAR_Util::unlinkRecursive(_BEAR_APP_HOME . '/tmp/session/');
        BEAR_Util::unlinkRecursive(_BEAR_APP_HOME . '/tmp/cache_lite/');
        BEAR_Util::unlinkRecursive(_BEAR_APP_HOME . '/tmp/smarty_cache/');
        BEAR_Util::unlinkRecursive(_BEAR_APP_HOME . '/tmp/smarty_templates_c/');
        BEAR_Util::unlinkRecursive(_BEAR_APP_HOME . '/tmp/misc/');
        if (function_exists('apc_clear_cache')) {
            apc_clear_cache('user');
            apc_clear_cache();
        }
    }

    /**
     * ログクリア
     */
    public function clearLog()
    {
        BEAR_Util::unlinkRecursive(_BEAR_APP_HOME . '/logs/');
    }

    /**
     * ログクリア
     */
    public function clearAll()
    {
        $this->clearCache();
        $this->clearLog();
        BEAR_Util::unlinkRecursive(_BEAR_APP_HOME . '/tmp/session/');
    }

    /**
     * ドキュメント作成
     *
     * @param string $path ドキュメント保存先パス
     */
    public function makeDoc($path)
    {
        echo "making phpdoc ...\n";
        $exec = 'phpdoc --pear --output HTML:Smarty:PHP --parseprivate off';
        $exec .= ' --title AppDoc --hidden --sourcecode --javadocdesc';
        $exec .= ' --ignore ' . _BEAR_APP_HOME . '/data,';
        $exec .= _BEAR_APP_HOME . 'tests/,';
        $exec .= _BEAR_APP_HOME . 'vendors/,';
        $exec .= _BEAR_APP_HOME . 'log/,';
        $exec .= _BEAR_APP_HOME . ',tmp/';
        $exec .= ' --directory ' . _BEAR_APP_HOME;
        $exec .= ' --target ' . $path;
        echo "${exec}\n";
        ob_flush();
        shell_exec("${exec} &");
    }

    /**
     * Make full path
     *
     * @param unknown_type $path
     *
     * @return string
     */
    private function _makeFullPath($path)
    {
        if (substr($path, -1) === '/') {
            $path = substr($path, 0, -1);
        }
        if ($path[0] !== '/') {
            $fullpath = $_SERVER['PWD'] . '/' . $path;
            $path = $fullpath;
        }

        return $path;
    }

    /**
     * アプリケーションパスの存在チェック
     */
    private function _checkAppExists()
    {
        if (class_exists('App', false)) {
            return;
        }
        $bearrc = getenv('HOME') . '/.bearrc';
        if (! file_exists($bearrc)) {
            $msg = "App path undefined. Please set application path with 'bear set-app' command.\n";
            $msg .= "ex) $ bear set-app /var/www/bear.test\n";
            echo $msg;
            exit;
        }
    }

    /**
     * テキストテーブル文字列の取得
     *
     * 文字列でテーブルを描画します。
     *
     * @param array $data
     *
     * @return string
     */
    private function _getTextTable($data)
    {
        if (! $data) {
            return '';
        }
        $table = new Console_Table();
        $table->setAlign(0, CONSOLE_TABLE_ALIGN_LEFT);
        $data = ($this->isList($data) !== true) ? [$data] : $data;
        $table->setHeaders(array_keys($data[0]));
        foreach ($data as $row) {
            $table->addRow($row);
        }

        return $table->getTable();
    }

    /**
     * Is array list ?
     */
    private function isList($data)
    {
        return isset($data[0]) && is_array(array_keys($data[0]));
    }

    /**
     * var_export文字列の取得
     *
     * @param string $str 文字列
     *
     * @return string
     */
    private function _getVarExport($str)
    {
        ob_start();
        var_export($str);

        return ob_get_clean();
    }

    /**
     * Get csv text
     *
     * @param unknown_type $data
     *
     * @return string
     *
     * @see http://jp.php.net/fputcsv
     */
    private function _getCsv($data)
    {
        $data = ($this->isList($data) !== true) ? [$data] : $data;
        $outstream = fopen('php://temp', 'r+');
        foreach ($data as $row) {
            fputcsv($outstream, $row);
        }
        rewind($outstream);
        $csv = '';
        while (! feof($outstream)) {
            $buffer = fgets($outstream);
            $csv .= $buffer;
        }
        fclose($outstream);

        return $csv;
    }

    /**
     * print_r文字列の取得
     *
     * @param string $var 文字列
     *
     * @return string
     */
    private function _printR($var)
    {
        if ($this->_config['cli']) {
            $result = print_r($var, true);
        } else {
            return print_r($var, true);
        }

        return $result;
    }

    /**
     * リソースリクエスト
     *
     * @param string $method メソッド
     * @param string $uri    URI（クエリー付き)
     * @param array  $values 引数
     *
     * @return BEAR_Ro
     */
    private function _request($method, $uri, array $values)
    {
        $resource = BEAR::dependency('BEAR_Resource');
        /* @var $resource BEAR_Resource */
        $params = ['uri' => $uri, 'values' => $values];

        return $resource->request($method, $uri, $values)->getRo();
    }

    /**
     * アプリケーションパス設定
     *
     * @param string $path アプリケーションパス
     */
    private function _setApp($path)
    {
        $path = realpath($path);
        if (! $path) {
            throw new ErrorException('App path is not valid', 0, 0, __FILE__, __LINE__);
        }
        $bearrc = getenv('HOME') . '/.bearrc';
        $config = (file_exists($bearrc)) ? unserialize(file_get_contents($bearrc)) : [];
        $config['app'] = realpath($path);

        file_put_contents($bearrc, serialize($config));
    }

    /**
     * アプリケーションパス表示
     *
     * ~/.bearrcを読んで表示します
     */
    private function _showApp()
    {
        $bearrc = getenv('HOME') . '/.bearrc';
        if (file_exists($bearrc)) {
            $config = unserialize(file_get_contents($bearrc));
            $appPath = $config['app'];
            $this->_result = $appPath;
        } else {
            $this->_result = "'{$bearrc}' is not set.";
        }
    }
}
