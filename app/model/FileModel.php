<?php

/**
 * Description of FileModel
 *
 * @author viky
 */
abstract class FileModel {

    /**
     * @var array 
     */
    protected $fileNames = array();

    /**
     * @var Finder
     */
    protected $finder;

    /**
     * 
     * @param string $filePath
     * @param string $fileMask
     */
    public function __construct($filePath, $fileMask) {
        $finder = Finder::findFiles($fileMask)->in($filePath);
        foreach ($finder as $name => $file) {
            $this->fileNames[$name] = $file;
        }
        $this->finder = $finder;
    }

}
