<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * JSON出力
 *
 * @param array $values  値
 * @param array $options オプション
 *
 * @return BEAR_Ro
 */
function outputJson(
    $values,
    /* @noinspection PhpUnusedParameterInspection */
    array $options
) {
    $body = json_encode($values);
    $headers = ['X-BEAR-Output: JSON' => 'Content-Type: text/javascript+json; charset=utf-8'];
    $ro = BEAR::factory('BEAR_Ro');
    $ro->setBody($body);
    $ro->setHeaders($headers);

    return $ro;
}
