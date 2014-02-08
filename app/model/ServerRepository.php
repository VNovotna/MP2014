<?php

namespace DB;

/**
 * ServeRepository is doing everthing around server table
 * @author viky
 */
class ServerRepository extends Repository {

    /**
     * @param int $serverId
     * @param string $name
     * @param string $path
     * @param string $executable
     * @return boolean
     * @throws \RuntimeException
     */
    public function updateServerParams($serverId, $name, $path, $executable) {
        try {
            $this->getTable()->where('id', $serverId)->update(array(
                'name' => $name,
                'path' => $path,
                'executable' => $executable
            ));
            return TRUE;
        } catch (\PDOException $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param int $serverId
     * @param string $executable
     * @return boolean
     * @throws \RuntimeException
     */
    public function setExecutable($serverId, $executable) {
        try {
            $this->getTable()->where('id', $serverId)->update(array(
                'executable' => $executable
            ));
            return TRUE;
        } catch (\PDOException $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param int $serverId
     * @return \Nette\Database\Table\Selection
     * @throws \RuntimeException
     */
    public function getRunParams($serverId) {
        try {
            return $this->getTable()->where('id', $serverId)->fetch();
        } catch (\PDOException $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode());
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
     * @throws \RuntimeException
     */
    public function setRuntimeHash($serverId, $hash) {
        try {
            $this->getTable()->where('id', $serverId)->update(array(
                'runhash' => $hash
            ));
            return TRUE;
        } catch (\PDOException $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * sets given runtime hash to NULL, use only when stoping server
     * @param type $hash
     * @return boolean TRUE on success
     * @throws \RuntimeException
     */
    public function removeRuntimeHash($hash) {
        try {
            $this->getTable()->where('runhash', $hash)->update(array(
                'runhash' => NULL
            ));
            return TRUE;
        } catch (\PDOException $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param int $serverId
     * @return string | NULL
     * @throws \RuntimeException
     */
    public function getRuntimeHash($serverId) {
        try {
            $hash = $this->getTable()->where('id', $serverId)->fetch();
            if ($hash == NULL) {
                return NULL;
            } else {
                return $hash->runhash;
            }
        } catch (\PDOException $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param int $user_id
     * @param string $name
     * @param string $path
     * @param string $executable
     * @param int $port port has to be free, use finFreePort method
     * @return int id of new server
     * @throws \RuntimeException
     */
    public function addServer($user_id, $name, $path, $executable, $port) {
        try {
            return $this->getTable()->insert(array(
                        'user_id' => $user_id,
                        'path' => $path,
                        'executable' => $executable,
                        'name' => $name,
                        'port' => $port
            ))->getPrimary();
        } catch (\PDOException $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @return int first free port number
     */
    public function findFreePort() {
        $ports = $this->getTable()->fetchPairs('id', 'port');
        $i = $ports[array_keys($ports)[0]];
        foreach ($ports as $port) {
            if ($i != $port) {
                return $i;
            }
            $i++;
        }
    }

    /**
     * check if user violated port number
     * @param int $serverId
     * @param int $port
     * @return boolean
     */
    public function isPortValid($serverId, $port) {
        $params = $this->getRunParams($serverId);
        if ($params->port == $port) {
            return TRUE;
        }
        return FALSE;
    }

    public function generateRuntimeHash() {
        return \Nette\Utils\Strings::random(10, 'A-Za-z');
    }

}
