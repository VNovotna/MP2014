<?php

namespace DB;

/**
 * PermissionRepository handles permission table and ops.txt
 *
 * @author viky
 */
class PermissionRepository extends Repository {

    /** @var ServerRepository */
    private $serverRepo;

    /** @var \ServerCommander */
    private $serverComm;

    /** @var UserRepository */
    private $userRepo;

    /** @var \FileModel */
    private $fileModel;

    /** @var \UUIDModel */
    private $uuidModel;

    public function __construct(\Nette\Database\Context $database, ServerRepository $serverRepo, \ServerCommander $serverComm, UserRepository $userRepo, \FileModel $fileModel, \UUIDModel $uuidModel) {
        parent::__construct($database);
        $this->serverRepo = $serverRepo;
        $this->serverComm = $serverComm;
        $this->userRepo = $userRepo;
        $this->fileModel = $fileModel;
        $this->uuidModel = $uuidModel;
    }

    /**
     * return all permissions lines from selected server - ops and owner
     * @param int $serverId 
     * @return Nette\Database\Table\ActiveRow
     */
    public function findAllOps($serverId) {
        return $this->getTable()->where(array('server_id' => $serverId));
    }

    /**
     * @param int $userId
     * @return array server_id => role
     */
    public function getPermissions($userId) {
        return $this->getTable()->where(array('user_id' => $userId))->fetchPairs('server_id', 'role');
    }

    /**
     * @param int $serverId
     * @param int $userId
     * @param string $runhash nescesary when server is running
     * @return boolean
     * @throws \RuntimeException
     */
    public function addOpToServer($serverId, $userId, $runhash = NULL) {
        if (!$this->addOpToDb($serverId, $userId)) {
            return FALSE;
        }
        if ($runhash == NULL) {
            $this->writeOpsToFile($serverId, FALSE);
        } else {
            $this->callCommand('op', $userId, $runhash);
        }
        return TRUE;
    }

    /**
     * @param int $serverId
     * @param int $userId
     * @param string $runhash nescesary when server is running
     * @return boolean
     * @throws \RuntimeException
     */
    public function removeOpFromServer($serverId, $userId, $runhash = NULL) {
        $this->removeOpFromDb($serverId, $userId);
        if ($runhash == NULL) {
            $this->writeOpsToFile($serverId, TRUE);
        } else {
            $this->callCommand('deop', $userId, $runhash);
        }
        return TRUE;
    }

    /**
     * checks whether someone called deop from game and corrects database
     * @param int $serverId
     */
    public function syncDBwithFile($serverId) {
        $path = $this->serverRepo->getPath($serverId);
        $file = $this->getNamesFromJsonArray($this->readOpsFromFile($path, 'ops.json'));
        $db = $this->getNamesFromJsonArray($this->readOpsFromDb($serverId));
        $diff = array_diff($db, $file);
        foreach ($diff as $opName) {
            $user = $this->userRepo->findBy(array('mcname' => $opName))->fetch();
            $this->removeOpFromDb($serverId, $user->id);
        }
    }

    /**
     * @param int $serverId
     * @param int $userId
     * @return boolean
     * @throws \RuntimeException
     */
    private function addOpToDb($serverId, $userId) {
        try {
            $this->getTable('permission')->insert(array(
                'user_id' => $userId,
                'server_id' => $serverId,
                'role' => 'op'
            ));
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                return FALSE;
            }
            throw new \RuntimeException($e->getMessage(), $e->getCode());
        }
        return TRUE;
    }

    /**
     * @param int $serverId
     * @param int $userId
     * @throws \RuntimeException
     */
    private function removeOpFromDb($serverId, $userId) {
        try {
            $this->getTable('permission')->where(array(
                'user_id' => $userId,
                'server_id' => $serverId,
                'role' => 'op'
            ))->delete();
        } catch (\PDOException $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param string $command 'op' | 'deop'
     * @param int $userId
     * @param string $runhash
     */
    private function callCommand($command, $userId, $runhash) {
        $mcname = $this->userRepo->findById($userId)->fetch()->mcname;
        $this->serverComm->issueCommand($command . ' ' . $mcname, $runhash);
    }

    /**
     * Call only when server is NOT running
     * @param int $serverId
     * @param boolean TRUE if you want to delete op, FALSE if you want to add op
     */
    private function writeOpsToFile($serverId, $delete) {
        $path = $this->serverRepo->getPath($serverId);
        $db = $this->readOpsFromDb($serverId);
        if ($delete == TRUE) {
            $write = $db;
        } else {
            $file = $this->readOpsFromFile($path, 'ops.json');
            $merge = array_merge($db, $file);
            $write = array_unique($merge, SORT_REGULAR);
        }
        $this->fileModel->write(json_encode($write, JSON_PRETTY_PRINT), $path . 'ops.json', TRUE);
    }

    /**
     * @param int $serverId
     * @return array suitable for json
     */
    private function readOpsFromDb($serverId) {
        $ops = $this->findAllOps($serverId);
        $result = array();
        foreach ($ops as $op) {
            $result[] = array(
                'uuid' => $op->user->uuid,
                'name' => $op->user->mcname,
                'level' => 4);
        }
        return $result;
    }

    /**
     * @param string $path
     * @return array suitable for json
     */
    private function readOpsFromFile($path, $file) {
        try {
            $opsFile = $this->fileModel->open($path . $file, FALSE);
            $json = json_decode(implode('', $opsFile));
            $ops = array();
            foreach ($json as $record) {
                $ops[] = array(
                    'uuid' => $record->uuid,
                    'name' => $record->name,
                    'level' => $record->level);
            }
            return $ops;
        } catch (\Nette\FileNotFoundException $ex) {
            return array();
        }
    }

    /**
     * @param array $jsonLike
     * @return array of mcnames
     */
    private function getNamesFromJsonArray($jsonLike) {
        $result = array();
        foreach ($jsonLike as $item) {
            $result[] = $item['name'];
        }
        return $result;
    }

}
