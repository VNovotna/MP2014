<?php

/**
 * Class for reading log files
 * 
 * @author viky
 */
class LogModel extends \Nette\Object{

    /**
     * @var Finder
     */
    protected $finder;

    public function __construct($filePath, $fileMask = 'latest.log*', $exclude = '*lck') {
        $this->finder = Finder::findFiles($fileMask)->exclude($exclude)->in($filePath);
    }

    public function getAll() {
        $i = 0;
        $output = array();
        foreach ($this->finder->orderByMTime() as $value) {
            $output = array_merge($output, array_reverse(file($value)));
            $i++;
        }
        return $output;
    }

    public function getWarnings() {
        
    }

    public function getInfos() {
        
    }

}
