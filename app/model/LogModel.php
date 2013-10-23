<?php

/**
 * Class for reading log files
 * 
 * @author viky
 */
class LogModel extends \Nette\Object {

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

    /**
     * make log files colorful
     * @param array $logArray
     * @return array
     */
    public static function makeColorful($logArray) {
        foreach ($logArray as $i => $line) {
            if (preg_match("/^\[(\d{2}):(\d{2}):(\d{2})\]/", $line, $out)) {
                $logArray[$i] = "<span style='color:green'>" . $out[0] . "</span>" . substr($line, 10);
            }
            if (preg_match("# \[.*/INFO\]:#", $logArray[$i], $out)) {
                $replaceWith = "<span style='color:blue'>" . $out[0] . "</span>";
                $logArray[$i] = str_replace($out[0], $replaceWith, $logArray[$i]);
            }
            if (preg_match("# \[.*/ERROR\]:#", $logArray[$i], $out)) {
                $replaceWith = "<span style='color:darkred'>" . $out[0] . "</span>";
                $logArray[$i] = str_replace($out[0], $replaceWith, $logArray[$i]);
            }
            if (preg_match("# \[.*/WARN\]:#", $logArray[$i], $out)) {
                $replaceWith = "<span style='color:red'>" . $out[0] . "</span>";
                $logArray[$i] = str_replace($out[0], $replaceWith, $logArray[$i]);
            }
        }
        return $logArray;
    }

}
