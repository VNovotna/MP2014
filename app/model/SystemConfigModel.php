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
        $file = implode('', file('../app/config/app.neon'));
        $this->neon = Utils\Neon::decode($file);
    }

    /**
     * @param string $section
     * @return array
     */
    public function getConfig($section = NULL) {
        if ($section == NULL) {
            return $this->neon;
        } else {
            return $this->neon[$section];
        }
    }

    /**
     * @param string $section
     */
    public function setConfig($section = NULL) {
        throw new Nette\NotImplementedException;
    }

}
