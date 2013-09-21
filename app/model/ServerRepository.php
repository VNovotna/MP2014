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
     * sets runtime has for running server identification
     * @param int $serverId
     * @param string $hash
     * @return boolean
     * @throws PDOException
     */
    public function setRuntimeHash($serverId, $hash) {
        try {
            $this->getTable()->where('id', $serverId)->update(array(
                'hash' => $hash
            ));
            return TRUE;
        } catch (PDOException $e) {
            throw new PDOException;
        }
    }
    public function generateRuntimeHash(){
        return \Nette\Utils\Strings::random();
    }

}