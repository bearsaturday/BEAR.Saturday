<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 *
 * @link       https://github.com/bearsaturday
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
    /* @noinspection PhpUnusedParameterInspection */
    array $options
) {
    $body = serialize($values);
    $headers = array('X-BEAR-Output: PHP' => 'Content-Type: text/html; charset=utf-8');
    $ro = BEAR::factory('BEAR_Ro');
    $ro->setBody($body);
    $ro->setHeaders($headers);

    return $ro;
}
