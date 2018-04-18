<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * Roコンテナ
 *
 * ArrayObjectのシリアラズでPHP5.2でのバクがあるためキャッシュのときに
 * RoではなくこのクラスをつかってシリアライズするためのRoコンテナ
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
