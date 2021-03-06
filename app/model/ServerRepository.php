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
     * @throws \RuntimeException|\Nette\InvalidArgumentException
     */
    public function updateServerParams($serverId, $name, $path, $executable) {
        $this->checkPathValidity($path);
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
     * @return Nette\Database\Table\ActiveRow
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
     * @param string $path must end on / 
     * @param string $executable
     * @param int $port port has to be free, use finFreePort method
     * @param string $serverFolder put name of the server folder here
     * @return int id of new server
     * @throws \RuntimeException|\Nette\InvalidArgumentException
     */
    public function addServer($user_id, $name, $path, $executable, $port, $serverFolder = NULL) {
        $this->checkPathValidity($path);
        if ($serverFolder != NULL) {
            $path = $this->createFolder($path, $serverFolder, $port);
        } else {
            $this->createServerProps($path, $port);
        }
        try {
            $id = $this->getTable()->insert(array(
                        'user_id' => $user_id,
                        'path' => $path,
                        'executable' => $executable,
                        'name' => $name,
                        'port' => $port
                    ))->getPrimary();
            $this->getTable('permission')->insert(array(
                'user_id' => $user_id,
                'server_id' => $id,
                'role' => 'owner'
            ));
            return $id;
        } catch (\PDOException $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * it will remove server from database
     * @param int $serverId
     * @return boolean TRUE on success, false otherwise
     * @throws \RuntimeException
     */
    public function removeServer($serverId) {
        try {
            $this->getTable()->where('id', $serverId)->delete();
            $this->getTable('permission')->where(array('server_id' => $serverId))->delete();
            return TRUE;
        } catch (\PDOException $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param string $path
     * @param string $folderName 
     * @param int $port
     * @return string with new path
     * @throws \Nette\InvalidArgumentException
     */
    private function createFolder($path, $folderName, $port) {
        chdir($path);
        if (!file_exists($folderName) and !mkdir($folderName, 0771)) {
            throw new \Nette\InvalidArgumentException('Path is invalid or it is unacessible');
        }
        $path = $path . $folderName;
        if (substr($path, -1) !== '/') {
            $path .= '/';
        }
        $this->createServerProps($path, $port);
        return $path;
    }

    private function createServerProps($path, $port) {
        $str = file_get_contents(__DIR__ . '/../config/server.properties.default');
        $str = str_replace('server-port=25565', "server-port=$port", $str);
        file_put_contents($path . 'server.properties', $str);
    }

    /**
     * @return int first free port number
     */
    public function findFreePort() {
        $ports = $this->getTable()->fetchPairs('id', 'port');
        sort($ports);
        if (count($ports) == 0) {
            return 25565;
        }
        $i = $ports[array_keys($ports)[0]];
        foreach ($ports as $port) {
            if ($i != $port) {
                return $i;
            }
            $i++;
        }
        return $i;
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

    /**
     * @param string $path
     * @throws \Nette\InvalidArgumentException
     */
    private function checkPathValidity($path) {
        if (!is_writable($path)) {
            throw new \Nette\InvalidArgumentException('Path is invalid or it is unaccessible');
        }
    }

    /**
     * @return string
     */
    public function generateRuntimeHash() {
        return \Nette\Utils\Strings::random(10, 'A-Za-z');
    }

}
