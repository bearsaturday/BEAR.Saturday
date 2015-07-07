<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Ro
 * @author    Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright 2008-2015 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id:$ Ro.php 1416 2010-02-26 12:18:11Z akihito.koriyama@gmail.com $
 * @link      https://github.com/bearsaturday
 */

/**
 * リソースオブジェクトデバッククラス
 *
 * リソース可視化などを行います
 *
 * @category  BEAR
 * @package   BEAR_Ro
 * @author    Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright 2008-2015 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: @package_version@ $Id:$ Ro.php 1416 2010-02-26 12:18:11Z akihito.koriyama@gmail.com $
 * @link      https://github.com/bearsaturday
 */
class BEAR_Ro_Debug extends BEAR_Base
{
    /**
     * デバック用リソース表示フラグ
     *
     * @var bool
     */
    private static $_hasResourceDebug = false;

    /**
     * Inject
     *
     * @return void
     */
    public function onInject()
    {
        $app = BEAR::get('app');
        $this->_config['path'] = $app['BEAR_View']['path'];
    }

    /**
     * デバック用にリソースを表示
     *
     * <pre>
     * テンプレート付リソースの時、リソースの詳細情報が画面上に表示されます。
     * デバックモード時に_resourceクエリーを付加すれば有効になります。
     *
     * _resource=html リソーステンプレート適用されたHTML表示
     * _resource=body リソースのBodyをprinta形式で表示
     * </pre>
     *
     * @param BEAR_Ro $ro リソースオブジェクト
     *
     * @return string
     */
    public function getResourceToString(BEAR_Ro $ro)
    {
        self::$_hasResourceDebug = true;
        $config = $ro->getConfig();
        $chainLinks = $ro->getHeader('_linked');
        $resourceHtml = $ro->getHtml();
        if (!(isset($_GET['_resource']))) {
            return $resourceHtml;
        }
        $body = $ro->getBody();
        if (isset($_GET['_resource'])) {
            if ($_GET['_resource'] === 'html') {
                $renderer = new Text_Highlighter_Renderer_Html(array('tabsize' => 4));
                /** @noinspection PhpDynamicAsStaticMethodCallInspection */
                $hlHtml = Text_Highlighter::factory("HTML");
                $hlHtml->setRenderer($renderer);
                if ($resourceHtml == '') {
                    $resourceHtml = '<span class="hl-all">(*Empty String)</span>';
                } else {
                    $resourceHtml = '<span class="hl-all">' . $hlHtml->highlight($resourceHtml) . '</span>';
                }
            } elseif ($_GET['_resource'] === 'body') {
                $resourceHtml = print_a($body, 'return:1');
                $logs = $ro->getHeader('_log');
                foreach ((array)$logs as $logKey => $logVal) {
                    $resourceHtml .= '<div class="bear-resource-info-label">' . $logKey . '</div>';
                    $resourceHtml .= is_scalar($logVal) ? $logVal : print_a($logVal, 'return:1');
                }
            }
        }
        //class editor
        if (class_exists($config['class'], false)) {
            $classEditorLink = $this->_getClassEditorLink($config['class'], $config['uri']);
        } else {
            $classEditorLink = $config['uri'];
        }
        $labelUri = $classEditorLink;
        $labelVaules = ($config['values']) ? '?' . http_build_query($config['values']) : '';
        $linkLabel = '';
        if ($chainLinks) {
            $linkLabels = array();
            foreach ($chainLinks as $links) {
                $linkLabels[] .= (count($links) > 1 ? implode(':', $links) : $links[0]);
            }
            $linkLabel .= '_link=' . implode(',', $linkLabels);
        }
        $result = '<div class="bear-resource-template">';
        $result .= '<div class="bear-resource-label">' . $labelUri;
        $result .= '<span class="bear-resource-values">' . $labelVaules . '</span>';
        $result .= '<span class="bear-resource-links">' . $linkLabel . '</span>';
        $result .= '' . " + (" . '';
        $result .= '<span><a border="0" title="' . $config['options']['template'] . '" href="/__panda/edit/?file=';
        $result .= (_BEAR_APP_HOME . $this->_config['path'] . 'elements/' . $config['options']['template'] . '.tpl') . '">';
        $result .= $config['options']['template'] . '</a>)</span>';
        $result .= '</div>' . $resourceHtml . '</div>';
        return $result;
    }

    /**
     * Get resource class editor link
     *
     * @param string $class
     * @param string $uri
     *
     * @return string
     */
    private function _getClassEditorLink($class, $uri)
    {
        $ref = new ReflectionClass($class);
        $file = $ref->getFileName();
        $classEditorLink = "<a href=\"/__panda/edit/?file={$file}\">{$uri}</a>";
        return $classEditorLink;
    }

    /**
     * リソースのデバック表示
     *
     * firePHPコンソールにリソースを表示します。
     *
     * @param BEAR_Ro $ro リソースオブジェクト
     *
     * @return void
     */
    public function debugShowResource(BEAR_Ro $ro)
    {
        $app = BEAR::get('app');
        $config = $ro->getConfig();
        if (!isset($config['method']) || !function_exists('FB')) {
            return;
        }
        $labelUri = "[resource] {$config['method']} {$config['uri']}";
        $labelUri .= ($config['values']) ? '?' . http_build_query($config['values']) : "";
        $body = $ro->getBody();
        if (is_array($body) && isset($body[0]) && is_array($body[0])) {
            // bodyが表構造と仮定
            $table = array();
            $table[] = (array_values(array_keys($body[0])));
            foreach ((array)$body as $val) {
                $table[] = array_values((array)$val);
            }
            FB::table($labelUri, $table);
        } else {
            FB::group("$labelUri", array('Collapsed' => true));
            FB::log($body);
            FB::groupEnd();
        }
    }

    /**
     * デバックモードでリソース表示しているか？
     *
     * フレームワーク用
     *
     * @return bool
     */
    public function hasResourceDebug()
    {
        return self::$_hasResourceDebug;
    }
}
