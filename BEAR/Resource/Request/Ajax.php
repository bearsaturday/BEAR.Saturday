<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * AJAXリソースリクエスト
 */
class BEAR_Resource_Request_Ajax extends BEAR_Base
{
    /**
     * JS取得
     *
     * @return string
     */
    public function getJs()
    {
        $requestId = md5(serialize($this->_config) . session_id());
        $params = array('key' => $requestId);
        $json = json_encode($params);
        $js = "<script type=\"text/javascript\">$(\"#{$requestId}\").ready(function(){ $(\"#{$requestId}\").load(\"/bear/r/\", $json); });</scprit>";

        return "<span id=\"{$requestId}\">*</span>" . $js;
    }
}
