<?php

/**
 * Description of BackupModel
 *
 * @author viky
 */
class BackupModel extends Nette\Object {

    /** @var ServerCommander */
    private $serverCmd;

    public function __construct(ServerCommander $serverCommander) {
        $this->serverCmd = $serverCommander;
    }

    /**
     * Backup world/ folder. Provide runtime hash when you want to save running server
     * @param string $path path to folder with minecraft
     * @param string $runtimeHash
     * @return boolean
     */
    public function backup($path, $runtimeHash = NULL) {
        $date = new Nette\DateTime();
        $filename = $date->format('Y-m-d_H:i');
        if ($this->serverCmd->isServerRunning($runtimeHash)) {
            return $this->backupRunning($path, $runtimeHash, $filename);
        } else {
            return $this->backupSwitchedOff($path, $filename);
        }
    }

    /**
     * Internal. Backup running server
     * @param string $path
     * @param string $hash runtimeHash
     * @param string $filename
     * @return boolean
     */
    private function backupRunning($path, $hash, $filename) {
        $phar = new PharData($path . 'backups/' . $filename . '.zip');
        $this->serverCmd->issueCommand('save-all', $hash);
        $this->serverCmd->issueCommand('save-off', $hash);
        $phar->buildFromDirectory($path . 'world/');
        $this->serverCmd->issueCommand('save-on', $hash);
        return TRUE;
    }

    /**
     * Internal. Backup server when it's not running
     * @param string $path
     * @param string $filename
     * @return boolean
     */
    private function backupSwitchedOff($path, $filename) {
        $phar = new PharData($path . 'backups/' . $filename . '.zip');
        $phar->buildFromDirectory($path . 'world/');
        return TRUE;
    }

}
