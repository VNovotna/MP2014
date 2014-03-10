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
        $file = $this->readOpsFromFile($path, 'ops.txt');
        if($delete == TRUE){
            $write = $db;
        }else{
            $write = array_unique(array_merge($db, $file));
        }
        $this->fileModel->write(implode("\n", $write), $path . 'ops.txt', TRUE);
        @unlink($path . 'ops.json');
    }

    /**
     * @param int $serverId
     * @return array
     */
    private function readOpsFromDb($serverId) {
        $ops = $this->findAllOps($serverId);
        $result = array();
        foreach ($ops as $op) {
            $result[] = $op->user->mcname;
        }
        return $result;
    }

    /**
     * @param string $path
     * @return array of existing ops 
     */
    private function readOpsFromFile($path, $file) {
        $ops = $this->fileModel->open($path . $file, TRUE);
        array_walk($ops, create_function('&$val', '$val = trim($val);'));
        return $ops;
    }

}
