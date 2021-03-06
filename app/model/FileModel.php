<?php

/**
 * FileModel is intented to do all the dirty work with files
 * @author viky
 */
class FileModel extends \Nette\Object{

    /**
     * open and return whole file content
     * @param string $file enter absolute path
     * @param bool $ignoreNonExistent TRUE if you want to create non existent file
     * @return array each line of file on one line
     * @throws Nette\FileNotFoundException
     */
    public function open($file, $ignoreNonExistent = FALSE) {
        if (file_exists($file)) {
            return file($file);
        } else {
            if ($ignoreNonExistent) {
                $handler = fopen($file, 'c');
                fclose($handler);
                return array();
            } else {
                throw new \Nette\FileNotFoundException;
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
            fclose($handler);
        } else {
            if ($ignoreNonExistent) {
                $handler = fopen($file, 'c+');
                fwrite($handler, $content);
                fclose($handler);
            } else {
                throw new \Nette\FileNotFoundException;
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
        return FALSE;
    }

    /**
     * check if user violated lines that shouldn't be changed by using preg_replace
     * @param string $data
     * @param array $unchangeableLines format: array('patern'=>'replacement', 'patern'=>'replacement')
     * @return string
     */
    public static function checkUnchangeableLines($data, $unchangeableLines) {
        $paterns = array_keys($unchangeableLines);
        $replacements = array_values($unchangeableLines);
        return preg_replace($paterns, $replacements, $data);
    }

}
