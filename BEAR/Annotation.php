<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * アノテーション
 *
 * @Singleton
 *
 * @config string class  クラス
 * @config string method メソッド
 */
class BEAR_Annotation extends BEAR_Base
{
    /**
     * @throws ReflectionException
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->_config['ref']['method'] = new ReflectionMethod($config['class'], $config['method']);
        $this->_config['ref']['class'] = new ReflectionClass($config['class']);
        /* @noinspection PhpUndefinedMethodInspection */
        $this->_config['doc']['class'] = $this->_config['ref']['class']->getDocComment();
        /* @noinspection PhpUndefinedMethodInspection */
        $this->_config['doc']['method'] = $this->_config['ref']['method']->getDocComment();
    }

    /**
     * メソッドの必須項目をチェック
     *
     * 連想配列で受け取った値に必要なキーがあるかチェックします。
     * 問題ががればBEAR_Exceptionが投げられます。
     *
     * @param array $values 配列
     *
     * @throws BEAR_Annotation_Exception
     */
    public function required(array $values)
    {
        $match = [];
        $result = preg_match_all('/@required\\s+(\\w+)/is', $this->_config['doc']['method'], $match);
        if ($result !== false) {
            // メソッド優先
            try {
                BEAR_Util::required($match[1], $values);
            } catch (Exception $e) {
                $info = [
                    'RO' => $this->_config['ref']['method']->class . '::' . $this->_config['ref']['method']->name,
                    'required' => $match[1],
                    'values' => $values,
                    'doc' => $this->_config['doc']['method']
                ];
                $required = implode($match[1], ',');
                $msg = "@required item[{$required}] is missing.";

                throw $this->_exception($msg, ['code' => BEAR::CODE_BAD_REQUEST, 'info' => $info]);
            }
        }
    }

    /**
     * アスペクトアノテーション
     *
     * @throws ReflectionException
     *
     * @return BEAR_Aspect_Weaver|ReflectionMethod
     */
    public function aspect()
    {
        $match = [];
        preg_match_all('/@aspect\\s+(\\w+)\\s+(\\w+)/is', $this->_config['doc']['method'], $match);
        $aspects = [];
        // <アドバイスタイプ> => <アドバイスクラス>の連想配列
        $max = count($match[0]);
        for ($i = 0; $i < $max; $i++) {
            $type = $match[1][$i];
            $class = $match[2][$i];
            $aspects[$type][] = $class;
        }
        if (! $aspects) {
            return new ReflectionMethod($this->_config['class'], $this->_config['method']);
        }
        $this->_config['aspects'] = $aspects;
        // weaver
        return BEAR::factory('BEAR_Aspect_Weaver', $this->_config);
        /* @var BEAR_Aspect_Weaver $weaver */
    }
}
