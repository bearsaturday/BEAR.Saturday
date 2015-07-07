<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Output
 * @author     Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright  2008-2015 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id: php.php 2485 2011-06-05 18:47:28Z akihito.koriyama@gmail.com $
 * @link       http://www.bear-project.net/
 */

/**
 * PHPシリアライズ変数出力
 *
 * @param array $values  値
 * @param array $options オプション
 *
 * @return BEAR_Ro
 */
function outputPhp(
    $values,
    /** @noinspection PhpUnusedParameterInspection */
    array $options
) {
    $body = serialize($values);
    $headers = array('X-BEAR-Output: PHP' => 'Content-Type: text/html; charset=utf-8');
    $ro = BEAR::factory('BEAR_Ro');
    $ro->setBody($body);
    $ro->setHeaders($headers);
    return $ro;
}
