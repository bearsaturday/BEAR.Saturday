<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Ro
 * @subpackage Cache
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id: Container.php 2486 2011-06-06 07:44:05Z koriyama@bear-project.net $
 * @link       http://www.bear-project.net/
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
 * @author     Akihito Koriyama <koriyama@bear-project.net>
 * @copyright  2008-2011 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id: Container.php 2486 2011-06-06 07:44:05Z koriyama@bear-project.net $
 * @link       http://www.bear-project.net/
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
