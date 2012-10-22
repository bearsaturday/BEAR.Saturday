<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Output
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id: ajax.php 2485 2011-06-05 18:47:28Z koriyama@bear-project.net $
 * @link      http://www.bear-project.net/
 */

/**
 * JSON出力
 *
 * @param array $values  値
 * @param array $options オプション
 *
 * @return BEAR_Ro
 */
function outputAjax(
    /** @noinspection PhpUnusedParameterInspection */
    $values,
    /** @noinspection PhpUnusedParameterInspection */
    array $options)
{
    $ajax = BEAR::get('BEAR_Page_Ajax');
    $values = $ajax->getAjaxValues();
    //    $body = json_encode($values);
    // Services_JSONの方が信頼性が高いため採用
    $json = new Services_JSON();
    $body = $json->encode($values);
    $log = BEAR::dependency('BEAR_Log');
    /* @var $log BEAR_Log */
    $log->log('AJAX', $values);
    $headers = array('X-BEAR-Output: AJAX' => 'Content-Type: text/javascript+json; charset=utf-8');
    $ro = BEAR::factory('BEAR_Ro');
    /* @var $ro BEAR_Ro */
    $ro->setBody($body);
    $ro->setHeaders($headers);
    return $ro;
}