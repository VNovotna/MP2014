<?php

use Nette\Utils;

/**
 * Class for editing aplication config files
 * 
 * @author viky
 */
class SystemConfigModel extends Nette\Object {

    /** @var array */
    private $neon = array();

    public function __construct() {
        $file = implode('',file('../app/config/app.neon'));
        $this->neon = Utils\Neon::decode($file);
    }

    public function getConfig() {
        return $this->neon;
    }

}
