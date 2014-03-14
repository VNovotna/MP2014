<?php

namespace DB;

use Nette;

/**
 * Handles everthing around users
 * @author viky
 */
class UserRepository extends Repository {

    /**
     * @param string $username
     * @return Nette\Database\Table\ActiveRow or FALSE if nothing found
     */
    public function findByName($username) {
        return $this->findAll()->where('username', $username)->fetch();
    }

    /**
     * returns id of the new user or throws PDOExeption
     * @param string $username
     * @param string $password
     * @param string $mcnick minecraft login name
     * @return int user id
     * @throws \RuntimeException
     */
    public function addUser($username, $password, $mcnick = NULL) {
        $password = \Authenticator::calculateHash($password);
        try {
            return $this->getTable()->insert(array(
                        'username' => $username,
                        'password' => $password,
                        'mcname' => $mcnick
                    ))->getPrimary();
        } catch (\PDOException $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param int $id id of user to be deleted
     * @return boolean
     * @throws \RuntimeException
     * @throws \Nette\InvalidArgumentException
     */
    public function deleteUser($id) {
        if (is_numeric($id)) {
            try {
                return $this->getTable()->where(array('id' => $id))->delete();
            } catch (\PDOException $e) {
                throw new \RuntimeException($e->getMessage(), $e->getCode());
            }
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
     * @param string $password unhashed password
     */
    public function setPassword($id, $password) {
        $this->getTable()->where(array('id' => $id))->update(array(
            'password' => \Authenticator::calculateHash($password)
        ));
    }

    /**
     * @param int $id user id
     * @param string $mcname new MC nick
     */
    public function setMcNick($id, $mcname) {
        $this->getTable()->where(array('id' => $id))
                ->update(array('mcname' => $mcname));
    }

    /**
     * @param int $id user id
     * @param string $uuid minecraft UUID
     */
    public function setUUID($id, $uuid) {
        $this->getTable()->where(array('id' => $id))
                ->update(array('uuid' => $uuid));
    }

    /**
     * @param string $role
     * @return Nette\Database\Table\Selection
     */
    public function findAllInRole($role) {
        return $this->findAll()->where("role LIKE $role");
    }

}
