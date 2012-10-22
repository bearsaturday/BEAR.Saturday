<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Resource
 * @subpackage Output
 * @author     Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright  2008 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: $Id: ajax.php 889 2009-09-16 00:22:54Z koriyama@users.sourceforge.jp $
 * @link       http://api.bear-project.net/BEAR_Resource/BEAR_Resource.html
 */
/**
 * JSON出力
 *
 * @param array $values 無効
 * @param array $options 無効
 *
 * @return BEAR_Ro
 */
function outputAjax($values, array $options)
{
    $ajax = BEAR::get('BEAR_Page_Ajax');
    $values = $ajax->getAjaxValues();
    $body = json_encode($values);
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