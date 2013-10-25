<?php

/**
 * ServerCommander is able to start/stop server, pass commands and backup server
 * 
 * @author viky
 */
class ServerCommander extends \Nette\Object {

    /**
     * forward any command on server
     * @param string $command
     * @param string $runtimeHash default 'mcs', usefull with more than one server
     * @return array usually empty if command was successfull
     */
    public function issueCommand($command, $runtimeHash) {
        $exec = 'screen -S ' . $runtimeHash . ' -p 0 -X stuff `printf "' . $command . '\r"`';
        exec($exec, $output);
        return $output;
    }

    /**
     * starts screen with name $runtimeHash and server runnig inside
     * @param string $jarPath absolute path to server folder
     * @param string $jarName name of server .jar
     * @param string $runtimeHash default 'mcs', usefull with more than one server
     * @return array usually empty array if command was successfull
     * @throws \Nette\InvalidStateException
     */
    public function startServer($jarPath, $jarName, $runtimeHash) {
        //should check if screen with specified name is not running already
        if ($this->isServerRunning($runtimeHash)) {
            throw new \Nette\InvalidStateException;
        } else {
            $exec = './../libs/start.sh ' . $jarPath . ' ' . $jarName . ' ' . $runtimeHash;
            exec($exec, $output);
            if ($this->isServerRunning($runtimeHash)) {
                return $output;
            } else {
                return array('something somewhere went terribly wrong');
            }
        }
    }

    /**
     * stops server
     * @param string $runtimeHash
     * @return array usually empty if command was successfull
     */
    public function stopServer($runtimeHash) {
        $output = $this->issueCommand('stop', $runtimeHash);
        return $output;
    }

    /**
     * further testing required
     * @param string $runtimeHash
     * @return boolean
     */
    public function isServerRunning($runtimeHash) {
        exec("ps ax | grep 'SCREEN -dmS $runtimeHash'", $output);
        $outString = implode(" ", $output);
        if(preg_match("#SCREEN -dmS ".$runtimeHash." java #", $outString)){
            return TRUE;
        }
        return FALSE;
    }
}