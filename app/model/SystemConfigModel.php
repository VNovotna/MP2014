<?php

use Nette\Utils;

/**
 * Class for editing aplication config files
 * 
 * @author viky
 */
class SystemConfigModel extends \Nette\Object implements ArrayAccess {

    /** @var array */
    private $neon;

    /** @var string */
    private $neonPath = "../app/config/app.neon";

    public function __construct() {
        $file = file_get_contents($this->neonPath);
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

    public function offsetExists($offset) {
        return isset($this->neon[$offset]);
    }

    public function &offsetGet($offset) {
        return $this->neon{$offset};
    }
    
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            throw new Nette\InvalidArgumentException('It\'s not possible to create new config entries!');
        } else {
            $this->neon{$offset} = $value;
        }
    }

    public function offsetUnset($offset) {
        throw new Nette\InvalidArgumentException('It\'s not possible to unset config entries!');
    }
    public function __destruct() {
        file_put_contents(__DIR__.'/../'.$this->neonPath, Utils\Neon::encode($this->neon, Utils\Neon::BLOCK));
    }

}
