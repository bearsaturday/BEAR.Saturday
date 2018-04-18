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
 * 変数画面出力
 *
 * @param array $values  値
 * @param array $options オプション
 *
 * @return BEAR_Ro
 */
function outputPrint(
    $values,
    /* @noinspection PhpUnusedParameterInspection */
    array $options
) {
    $body = print_a($values, 'return:1');
    $headers = array('X-BEAR-Output: PRINT' => 'Content-Type: text/html; charset=utf-8');
    $ro = BEAR::factory('BEAR_Ro');
    $ro->setBody($body);
    $ro->setHeaders($headers);

    return $ro;
}
