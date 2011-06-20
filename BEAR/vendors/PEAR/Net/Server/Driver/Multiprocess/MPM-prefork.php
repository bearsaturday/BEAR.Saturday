<?php
declare (ticks = 1);
class Net_Server_Driver_Multiprocess_MPM extends PHP_Fork {

    var $_threadPool;
    var $_server;
    var $_numThreads;
    var $_startPool;
    var $_maxIdleTime;

    function Net_Server_Driver_Multiprocess_MPM(&$pool, &$server)
    {
        $this->PHP_Fork("Processor-3");
        $this->_server = &$server;

        $this->_numThreads = $this->_server->_numThreads;
        $this->_startPool = $this->_server->_startPool;
        $this->_maxIdleTime = $this->_server->_maxIdleTime;

        $this->_threadPool = &$pool;
    } 

    function run()
    { 

        // reset shared mem
        for ($i = 0; $i < count($this->_threadPool)-1; $i++) {
            $this->_threadPool[$i]->setVariable("threadWorking", false);
        } 
        // this global value is true until the stopAllProcesses() method
        // sets it to false before stopping the processes
        $GLOBALS['mpm_run'] = true; 
        // Dynamic pool regulation
        while ($GLOBALS['mpm_run']) {
             print "checking...";
            $working = 0;

            for ($i = 0; $i < count($this->_threadPool)-1; $i++) {
                if ($this->_threadPool[$i]->getVariable("threadWorking")) {
                    $working++;
                } 
            } 
            $nextIndex = count($this->_threadPool);

            $this->setVariable("workingProcesses", $working);
            $this->setVariable("runningProcesses", $nextIndex); 
            print($working . " clients connected, " . $nextIndex . " threads avaible" . "\n");
            if (($nextIndex - $working) <= 1 && $nextIndex < $this->_numThreads) {
                // let's see if there're few threads avaible...
                print("Starting new thread with id " . $nextIndex . "\n");

                $this->_threadPool[$nextIndex] = &new Net_Server_Driver_Multiprocess_Processor($nextIndex, $this->_server);
                $this->_threadPool[$nextIndex]->start();
                print("Started Processor " . $nextIndex . " with PID " . $this->_threadPool[$nextIndex]->getPid() . "\n");
                sleep(2);
            } else {

                for ($key = count($listener)-1; $key > $this->_startPool; $key--) {
                    $thread = $listener[$key];
                    if ($thread->getLastAlive() > $this->_maxIdleTime) {
                        // this thread is not used since _maxIdleTime; let's kill it...
                        $threadName = $thread->getName();
                        $this->_sendDebugMessage("killing " . $threadName . "...\n");
                        $thread->stop();
                        array_splice($listener, $key, 1);
                        $this->_sendDebugMessage("Now avaible: " . count($listener) . " threads...\n");
                    }
                }

            } 
            sleep(1);
        } 

    } 

    function getProcOverview()
    {
        return array('working' => $this->getVariable("workingProcesses"),
            'running' => $this->getVariable("runningProcesses"));
    } 

    function getProcList($params)
    {
        if ($this->_isChild) {
            $procList = array();
            foreach($this->_threadPool as $thread) {
                $procList[] = array($thread->getName(), $thread->getPid(), $thread->getVariable("threadWorking"));
            } 
            return $procList;
        } else return $this->register_callback_func(func_get_args(), __FUNCTION__);
    } 

    function stopAllProcesses()
    {
        if ($this->_isChild) {
            // this is needed otherwise the run() method will continue to check
            // for dead processes status raising a fatal exception.
            $GLOBALS['mpm_run'] = false;

            foreach($this->_threadPool as $thread) {
                print "Stopping " . $thread->getName() . "\n";
                $thread->stop();
            } 
            return true;
        } else return $this->register_callback_func(func_get_args(), __FUNCTION__);
    } 
} 

