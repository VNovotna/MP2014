<?php

/**
 * FileModel is intented to do all the dirty work with files
 * @author viky
 */
class FileModel {

    /**
     * open and return whole file content
     * @param string $file enter absolute path
     * @param bool $ignoreNonExistent TRUE if want to create non existent files
     * @return array each line of file on one line
     * @throws Nette\FileNotFoundException
     */
    public function open($file, $ignoreNonExistent = FALSE) {
        if (file_exists($file)) {
            return file($file);
        } else {
            if ($ignoreNonExistent) {
                fopen($file, 'c');
                return array();
            } else {
                throw Nette\FileNotFoundException;
            }
        }
    }

    /**
     * write content into a file, ignoring previous content
     * @param array $content array of strings, each string representing a line
     * @param string $file enter absolute path
     * @param bool $ignoreNonExistent TRUE if want to create non existent files
     * @return boolean
     */
    public function write($content, $file, $ignoreNonExistent = FALSE) {
        if (file_exists($file)) {
            $handler = fopen($file, 'w');
            fwrite($handler, $content);
        } else {
            if ($ignoreNonExistent) {
                $handler = fopen($file, 'c+');
                fwrite($handler, $content);
            } else {
                throw Nette\FileNotFoundException;
            }
        }
        return TRUE;
    }

    /**
     * add content into a file
     * @param array $content array of strings, each string representing a line
     * @param string $file enter absolute path
     * @param bool $ignoreNonExistent TRUE if want to create non existent files
     * @return boolean
     * @throws Nette\NotImplementedException
     */
    public function add($content, $file, $ignoreNonExistent = FALSE) {
        throw Nette\NotImplementedException;
        return TRUE;
    }

}
