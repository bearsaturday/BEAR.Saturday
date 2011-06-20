<?php

class Net_Server_Driver_Multiprocess_Processor extends PHP_Fork {
    var $_server;

    function Net_Server_Driver_Multiprocess_Processor($id, $server)
    {
        $this->PHP_Fork("Processor-" . $id);
        $this->_server = &$server;
    } 

    function run()
    { 
        // now the callback object has a reference to the server obj into the parent
        // we need to set a reference to the server obj into the child...
        $this->_server->callbackObj->setServerReference($this->_server);

        while (true) {
            $readFDs = array();
            array_push($readFDs, $this->_server->initFD); 
            // fetch all clients that are awaiting connections
            for ($i = 0; $i < count($this->_server->clientFD); $i++) {
                if (isset($this->_server->clientFD[$i]))
                    array_push($readFDs, $this->_server->clientFD[$i]);
            } 
            // block and wait for data or new connection
            $ready = @socket_select($readFDs, $this->_server->null, $this->_server->null, null);

            if ($ready === false) {
                die("socket_select failed.");
            } 
            // check for new connection
            if (in_array($this->_server->initFD, $readFDs)) {
                $newClient = $this->acceptConnection($this->_server->initFD);
                $this->setVariable("threadWorking", true); 
                // check for maximum amount of connections
                /*
                if ($this->maxClients > 0) {
                    if ($this->clients > $this->maxClients) {
                        $this->_sendDebugMessage("Too many connections.");

                        if (method_exists($this->callbackObj, "onConnectionRefused")) {
                            $this->callbackObj->onConnectionRefused($newClient);
                        }

                        $this->closeConnection($newClient);
                    }
                } */

                if (--$ready <= 0) {
                    continue;
                } 
            } 
            // check all clients for incoming data
            for($i = 0; $i < count($this->_server->clientFD); $i++) {
                if (!isset($this->_server->clientFD[$i])) {
                    continue;
                } 

                if (in_array($this->_server->clientFD[$i], $readFDs)) {
                    $data = $this->_server->readFromSocket($i); 
                    // empty data => connection was closed
                    if (!$data) {
                        $this->_server->_sendDebugMessage("*** Connection closed by peer");
                        $this->_server->closeConnection($i);
                    } else {
                        $this->_server->_sendDebugMessage("Received " . trim($data) . " from " . $i);

                        if (method_exists($this->_server->callbackObj, "onReceiveData")) {
                            $this->_server->callbackObj->onReceiveData($i, $data);
                        } 
                    } 
                    $this->setVariable("threadWorking", false);
                } 
            } 
        } 
    } 

    function acceptConnection(&$socket)
    {
        for($i = 0 ; $i <= count($this->_server->clientFD); $i++) {
            if (!isset($this->_server->clientFD[$i]) || $this->_server->clientFD[$i] == null) {
                $this->_server->clientFD[$i] = socket_accept($socket);
                socket_setopt($this->_server->clientFD[$i], SOL_SOCKET, SO_REUSEADDR, 1);
                $peer_host = "";
                $peer_port = "";
                socket_getpeername($this->_server->clientFD[$i], $peer_host, $peer_port);
                $this->_server->clientInfo[$i] = array("host" => $peer_host,
                    "port" => $peer_port,
                    "connectOn" => time()
                    ); 
                // $this->clients++;
                $this->_server->_sendDebugMessage("New connection (" . $i . ") from " . $peer_host . " on port " . $peer_port);

                if (method_exists($this->_server->callbackObj, "onConnect")) {
                    $this->_server->callbackObj->onConnect($i);
                } 
                return $i;
            } 
        } 
    } 
} 
