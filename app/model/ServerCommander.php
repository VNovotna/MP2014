<?php

/**
 * ServerCommander is able to start/stop server and pass commands
 * 
 * @author viky
 */
class ServerCommander extends \Nette\Object {

    /** @var \DB\ServerRepository */
    private $serverRepo;

    public function __construct(\DB\ServerRepository $serverRepo) {
        $this->serverRepo = $serverRepo;
    }

    /**
     * forward any command on server (return without delay)
     * @param string $command
     * @param string $runtimeHash
     * @return array usually empty if command was successfull
     */
    public function issueCommand($command, $runtimeHash) {
        $exec = 'screen -S ' . $runtimeHash . ' -p 0 -X stuff "`printf "' . $command . '\r"`"';
        exec($exec, $output);
        return $output;
    }

    /**
     * starts screen with name $runtimeHash and server runnig inside
     * @param string $jarPath absolute path to server folder
     * @param string $jarName name of server .jar
     * @param string $runtimeHash hash that indentifies server
     * @return array usually empty array if command was successfull
     * @throws \Nette\InvalidStateException
     */
    public function startServer($jarPath, $jarName, $runtimeHash) {
        $lastEdit = filectime($jarPath . 'logs/latest.log');
        if ($this->isServerRunning($runtimeHash)) {
            throw new \Nette\InvalidStateException;
        } else {     
            $exec = './../libs/start.sh ' . $jarPath . ' ' . $jarName . ' ' . $runtimeHash;
            exec($exec, $output);
            if ($this->isServerRunning($runtimeHash)) {
                $this->waitUntilFileIsChanged($jarPath . 'logs/latest.log', $lastEdit);
                return $output;
            } else {
                return array('something somewhere went terribly wrong');
            }
        }
    }
    /**
     * blocks for 20 seconds or until file in $filePath is modified
     * @param string $filePath
     * @param int $lastEdit unix timestamp
     */
    private function waitUntilFileIsChanged($filePath, $lastEdit) {
        for ($i = 0; $i < 40; $i++) {
            clearstatcache();
            if ($lastEdit < filectime($filePath)) {
                break;
            } else {
                usleep(500000);
            }
        }
    }

    /**
     * stops server and delete runtime hash 
     * @param string $runtimeHash
     * @return array usually empty if command was successfull
     */
    public function stopServer($runtimeHash) {
        $output = $this->issueCommand('stop', $runtimeHash);
        $this->serverRepo->removeRuntimeHash($runtimeHash);
        sleep(1);
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
        if (preg_match("#SCREEN -dmS " . $runtimeHash . " java #", $outString)) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * check if given port is free (black magic)
     * @param int $port
     * @return boolean
     */
    public static function isPortFree($port) {
        exec('./../libs/freePort.sh ' . $port, $output);
        if ($output[0] != $port) {
            return FALSE;
        }
        return TRUE;
    }

}
