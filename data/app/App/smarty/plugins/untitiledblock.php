<?php
/**
 * App
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR.app
 * @subpackage Smarty
 * @author     $Author: koriyama@bear-project.net $ <username@example.com>
 * @version    SVN: Release: @package_version@ $Id:$ untitiledblock.php 688 2009-07-03 15:57:58Z koriyama@bear-project.net $
 * @ignore
 */

/**
 * Untitle block plugin
 *
 * @param string $params  パラメーター
 * @param string $content HTML
 * @param Smarty &$smarty &Smarty object
 * @param bool   &$repeat &$repeat 呼び出し回数
 *
 * @return string
 * @ignore
 */
function smarty_block_untitled($params, $content, &$smarty, &$repeat)
{
    return $content;
}
