<?PHP
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 * Base class for all drivers
 *
 * PHP versions 4 and 5
 *
 * @category Networking
 * @package  Net_Server
 * @author   Stephan Schmidt <schst@php.net>
 * @author   Christian Weiske <cweiske@php.net>
 * @license  http://www.php.net/license PHP License
 * @link     http://pear.php.net/package/Net_Server
 */

/**
 * uses PEAR's error handling and the destructors
 */
require_once 'PEAR.php';

/**
 * Base class for all drivers
 *
 * This class provides methods, can be used by all
 * server implementations.
 *
 * @category Networking
 * @package  Net_Server
 * @author   Stephan Schmidt <schst@php.net>
 * @author   Christian Weiske <cweiske@php.net>
 * @license  http://www.php.net/license PHP License
 * @link     http://pear.php.net/package/Net_Server
 */
class Net_Server_Driver extends PEAR
{
    /**
     * Port to listen on
     *
     * @access private
     * @var integer
     */
    var $port = 10000;

    /**
     * Domain to bind to
     *
     * @access private
     * @var string
     */
    var $domain = "localhost";

    /**
     * The connection protocol:
     * AF_INET, AF_INET6, AF_UNIX
     *
     * @access private
     * @var integer
     */
    var $protocol = AF_INET;

    /**
     * All file descriptors are stored here
     *
     * @access private
     * @var array
     */
    var $clientFD = array();

    /**
     * Maximum amount of clients
     *
     * @access private
     * @var integer
     */
    var $maxClients = -1;

    /**
     * buffer size for socket_read
     * @access private
     * @var integer
     */
    var $readBufferSize = 128;

    /**
     * Wnd character for socket_read
     *
     * @access private
     * @var integer
     */
    var $readEndCharacter = "\n";

    /**
     * Maximum of backlog in queue
     *
     * @access private
     * @var integer
     */
    var $maxQueue = 500;

    /**
     * Debug mode
     *
     * @access private
     * @var boolean
     */
    var $_debug = true;

    /**
     * Debug mode, normally only text is needed,
     * as servers should not be run in a browser
     *
     * @access private
     * @var string
     */
    var $_debugMode = "text";

    /**
     * Debug destination (filename or stdout)
     *
     * @access private
     * @var string
     */
    var $_debugDest = "stdout";

    /**
     * Empty array, used for socket_select
     *
     * @access private
     * @var array
     */
    var $null = array();

    /**
     * Needed to store client information
     *
     * @access private
     * @var array
     */
    var $clientInfo = array();

    /**
     * Part of the string that has been read via socket_read
     * but came after the readEndCharacter char.
     *
     * This can happen if the server gets hammered with > 100 requests
     * per second - multiple requests are delivered on the same connection
     * then. We need to supply a way to split the single packages, and
     * _readLeftOver is the solution.
     *
     * @access private
     * @var string
     */
    var $_readLeftOver = null;



    /**
     * constructor _MUST_ not be called directly
     *
     * Please use the Net_Server::create() method instead
     * that can be called statically and will return a server
     * of the specified type.
     *
     * @param string  $domain   Domain to bind to
     * @param integer $port     Port to listen to
     * @param integer $protocol Protocol type: AF_INET (default), AF_INET6, AF_UNIX
     *
     * @see Net_Server::create()
     *
     * @access   private
     */
    function Net_Server_Driver($domain = "localhost", $port = 10000, $protocol = AF_INET)
    {
        $this->PEAR();

        $this->domain   = $domain;
        $this->port     = $port;
        $this->protocol = (int)$protocol;
    }



    /**
     * destructor
     *
     * Will shutdown the server if the script is abborted.
     *
     * @return void
     *
     * @access private
     */
    function _Net_Server_Driver()
    {
        $this->shutdown();
    }



    /**
     * Set debug mode
     *
     * Debug information can either be written
     * to a logfile or standard out (= the console).
     *
     * To disable debugging, pass false as first parameter.
     *
     * @param mixed  $debug [text|html|false]
     * @param string $dest  Destination of debug message ("stdout" to output or
     *                       filename if log should be written)
     *
     * @return void
     *
     * @access public
     */
    function setDebugMode($debug, $dest = "stdout")
    {
        if ($debug === false) {
            $this->_debug = false;
            return true;
        }

        $this->_debug     = true;
        $this->_debugMode = $debug;
        $this->_debugDest = $dest;
    }



    /**
     * Read from a socket
     *
     * @param integer $clientId Internal id of the client to read from
     *
     * @return string|boolean $data Data that was read
     *
     * @access   private
     */
    function readFromSocket($clientId = 0)
    {
        // start with empty string
        $data = '';

        // There are problems when readEndCharacter is longer than
        // readBufferSize - endings won't be detected.
        $lookInData = strlen($this->readEndCharacter) > $this->readBufferSize;
        if ($lookInData) {
            $extraDataLength = strlen($this->readEndCharacter) - $this->readBufferSize;
        }

        // read data from socket
        while (true) {
            if ($this->_readLeftOver == null) {
                $buf = socket_read(
                    $this->clientFD[$clientId], $this->readBufferSize
                );
            } else {
                $buf                 = $this->_readLeftOver;
                $this->_readLeftOver = null;
            }

            if ($this->readEndCharacter != null) {
                if (strlen($buf) == 0) {
                    break;
                }

                if (!$lookInData) {
                    $posEndChar = strpos($buf, $this->readEndCharacter);
                } else {
                    $posEndChar = strpos(substr($data, - $extraDataLength)
                        . $buf, $this->readEndCharacter);
                }
                if ($posEndChar === false) {
                    $data .= $buf;
                } else {
                    $posEndChar += strlen($this->readEndCharacter);
                    $data       .= substr($buf, 0, $posEndChar);
                    if ($posEndChar < strlen($buf)) {
                        $this->_readLeftOver = substr($buf, $posEndChar);
                    }
                    break;
                }
            } else {
                /**
                 * readEndCharacter is set to null => don't loop, just return
                 */
                $data .= $buf;
                break;
            }
        }

        if ($buf === false) {
            $this->_sendDebugMessage(
                'Could not read from client ' . $clientId . ' ('
                . $this->getLastSocketError($this->clientFD[$clientId])
                . ').');
            return false;
        }
        if ((string)$data === '') {
            return false;
        }
        return $data;
    }



    /**
     * send a debug message
     *
     * Debug messages will be either sent to standard out
     * or written to a logfile, depending on debug settings.
     *
     * @param string $msg Message to debug
     *
     * @return void
     *
     * @see  setDebugMode()
     * @todo remove underscore from method name as the method now is public.
     *
     * @access public
     */
    function _sendDebugMessage($msg)
    {
        if (!$this->_debug) {
            return false;
        }

        $msg = date('Y-m-d H:i:s', time()) . ' ' . $msg;

        switch ($this->_debugMode) {
        case 'text':
            $msg = $msg."\n";
            break;
        case 'html':
            $msg = htmlspecialchars($msg) . "<br />\n";
            break;
        }

        if ($this->_debugDest == 'stdout' || empty($this->_debugDest)) {
            echo $msg;
            flush();
            return true;
        }

        error_log($msg, 3, $this->_debugDest);
        return true;
    }



    /**
     * register a callback object
     *
     * The callback object is the actual server,
     * that contains the logic to process the events
     * triggered by the driver class.
     *
     * The best way to create a callback object is to
     * extend the Net_Server_Handler class.
     *
     * @param object &$object callback object
     *
     * @return void
     *
     * @see Net_Server_Handler
     *
     * @access public
     */
    function setCallbackObject(&$object)
    {
        $this->callbackObj = &$object;
        if (method_exists($this->callbackObj, 'setServerReference')) {
            $this->callbackObj->setServerReference($this);
        }
    }



    /**
     * return string for last socket error
     *
     * @param resource &$fd Socket to get last error from
     *
     * @return string $error Last error
     *
     * @access   public
     */
    function getLastSocketError(&$fd)
    {
        if (!is_resource($fd)) {
            return '';
        }
        $lastError = socket_last_error($fd);
        return 'Msg: ' . socket_strerror($lastError) . ' / Code: '.$lastError;
    }



    /**
     * Sets the readEndCharacter setting.
     *
     * @param string $readEndCharacter The new end character
     *
     * @return void
     *
     * @access public
     */
    function setEndCharacter($readEndCharacter)
    {
        $this->readEndCharacter = $readEndCharacter;
    }



    /**
     * Returns the readEndCharacter setting.
     *
     * @return string The readEndCharacter setting
     *
     * @access public
     */
    function getEndCharacter()
    {
        return $this->readEndCharacter;
    }

}
?>
