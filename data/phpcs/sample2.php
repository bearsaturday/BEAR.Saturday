<?php
/**
 * App
 *
 * @category BEAR
 * @package  {@app@}
 * @author   $Author: $ <username@example.com>
 * @license  {@app_license@} http://www.example.com/
 * @version  $Id: sample2.php 2453 2011-05-31 13:42:18Z koriyama@bear-project.net $
 * @link     http://www.example.com/
 */

/**
 * App
 *
 * @category BEAR
 * @package  {@app@}
 * @author   $Author: $ <username@example.com>
 * @license  {@app_license@} http://www.example.com/license
 * @version  $Id: sample2.php 2453 2011-05-31 13:42:18Z koriyama@bear-project.net $
 * @link     http://www.example.com/api
 */
class A {

    /**
     * @param int $hop
     * @param string  $step
     */
    public function funcParamInvalid($hop, $step, $jump)
    {
    }

    /**
     * This function should be valid
     *
     * @param int $hop
     * @param string $step
     * @param string $jump
     *
     * @return void
     */
    public function funcParamValid($hop, $step, $jump)
    {
    }
}