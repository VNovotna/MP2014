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
        $filename = $date->format('Y-m-d_H-i');
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
        try {
            $phar = new PharData($path . 'backups/' . $filename . '.zip');
            $this->serverCmd->issueCommand('save-all', $hash);
            $this->serverCmd->issueCommand('save-off', $hash);
            $phar->buildFromDirectory($path . 'world/');
            $this->serverCmd->issueCommand('save-on', $hash);
            return TRUE;
        } catch (UnexpectedValueException $e) {
            echo $e->getTraceAsString();
            return FALSE;
        }
    }

    /**
     * Internal. Backup server when it's not running
     * @param string $path
     * @param string $filename
     * @return boolean
     */
    private function backupSwitchedOff($path, $filename) {
        try {
            $phar = new PharData($path . 'backups/' . $filename . '.zip');
            $phar->buildFromDirectory($path . 'world/');
            return TRUE;
        } catch (UnexpectedValueException $e) {
            echo $e->getTraceAsString();
            return FALSE;
        }
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
     * @param string $filename name of the backup file
     * @param string $runtimeHash if is server running
     * @param string $jar name of jar file to run, needed only on running server
     * @return boolean TRUE when successful
     */
    public function restore($path, $filename, $runtimeHash = NULL, $jar = NULL) {
        if (file_exists($path . 'backups/' . $filename)) {
            if ($runtimeHash !== NULL) {  //stop server
                $this->serverCmd->stopServer($runtimeHash);
            }
            //delete world/
            $this->removeDir($path . 'world/');
            //extract backup archive
            try {
                $archive = new PharData($path . 'backups/' . $filename);
                $archive->extractTo($path . 'world/', NULL, TRUE);
            } catch (UnexpectedValueException $e) {
                echo $e->getTraceAsString();
                return FALSE;
            }
            if ($runtimeHash !== NULL) {  //start server again 
                $this->serverCmd->startServer($path, $jar, $runtimeHash);
                return TRUE;
            }
            return TRUE;
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

    /**
     * remove all files in the dir
     * @param string $path to folder to wipe
     */
    public function removeDir($path) {
        $class_func = array(__CLASS__, __FUNCTION__);
        return is_file($path) ?
                @unlink($path) :
                array_map($class_func, glob($path . '/*')) == @rmdir($path);
    }

}
