<?php
/**
 * Originally coded by the author Stephan Schmidt
 * and committed to PEAR repository.
 *
 * Deep modifications was been made since that day...
 * Into PEAR you can actually found the base class (Server.php)
 * the handler base class (Handler.php) and the sequential driver (Sequential.php)
 *
 * I developed both multiprocess driver and Multi Processing Modules (actually only preforking)
 * specifically for phplet project.
 * Hope this stuff can be committed to PEAR one day!
 *
 * @author Stephan Schmidt <schst@php.net>
 */
require_once 'PEAR.php';
require_once 'PHP/Fork.php';
require_once 'Net/Server/Driver.php';
require_once 'Net/Server/Driver/Multiprocess/remoteConsole.php';
require_once 'Net/Server/Driver/Multiprocess/Processor.php';

class Net_Server_Multiprocess extends Net_Server_Driver {
    var $_numThreads;
    var $_startPool;
    var $_maxIdleTime;
    var $_threadPool;
    var $_mpm;
    var $_console;

    var $_mpmObj;
    var $_consoleObj;

    /**
     * set maximum amount of simultaneous connections
     * that's to say the maximum number of listener processes
     * that this server can handle
     *
     * @access public
     * @param int $maxClients
     */
    function setMaxClients($maxClients)
    {
        $this->_numThreads = $maxClients;
    }

    /**
     * set the initial number of listener processes
     *
     * @access public
     * @param int $dim
     */
    function setStartPool($dim)
    {
        $this->_startPool = $dim;
    }

    /**
     * set the maximun time a process can be sleeping
     * (without serving any request) before MPM kill it
     * NOT USED AT THIS MOMENT
     *
     * @access public
     * @param int $secs
     */
    function setMaxIdle($secs)
    {
        $this->_maxIdleTime = $secs;
    }

    /**
     * set the MPM layout for this server
     * (only preforkg avaible for now)
     *
     * @access public
     * @param string $mpm
     */
    function setMPM($mpm)
    {
        $this->_mpm = $mpm;
    }

    /**
     * set the console class for this server
     *
     * @access public
     * @param string $classname
     */
    function setConsole($classname)
    {
        $this->_console = $classname;
    }

    /**
     * start the server
     *
     * @access public
     */
    function start()
    {
        if (!function_exists('pcntl_fork')) {
            die ('Needs pcntl extension to fork processes.');
        }

        $this->initFD = @socket_create($this->protocol, SOCK_STREAM, 0);
        if (!$this->initFD) {
            die ("Could not create socket.");
        }
        // adress may be reused
        socket_setopt($this->initFD, SOL_SOCKET, SO_REUSEADDR, 1);
        // bind the socket
        if (!@socket_bind($this->initFD, $this->domain, $this->port)) {
            $error = $this->getLastSocketError($this->initFd);
            @socket_close($this->initFD);
            die ("Could not bind socket to " . $this->domain . " on port " . $this->port . " (" . $error . ").");
        }
        // listen on selected port
        if (!@socket_listen($this->initFD, $this->maxQueue)) {
            $error = $this->getLastSocketError($this->initFd);
            @socket_close($this->initFD);
            die ("Could not listen (" . $error . ").");
        }

        $this->_sendDebugMessage("Listening on port " . $this->port . ". Server started at " . date("H:i:s", time()));

        if (method_exists($this->callbackObj, "onStart")) {
            $this->callbackObj->onStart();
        }

        $this->_setDefaultOptions();

        $mpmDriverFile = 'Net/Server/Driver/Multiprocess/MPM-' . $this->_mpm . '.php';

        if (!@include_once $mpmDriverFile) {
            die ('Unknown MPM ' . $mpmDriverFile);
        }
        // starting pool of processors...
        for ($i = 0; $i < $this->_startPool; $i++) {
            $this->_threadPool[$i] = & new Net_Server_Thread_Processor($i, $this);
            $this->_threadPool[$i]->start();

            print("Started Processor " . $i . " with PID " . $this->_threadPool[$i]->getPid() . "\n");
        }
        // Multi Process Manager
        $this->_mpmObj = & new MPM($this->_threadPool, $this);
        $this->_mpmObj->start();
        print("Started MPM with PID " . $this->_mpmObj->getPid() . "\n");
        // destroy the listening primar socket, it's unuseful at this point
        @socket_close($this->initFD);
        // remote console thread
        $consoleClassName = $this->_console;
        if (!@include_once ('Net/Server/Driver/Multiprocess/'.$consoleClassName.'.php')) {
            die ('Unknown console ' . $consoleClassName);
        }
        $this->_consoleObj = & new $consoleClassName($this);
        $this->_consoleObj->start();
        print("Started remote console with PID " . $this->_consoleObj->getPid() . "\n");
    }

    /**
     * check, whether a client is still connected
     *
     * @access public
     * @param integer $id client id
     * @return boolean $connected  true if client is connected, false otherwise
     */
    function isConnected($i)
    {
        if (is_resource($this->clientFD[$i])) {
            return true;
        }
    }

    /**
     * get current amount of clients
     *
     * @access public
     * @return int $clients    amount of clients
     */
    function getClients()
    {
    	$overview = $this->_mpmObj->getProcOverview();
        return $overview['working'];
    }

    /**
     * send data to a client
     *
     * @access public
     * @param int $clientId ID of the client
     * @param string $data data to send
     * @param boolean $debugData flag to indicate whether data that is written to socket should also be sent as debug message
     */
    function sendData($clientId, $data, $debugData = true)
    {
    //print_r ($this);
        if (!isset($this->clientFD[$clientId]) || $this->clientFD[$clientId] == null) {
            
            return $this->raiseError("Client $clientId does not exist. (193)");
        }

        if ($debugData) {
            $this->_sendDebugMessage("sending: \"" . $data . "\" to: $clientId");
        }
        if (!@socket_write($this->clientFD[$clientId], $data)) {
            $this->_sendDebugMessage("Could not write '" . $data . "' client " . $clientId . " (" . $this->getLastSocketError($this->clientFD[$clientId]) . ").");
        }
    }

    /**
     * send data to all clients
     *
     * @access public
     * @param string $data data to send
     * @param array $exclude client ids to exclude
     */
    function broadcastData($data, $exclude = array())
    {
        $this->sendData($data);
    }

    /**
     * get current information about a client
     *
     * @access public
     * @param int $i ID of the client
     * @return array $info        information about the client
     */
    function getClientInfo($i)
    {
        if (!isset($this->clientFD[$i]) || $this->clientFD[$i] == null) {
            return $this->raiseError("Client does not exist.(226)");
        }
        return $this->clientInfo[$i];
    }

    /**
     * close connection to a client
     *
     * @access public
     * @param int $i internal ID of the client
     */
    function closeConnection($i = 0)
    {
        if (!isset($this->clientFD[$i])) {
            return $this->raiseError("Connection already has been closed.");
        }

        if (method_exists($this->callbackObj, "onClose")) {
            $this->callbackObj->onClose($i);
        }

        $this->_sendDebugMessage("Closed connection from " . $this->clientInfo[$i]["host"] . " on port " . $this->clientInfo[$i]["port"]);

        @socket_close($this->clientFD[$i]);
        $this->clientFD[$i] = null;
        unset($this->clientInfo[$i]);
    }

    /**
     * Shutdown server
     * ---------------------------- ATTENTION -------------------------------------
     * On multiprocess layouts stopping the server ==> stopping all processes
     * that are listening on server port; we generally can't do this operation
     * from the same server instance, because it only knows of the existence of processes
     * that it forked at start(). This start pool is equivalent to running pool in every
     * moment only if we're running the MPM-static.
     * With more complex MPM like the pre-forking one the process responsible for forking
     * new listeners at run-time  is MPM, so we must make a call to it in order
     * to stop all forked processes.
     * Of course if we have a running MPM and a running console, stopping the server
     * requires stopping them too.
     *
     * @access public
     */
    function shutDown()
    {
        if (method_exists($this->callbackObj, "onShutdown")) {
            $this->callbackObj->onShutdown();
        }
        // this always should return true, we use PHP_FORK_RETURN_METHOD because
        // we want to wait until all processes are really stopped...
        if ($this->_mpmObj->stopAllProcesses(array(''), PHP_FORK_RETURN_METHOD)) {
            print "Stopping " . $this->_mpmObj->getName() . "\n";
            $this->_mpmObj->stop();

            print "Exiting from console...\n";
        } else {
            print "Error stopping mpm\n";
        }
        $this->closeConnection();
        $this->_consoleObj->stop();
    }

    function raiseError($msg)
    {
        $this->_sendDebugMessage("ERROR: " . $msg);
    }

    /**
     * set some default options that are needed to run
     * a multiprocess server; usually we override this
     * with custom values.
     *
     * @access private
     */
        function _setDefaultOptions()
        {
        if (!isset($this->_numThreads))
            $this->_numThreads = 10;

        if (!isset($this->_startPool))
            $this->_startPool = 2;

        if (!isset($this->_maxIdleTime))
            $this->_maxIdleTime = 60;

        if (!isset($this->_mpm))
            $this->_mpm = "prefork";

        if (!isset($this->_console))
        	$this->_console = "remoteConsole";

        }

}

?>
