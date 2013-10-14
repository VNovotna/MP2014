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

    /**
     * Return array of existing backup files 
     * @param string $path
     * @return array of SplFileInfo 
     */
    public function getBackups($path) {
        $files = array();
        $finder = Finder::findFiles('*.zip')->in($path . 'backups/');
        foreach ($finder->orderByName() as $file) {
            $files[] = $file->getBasename();
        }
        return array_reverse($files);
    }

    /**
     * stops server, delete world/, restore backup, and start server
     * stoping and starting server will be omitted if the server is down
     * @param string $path to minecraft folder ('backup/' will be added automaticaly)
     * @param string $file name of the backup file
     * @param string $runtimeHash if is server running
     * @return boolean TRUE when successful
     */
    public function restore($path, $file, $runtimeHash = NULL) {
        if (file_exists($path . 'backups/' . $file)) {
            if ($runtimeHash !== NULL) {  //stop server
                //$this->serverCmd->stopServer($runtimeHash);
            }
            //smazat world/
            //rozbalit zálohu
            //nastartovat

            return FALSE;
        } else {//no such file
            return FALSE;
        }
    }

    /**
     * remove given file, intended only for deleting backup files
     * @param string $path to minecraft folder ('backup/' will be added automaticaly)
     * @param string $file name of the backup file
     * @return boolean TRUE when successful
     */
    public function removeFile($path, $file) {
        if (file_exists($path . 'backups/' . $file)) {
            exec('rm ' . $path . 'backups/' . $file, $output);
            if ($output === array()) {
                return TRUE;
            } else {
                return $output;
            }
        } else { //no such file
            return FALSE;
        }
    }

}
