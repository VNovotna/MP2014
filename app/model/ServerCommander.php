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
     * stops server, if there is more than one server $runtimeHash is necessary
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
        exec("ps ux | grep 'SCREEN -dmS $runtimeHash'", $output);
        if (count($output) == 3) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Backup world/ folder. Provide runtime hash when you want to save running server
     * @param string $path path to folder with minecraft
     * @param string $runtimeHash
     * @return boolean
     */
    public function backup($path, $runtimeHash) {
        if ($this->isServerRunning($runtimeHash)) {
            echo "saving running";
            $date = new Nette\DateTime();
            $filename = $date->format('Y-m-d_H:i');
            $phar = new PharData('/home/viky/mcs/backups/' . $filename . '.zip');
            $this->issueCommand('save-all', $runtimeHash);
            $this->issueCommand('save-off', $runtimeHash);
            $phar->buildFromDirectory('/home/viky/mcs/world');
            $this->issueCommand('save-on', $runtimeHash);
            return TRUE;
        } else {
            throw new Nette\NotImplementedException;
        }
    }

}
