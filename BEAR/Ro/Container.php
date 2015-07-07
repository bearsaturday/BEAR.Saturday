<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Ro
 * @subpackage Cache
 * @author     Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright  2008-2015 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id: Container.php 2486 2011-06-06 07:44:05Z akihito.koriyama@gmail.com $
 * @link       https://github.com/bearsaturday
 */

/**
 * Roコンテナ
 *
 * ArrayObjectのシリアラズでPHP5.2でのバクがあるためキャッシュのときに
 * RoではなくこのクラスをつかってシリアライズするためのRoコンテナ
 *
 * @category   BEAR
 * @package    BEAR_Ro
 * @subpackage Cache
 * @author     Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright  2008-2015 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id: Container.php 2486 2011-06-06 07:44:05Z akihito.koriyama@gmail.com $
 * @link       https://github.com/bearsaturday
 */
class BEAR_Ro_Container
{
    /**
     * Code
     *
     * @var int
     */
    public $code;

    /**
     * Headers
     *
     * @var array
     */
    public $header;

    /**
     * Body
     *
     * @var string
     */
    public $body;

    /**
     * Links
     *
     * @var array
     */
    public $links;

    /**
     * HTML
     *
     * @var string
     */
    public $html;

    /**
     * Constructor
     *
     * @param BEAR_Ro $ro
     */
    public function __construct(BEAR_Ro $ro)
    {
        /* @var $ro BEAR_Ro */
        $this->code = $ro->getCode();
        $this->header = $ro->getHeaders();
        $this->body = $ro->getBody();
        $this->links = $ro->getLinks();
        $this->html = $ro->getHtml();
    }
}
