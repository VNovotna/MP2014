<?php

namespace DB;

/**
 * 
 * @author viky
 */
class ServerRepository extends Repository {

    /**
     * @param int $serverId
     * @param string $name
     * @param string $path
     * @param string $executable
     * @return boolean
     * @throws PDOException
     */
    public function updateServerParams($serverId, $name, $path, $executable) {
        try {
            $this->getTable()->where('id', $serverId)->update(array(
                'name' => $name,
                'path' => $path,
                'executable' => $executable
            ));
            return TRUE;
        } catch (PDOException $e) {
            throw new PDOException;
        }
    }

    /**
     * @param int $serverId
     * @return \Nette\Database\Table\Selection
     * @throws PDOException
     */
    public function getRunParams($serverId) {
        try {
            return $this->getTable()->where('id', $serverId)->fetch();
        } catch (PDOException $e) {
            throw new PDOException;
        }
    }

    /**
     * return server path
     * @param int $serverId
     * @return string /path/to/server/
     */
    public function getPath($serverId) {
        return $this->getRunParams($serverId)->path;
    }

    /**
     * sets runtime has for running server identification
     * @param int $serverId
     * @param string $hash
     * @return boolean
     * @throws PDOException
     */
    public function setRuntimeHash($serverId, $hash) {
        try {
            $this->getTable()->where('id', $serverId)->update(array(
                'runhash' => $hash
            ));
            return TRUE;
        } catch (PDOException $e) {
            throw new PDOException;
        }
    }

    public function getRuntimeHash($serverId) {
        try {
            return $this->getTable()->where('id', $serverId)->fetch()->runhash;
        } catch (PDOException $e) {
            throw new PDOException;
        }
    }

    public function generateRuntimeHash() {
        return \Nette\Utils\Strings::random(10, 'A-Za-z');
    }

}