<?php

namespace DB;

use Nette;

/**
 * Handles everthing around users and theirs permissions
 * @author viky
 */
class UserRepository extends Repository {

    /**
     * @param string $username
     * @return Nette\Database\Table\Selection or FALSE if nothing found
     */
    public function findByName($username) {
        return $this->findAll()->where('username', $username)->fetch();
    }

    /**
     * returns id of the new user or throws PDOExeption
     * @param string $username
     * @param string $password
     * @return int user id
     * @throws PDOException
     */
    public function addUser($username, $password) {
        $password = \Authenticator::calculateHash($password);
        try {
            $this->getTable()->insert(array(
                'username' => $username,
                'password' => $password
            ));
            return $this->getTable()->where(array('username' => $username))->fetch()->id;
        } catch (PDOException $e) {
            throw new PDOException;
        }
    }

    /**
     * @param int $id user id
     * @throws \Nette\InvalidArgumentException
     */
    public function deleteUser($id) {
        if (is_numeric($id)) {
            $this->getTable()->where(array('id' => $id))->delete();
        } else {
            throw new \Nette\InvalidArgumentException;
        }
    }

    /**
     * @param int $id user id
     * @param string $role
     */
    public function setRole($id, $role) {
        $this->getTable()->where(array('id' => $id))->update(array('role' => $role));
    }

    /**
     * @param int $id user id
     * @param string $password
     */
    public function setPassword($id, $password) {
        $this->getTable()->where(array('id' => $id))->update(array(
            'password' => Authenticator::calculateHash($password)
        ));
    }
/**
 * 
 * @param int $id user id
 * @param string $mcname new MC nick
 */
    public function setMcNick($id, $mcname) {
        $this->getTable()->where(array('id' => $id))
                ->update(array('mcname' => $mcname));
    }

    /**
     * @param string $role
     * @return Nette\Database\Table\Selection
     */
    public function findAllInRole($role) {
        return $this->findAll()->where("role LIKE $role");
    }

    /**
     * return all permissions lines from selected server
     * @param int $serverId 
     * @return Nette\Database\Table\Selection
     */
    public function findAllFromServer($serverId) {
        return $this->getTable('permission')->where(array('server_id' => $serverId));
    }

    public function addOpToServer($serverId, $userId) {
        throw new \Nette\NotImplementedException;
    }

    public function removeOpFromServer($serverId, $userId) {
        throw new \Nette\NotImplementedException;
    }

}
