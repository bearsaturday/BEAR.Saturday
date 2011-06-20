<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Author: Luca Mariano <luca.mariano@email.it>                         |
// +----------------------------------------------------------------------+
// $Id: remoteConsole.php
/**
 * This is a base class for a TCP based console listener; it manages by itself
 * the standard console operations:
 *
 * - help: generated from the options list given at console creation;
 * - quit: exit from the console;
 * - shutdown: request for server shutdown; close the listener itself and
 * calls (if exists) the class method shutdownActionPerformed().
 *
 * In order to use the class you must write your own class that extends remoteConsole;
 * into your constructor you must call the superclass constructor passing as args
 * at least an array of options that the console will support, the application name
 * (a random string if you've no idea...) and socket data (address & port to bind);
 * other options are avaible, see API for details.
 * Then you must implement into your class a method for each avaible option; this method
 * must have a name with the syntax: [choice]actionPerformed
 * When a client choose the option [choice], this method will be executed
 * (if exists),  and return string is sent back to the client
 *
 * (for example, if you have a menu like L -> list files in current dir
 * and you have defined the method LactionPerformed() this
 * method will be called)
 */
define('REMOTE_CONSOLE_MAXLINE', 1024); // how much to read from a socket at a time

class Net_Server_Driver_Multiprocess_remoteConsole extends PHP_Fork {
    /**
     * A list of all options avaible to clients; the format is
     * array('choice1'=>'description 1','choice2'=>'description 2')...
     *
     * @var array
     * @access private
     */
    var $_options;
    /**
     * The name of the application that uses this console
     * for reference only, no spaces
     *
     * @var string
     * @access private
     */
    var $_applicationName;
    /**
     * The IP address to bind
     *
     * @var integer
     * @access private
     */
    var $_address;
    /**
     * The port to bind
     *
     * @var integer
     * @access private
     */
    var $_port;
    /**
     * Listening queue
     *
     * @var integer
     * @access private
     */
    var $_listenq;
    /**
     * Max number of cuncurrent conenctions
     *
     * @var integer
     * @access private
     */
    var $_fd_set_size;
    /**
     * A list of IP address that are allowed to connect to the console
     * all other IP will be banned.
     *
     * @var array
     * @access private
     */
    var $_acl;

    /**
     * The master socket
     *
     * @var resource
     * @access private
     */
    var $_sock;
    /**
     * Array of client sockets in use
     *
     * @var array
     * @access private
     */
    var $_clientFD;
    /**
     * Array of client IP
     *
     * @var array
     * @access private
     */
    var $_remote_host;
    /**
     * Array of client ports
     *
     * @var array
     * @access private
     */
    var $_remote_port;
    /**
     * Console prompt
     *
     * @var string
     * @access private
     */
    var $_prompt = ">";

    function Net_Server_Driver_Multiprocess_remoteConsole($options, $applicationName, $address = "localhost", $port = "9999", $listenq = 100, $fd_set_size = 5, $acl = array("127.0.0.1"))
    {
  //      if (!is_array($options))
  //          die ("You should specify some options for your console!\n");

        $this->_options = $options;
        $this->_applicationName = $applicationName;
        $this->_address = $address;
        $this->_port = $port;
        $this->_listenq = $listenq;
        $this->_fd_set_size = $fd_set_size;
        $this->_acl = $acl;

        $this->PHP_Fork($this->_applicationName);
    }

    function run()
    {
        if (!$this->_openSocket()) {
            socket_close ($this->_sock);
            die ("Can't open listening socket\n");
        } else
            $this->_acceptCalls();
    }

    function _openSocket()
    {
        $ret = true;
        if (($this->_sock = socket_create (AF_INET, SOCK_STREAM, 0)) < 0) {
            echo "socket_create() fallito: motivo: " . socket_strerror ($this->_sock) . "\n";
            $ret = false;
        }
        socket_set_option($this->_sock, SOL_SOCKET, SO_REUSEADDR, 1);

        if (false == (@socket_bind ($this->_sock, $this->_address, $this->_port))) {
            echo "socket_bind() fallito: motivo: " . socket_strerror (socket_last_error($this->_sock)) . "\n";
            $ret = false;
        }

        if (($retcode = socket_listen ($this->_sock, $this->_listenq)) < 0) {
            echo "socket_listen() fallito: motivo: " . socket_strerror ($retcode) . "\n";
            $ret = false;
        }
        return $ret;
    }

    function _acceptCalls()
    {
        $maxi = -1;
        for ($i = 0; $i < $this->_fd_set_size; $i++)
        $this->_clientFD[$i] = null;

        while (true) {
            $rfds[0] = &$this->_sock;

            for ($i = 0; $i < $this->_fd_set_size; $i++) {
                if ($this->_clientFD[$i] != null)
                    $rfds[$i + 1] = $this->_clientFD[$i];
            }
            // block indefinitely until we receive a connection...
            $nready = socket_select($rfds, $null, $null, null);
            // if we have a new connection, stick it in the $client array,
            if (in_array($this->_sock, $rfds)) {
                //print "listenfd heard something, setting up new client\n";

                for ($i = 0; $i < $this->_fd_set_size; $i++) {
                    if ($this->_clientFD[$i] == null) {
                        $this->_clientFD[$i] = socket_accept($this->_sock);
                        socket_setopt($this->_clientFD[$i], SOL_SOCKET, SO_REUSEADDR, 1);
                        socket_getpeername($this->_clientFD[$i], $this->_remote_host[$i], $this->_remote_port[$i]);

                        if ($i == ($this->_fd_set_size - 1)) {
                            print ("too many clients, refusing {$this->_remote_host[$i]}\n");
                            socket_write($this->_clientFD[$i], "Sorry, too many clients" , 23);
                            $this->_closeClient($i);
                            break;
                        } else if (!in_array($this->_remote_host[$i], $this->_acl)) {
                            //print ("Refusing {$this->_remote_host[$i]} for ACL rules\n");
                            socket_write($this->_clientFD[$i], "Permission denied" , 17);
                            $this->_closeClient($i);
                            break;
                        }

                        //print "Accepted {$this->_remote_host[$i]}:{$this->_remote_port[$i]} as client[$i]\n";
                        $talkback = $this->_help();
                        socket_write($this->_clientFD[$i], $talkback , strlen($talkback));
                        break;
                    }
                }
                if ($i > $maxi)
                    $maxi = $i;

                if (--$nready <= 0)
                    continue;
            }
            // check the clients for incoming data.
            for ($i = 0; $i <= $maxi; $i++) {
                if ($this->_clientFD[$i] == null)
                    continue;

                if (in_array($this->_clientFD[$i], $rfds)) {
                    $n = trim(socket_read($this->_clientFD[$i], REMOTE_CONSOLE_MAXLINE));

                    if (!$n) {
                        $this->_closeClient($i);
                    } else {
                        // the request string is $n
                        // build the response and put in $talkback
                        if ($n == 'q') {
                            $this->_closeClient($i);
                            continue;
                            // print the help screen
                        } else if ($n == 'h') {
                            $talkback = $this->_help();
                            // quit the whole application
                        } else if ($n == 'x') {
                            $this->_stopApplication();
                            break;
                        } else {
                            // Here we manage all user-defined menu entries
                            // if a method exist with the name [choice]actionPerformed()
                            // this method will be executed and return string is sent
                            // back to the client
                            // (for example, if you have a menu like L -> list files in current dir
                            // and you have defined the method LactionPerformed() this
                            // method will be called)
                            $methodName = strtoupper($n) . "actionPerformed";
                            if (method_exists($this, $methodName)) {
                                $talkback = "\r\n" . $this->$methodName() . "\r\n" . $this->_prompt;
                            } else {
                                if (array_key_exists(strtoupper($n), $this->_options))
                                    $talkback = "Sorry, feature not yet implemented.\r\n" . $this->_prompt;
                                else
                                    $talkback = "\r\n" . $this->_prompt;
                            }
                        }
                        // send out $talkback to client
                        //print "From {$this->_remote_host[$i]}:{$this->_remote_port[$i]},   client[$i]: $n\n";

                        if ($this->_clientFD[$i] != null) {
                            socket_write($this->_clientFD[$i], $talkback , strlen($talkback));
                        }
                    }

                    if (--$nready <= 0)
                        break;
                }
            }
        }
        print(time() . " - You should never read this...\n");
        exit(1);
    }

    function _help()
    {
        $msg = "\r\n" . $this->_applicationName . " Remote Console\r\n";

        $msg .= str_repeat("-", (strlen($msg)-4)) . "\r\n";
        $msg .= "Avaible commands follows:\r\n\r\n";

        foreach($this->_options as $key => $option) {
            $msg .= strtoupper($key) . " -> " . $option . "\r\n";
        }
        $msg .= "H -> this help\r\n";
        $msg .= "Q -> quit console\r\n";
        $msg .= "X -> stop application (CAUTION!!!)\r\n\r\n";
        $msg .= $this->_prompt;

        return $msg;
    }

    function _closeClient($i)
    {
        //print "closing client[$i] ({$this->_remote_host[$i]}:{$this->_remote_port[$i]})\n";

        socket_close($this->_clientFD[$i]);
        $this->_clientFD[$i] = null;
        unset($this->_remote_host[$i]);
        unset($this->_remote_port[$i]);
    }

    function _stopApplication()
    {
        // call to application shutdown...
        if (method_exists($this, 'shutdownActionPerformed')) {
            $this->shutdownActionPerformed();
        }
        socket_close($this->_sock);
        $msg = "Remote console is going down!\n";
        for ($i = 0; $i < $this->_fd_set_size; $i++) {
            if ($this->_clientFD[$i] != null) {
                socket_write($this->_clientFD[$i], $msg, strlen($msg));
                socket_close($this->_clientFD[$i]);
            }
        }
        exit(0);
    }
}

