<?php
/**
 * selectorToXPath.php
 * 
 * @author Daichi Kamemoto <daichi@asial.co.jp>
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

/**
 * CSSセレクタをXPathクエリに置換する。
 * HTML::Selector::XPath
 * http://search.cpan.org/dist/HTML-Selector-XPath/lib/HTML/Selector/XPath.pm
 * のPHP移殖版
 *   理解のいたる範囲で移植したので劣化コピーかもしれない。
 */ 
class selectorToXPath
{
  /**
   * マッチングに使用する正規表現
   */
  private static $regex = array(
      'element'    => '/^(\*|[a-z_][a-z0-9_-]*)/i',
      'id_class'   => '/^([#.])([a-z0-9*_-]*)/i',
      'attribute'  => '/^\[\s*([^~\|=\s]+)\s*([~\|]?=)\s*"([^"]+)"\s*\]/i',
      'attr_box'   => '/^\[([^\]]*)\]/i',
      'attr_not'   => '/^:not\((.*?)\)/i',
      'pseudo'     => '/^:([a-z0-9_-]+)(\(([a-z0-9_-]+)\))?/i',
      'combinator' => '/^(\s*[>+~\s])/i',
      'comma'      => '/^,/i',
    );

  /**
   * CSSセレクタをXPathクエリに再構成する
   */
  public static function toXPath($input_selector)
  {
    $parts = array();

    $parts[] = '//*';
    $joint = '//';

    $last = '';
    $selector = trim($input_selector);

    while ((strlen(trim($selector)) > 0) && ($last != $selector))
    {
      $selector = trim($selector);
      $last = trim($selector);

      // Elementを取得
      if (self::pregMatchDelete(self::$regex['element'], $selector, $e))
      {
        if ($joint == 'child sign')
        {
          $joint = '//';
        }
        $parts[] = $joint;
        $parts[] = $e[1];
        $joint = '//';
      }

      // IDとClassの指定を取得
      if (self::pregMatchDelete(self::$regex['id_class'], $selector, $e))
      {
        if ($joint != '//')
        {
          if ($joint == 'child sign')
          {
            $joint = '//';
          }
          $parts[] = $joint;
          $parts[] = '*';
          $joint = '//';
        }
        switch ($e[1])
        {
          case '.':
            $parts[] = '[contains(concat( " ", @class, " "), " ' . $e[2] . ' ")]';
            break;
          case '#':
            $parts[] = '[@id="' . $e[2] . '"]';
            break;
          default:
            break;
        }
      }

      // atribauteを取得
      if (self::pregMatchDelete(self::$regex['attribute'], $selector, $e))
      {
        switch ($e[2])
        {
          case '!=':
            $parts[] = '[@' . $e[1] . '!=' . $e[3] . ']';
            break;
          case '~=':
            $parts[] = '[contains(concat( " ", @' . $e[1] . ', " "), "' . $e[3] . '")]';
            break;
          case '|=':
            $parts[] = '[@' . $e[1] . '="' . $e[3] . ' " or starts-with(@' . $e[1] . ', concat( "' . $e[3] . '", "-"))]';
            break;
          default:
            $parts[] = '[@' . $e[1] . '="' . $e[3] . '"]';
            break;
        }
      }
      else if (self::pregMatchDelete(self::$regex['attr_box'], $selector, $e))
      {
        $parts[] = '[@' . $e[1] . ']';
      }

      // notつきのattribute処理
      if (self::pregMatchDelete(self::$regex['attr_not'], $selector, $e))
      {
        if (self::pregMatchDelete(self::$regex['attribute'], $e[1], $sub_e))
        {
          switch ($sub_e[2])
          {
            case '=':
              $parts[] = '[@' . $sub_e[1] . '!=' . $sub_e[3] . ']';
              break;
            case '~=':
              $parts[] = '[not(contains(concat( " ", @' . $sub_e[1] . ', " "), "' . $sub_e[3] . '"))]';
              break;
            case '|=':
              $parts[] = '[not(@' . $sub_e[1] . '="' . $sub_e[3] . ' " or starts-with(@' . $sub_e[1] . ', concat( "' . $sub_e[3] . '", "-")))]';
              break;
            default:
              break;
          }
        }
        else if (self::pregMatchDelete(self::$regex['attr_box'], $e[1], $e))
        {
          $parts[] = '[not(@' . $e[1] . ')]';
        }
      }

      // 疑似セレクタを処理
      if (self::pregMatchDelete(self::$regex['pseudo'], $selector, $e))
      {
        switch ($e[1])
        {
          case 'first-child':
            $parts[] = '[not(preceding-sibling::*)]';
            break;
          case 'last-child':
            $parts[] = '[not(following-sibling::*)]';
            break;
          case 'nth-child':
            // CSS3 (一部)
            if (is_numeric($e[3]))
            {
              $parts[] = '[count(preceding-sibling::*) = ' . $e[3] . ' - 1]';
            } else if ($e[3] == 'odd') {
              $parts[] = '[count(preceding-sibling::*) mod 2 = 0]';
            } else if ($e[3] == 'even') {
              $parts[] = '[count(preceding-sibling::*) mod 2 = 1]';
            }
            break;
          case 'lang':
            $parts[] = '[@xml:lang="' . $e[3] . '" or starts-with(@xml:lang, "' . $e[3] . '-")]';
            break;
          default:
            break;
        }
      }

      // combinatorがあったらjointを切り変える。
      if (self::pregMatchDelete(self::$regex['combinator'], $selector, $e))
      {
        switch (trim($e[1]))
        {
          case '>':
            $joint = '/';
            break;
          case '+':
            $joint = '/following-sibling::*[1]/self::';
            break;
          case '~':
            // CSS3
            $joint = '/following-sibling::';
            break;
          default:
            // 少しキモイ。
            $joint = 'child sign';
            break;
        }
      }

      // commaがあったら、そこで評価式を分ける
      if (self::pregMatchDelete(self::$regex['comma'], $selector, $e))
      {
        $parts[] = ' | ';
        $parts[] = '//*';
      }
    }

    $return = implode('', $parts);

    return $return;
  }

  /**
   * 正規表現でマッチをしつつ、マッチ部分を削除
   */
  public static function pregMatchDelete($pattern, &$subject, &$matches)
  {
    $result = false;
    if (preg_match($pattern, $subject, $matches))
    {
      $subject = substr($subject, strlen($matches[0]));
      $result = true;
    }

    return $result;
  }

}
