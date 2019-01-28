<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
/**
 * The MIT License
 *
 * Copyright (c) 2008 Asial Corporation Japan, Daichi Kamemoto <daichi@asial.co.jp>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
require_once 'selectorToXPath.php';
@require_once 'HTML/CSS.php';

/**
 * DoCoMo向けに外部参照/<style>タグののCSSをインラインのstyle要素に埋め込む
 *   PerlのHTML::DoCoMoCSS
 *   ( http://search.cpan.org/~tokuhirom/HTML-DoCoMoCSS-0.01/lib/HTML/DoCoMoCSS.pm )
 *   のPHP移殖版
 */
class toInlineCSSDoCoMo
{
    private $base_dir = './';

    public function setBaseDir($base_dir)
    {
        $this->base_dir = $base_dir;

        return $this;
    }

    public static function getInstance()
    {
        return new self();
    }

    public function apply($document, $base_dir = '')
    {
        if (! $base_dir) {
            $base_dir = $this->base_dir;
        }

        // XHTMLをパース
        $dom = new DOMDocument();
        $dom->loadHTML($document);
        $dom_xpath = new DOMXPath($dom);

        // 外部参照のCSSファイルを抽出する
        $nodes = $dom_xpath->query('//link[@rel="stylesheet" or @type="text/css"] | //style[@type="text/css"]');

        $add_style = [];
        $psudo_classes = ['a:hover', 'a:link', 'a:focus', 'a:visited'];
        foreach ($nodes as $key => $node) {
            // CSSをパース
            $html_css = new HTML_CSS();
            if ($node->tagName == 'link' && $href = $node->attributes->getNamedItem('href')) {
                // linkタグの場合
                if (! file_exists($base_dir . $href->nodeValue)) {
                    throw new UnexpectedValueException('ERROR: ' . $base_dir . $href->nodeValue . ' file does not exist');
                }

                #TODO: @importのサポート
                $css_string = file_get_contents($base_dir . $href->nodeValue);
            } elseif ($node->tagName == 'style') {
                // styleタグの場合
                $css_string = $node->nodeValue;
            }

            $css_error = $html_css->parseString($css_string);
            if ($css_error) {
                throw new RuntimeException('ERROR: css parse error');
            }

            // a:hover, a:link, a:focus a:visited を退避
            foreach ($psudo_classes as $psude_class) {
                $block = $html_css->toInline($psude_class);
                if (! $block) {
                    continue;
                }

                $add_style[] = $psude_class . '{' . $block . '}';
            }

            // CSSをインライン化
            $css = $html_css->toArray();
            foreach ($css as $selector => $style) {
                #TODO: 疑似要素のサポート
                // 疑似要素と@ルールはスルー(selectorToXPath的にバグでやすい)
                if (strpos($selector, '@') !== false) {
                    continue;
                }
                if (strpos($selector, ':') !== false) {
                    continue;
                }

                $xpath = selectorToXPath::toXPath($selector);
                $elements = $dom_xpath->query($xpath);

                if ($elements->length == 0) {
                    continue;
                }
                // inlineにするCSS文を構成(toInline($selector)だとh2, h3 などでうまくいかないバグ？があったため)
                $inline_style = '';
                foreach ($style as $k => $v) {
                    $inline_style .= $k . ':' . $v . ';';
                }
                foreach ($elements as $element) {
                    if ($attr_style = $element->attributes->getNamedItem('style')) {
                        // style要素が存在する場合は追記
                        if (substr($attr_style->nodeValue, -1) != ';') {
                            $inline_style = ';' . $inline_style;
                        }
                        $attr_style->nodeValue .= $inline_style;
                    } else {
                        // style要素が存在しない場合は追加
                        $element->setAttribute('style', $inline_style);
                    }
                }
            }

            // 読み込み終わったノードを削除
            $parent = $node->parentNode;
            $parent->removeChild($node);
        }

        // 疑似クラスを<style>タグとして追加
        if (! empty($add_style)) {
            $new_style = implode(PHP_EOL, $add_style);
            $new_style = implode(PHP_EOL, ['<![CDATA[', $new_style, ']]>']);

            $head = $dom_xpath->query('//head');
            $new_style_node = new DOMElement('style', $new_style);
            $head->item(0)->appendChild($new_style_node)->setAttribute('type', 'text/css');
        }

        return $dom->saveHTML();
    }
}
