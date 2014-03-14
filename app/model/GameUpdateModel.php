<?php

/**
 * GameUpdater offers downloading Minecraft server .jar from preset webpages
 * 
 * @author viky
 */
class GameUpdateModel extends Nette\Object{

    private $config;

    public function __construct(SystemConfigModel $configModel) {
        $this->config = $configModel->getConfig('update');
    }

    /**
     * parse whole page in url searching for links for minecraft server
     * (could take a long time btw)
     * @param string $url
     * @return array link => version
     */
    private function parseForLinks($url) {
        $html = @implode('', file($url));
        if ($html != NULL) {
            $dom = new DOMDocument;
            libxml_use_internal_errors(true);
            $dom->loadHTML($html);
            $links = $dom->getElementsByTagName('a');
            return $this->findMinecraftLinks($links);
        }
        return array();
    }

    private function findMinecraftLinks($links) {
        $result = array();
        $matches = array();
        foreach ($links as $key => $link) {
            if (preg_match($this->config['regex'], $link->getAttribute('href'), $matches)) {
                $key = $this->getVersionFromFileName($matches[0]);
                $result[$link->getAttribute('href')] = $key;
            }
        }
        return $result;
    }

    /**
     * return stable versions of servers 
     * @return array link => version
     */
    public function getStableJars() {
        $urls = preg_split("#\ #", $this->config['stable']);
        $result = array();
        foreach ($urls as $url) {
            $result = array_merge($result, $this->parseForLinks($url));
        }
        return $result;
    }

    /**
     * return snapshot versions of servers
     * @return array version => link
     */
    public function getSnapshotsJars() {
        $urls = preg_split("#\ #", $this->config['unstable']);
        $result = array();
        foreach ($urls as $url) {
            $result = array_merge($result, $this->parseForLinks($url));
        }
        return $result;
    }

    /**
     * Download file from $url to directory in $path
     * @param string $url
     * @param string $path
     * @param string $version
     * @return string filename 
     */
    public function download($url, $path, $version) {
        set_time_limit(0);
        $raw = file_get_contents($url);
        $name =  'minecraft_server.' . $version . '.jar';
        file_put_contents($path . $name, $raw);
        return $name;
    }

    /**
     * return all filenames that are already downloaded
     * @param string $path
     * @return array 'filename' => version
     */
    public function getAvailableJars($path) {
        $result = array();
        $regex = substr($this->config['regex'], 1, -1);
        foreach (Finder::findFiles($regex)->in($path)->orderByName() as $file) {
            $result[$file->getFilename()] = $this->getVersionFromFileName($file->getFilename());
        }
        return array_reverse($result);
    }

    /**
     * get version from minecraft server filename
     * programer quote: it's not very universal method
     * @param string $filename
     * @return string
     */
    public static function getVersionFromFileName($filename) {
        return substr($filename, 17, -4);
    }

}
