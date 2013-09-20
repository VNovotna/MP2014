<?php

/**
 * ServerCommander is able to start/stop server, passes commands and read logs.
 * (Or it should be able of doing this)
 * @author viky
 */
class ServerCommander extends \Nette\Object {

    /**
     * forward any command on server
     * @param string $command
     * @param string $screenName default 'mcs', usefull with more than one server
     * @return array usually empty if command was successfull
     */
    public function issueCommand($command, $screenName = 'mcs') {
        $output = array();
        $exec = 'screen -S ' . $screenName . ' -p 0 -X stuff `printf "' . $command . '\r"`';
        exec($exec, $output);
        return $output;
    }

    /**
     * starts screen with name $screenName and server runnig inside
     * @param string $startupScript absolute path to startup script
     * @param string $screenName default 'mcs', usefull with more than one server
     * @return array usually empty if command was successfull
     */
    public function startServer($startupScript, $screenName = 'mcs') {
        //should generate startup script
        //should check if screen with specified name is not running already
        $output = array();
        exec('screen -dmS ' . $screenName . ' ' . $startupScript, $output);
        return $output;
    }

    /**
     * stops server, if there is more than one server $screenName is necessary
     * @param string $screenName
     * @return array usually empty if command was successfull
     */
    public function stopServer($screenName = 'mcs') {
        $output = $this->issueCommand('stop', $screenName);
        return $output;
    }

}
