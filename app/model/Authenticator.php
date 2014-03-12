<?php

use Nette\Security,
    Nette\Utils\Strings;

/**
 * Users authenticator.
 */
class Authenticator extends \Nette\Object implements Security\IAuthenticator {

    /** @var \Nette\Database\Connection */
    private $database;

    public function __construct(\Nette\Database\Context $database) {
        $this->database = $database;
    }

    /**
     * Performs an authentication.
     * @return Nette\Security\Identity
     * @throws Nette\Security\AuthenticationException
     */
    public function authenticate(array $credentials) {
        list($username, $password) = $credentials;
        $row = $this->database->table('user')->where('username', $username)->fetch();
        if (!$row) {
            throw new Security\AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);
        }
        if (!$this->checkPassword($row->password, $password)) {
            throw new Security\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);
        }
        $roles = $this->database->table('permission')->where('user_id', $row->id)->fetchPairs('server_id', 'role');
        $arr = array('username' => $row->username, 'serverRoles' => $roles);
        if ($row->role == 'admin') {
            return new Nette\Security\Identity($row->id, 'admin', $arr);
        } else {
            return new Nette\Security\Identity($row->id, 'player', $arr);
        }
    }

    /**
     * check if $passFromForm is same as $passFromDb
     * @param string $passFromDb
     * @param string $passFromForm
     * @return boolean
     */
    public static function checkPassword($passFromDb, $passFromForm) {
        if ($passFromDb === \Authenticator::calculateHash($passFromForm, $passFromDb)) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Computes salted password hash.
     * @param  string
     * @return string
     */
    public static function calculateHash($password, $salt = NULL) {
        if ($password === Strings::upper($password)) { // perhaps caps lock is on
            $password = Strings::lower($password);
        }
        return crypt($password, $salt ? : '$2a$07$' . Strings::random(22));
    }

}
