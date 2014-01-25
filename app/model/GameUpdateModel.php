<?php

/**
 * GameUpdater offers downloading server .jar from minecraft.net and mojang.com
 * Not the best code, but it's working now
 * @author viky
 */
class GameUpdateModel {

    /**
     * parse whole page in url searching for links for minecraft_server
     * (could take a long time btw)
     * @param string $url
     * @return array link => version
     */
    private function parseForLinks($url) {
        $result = array();
        $html = @implode('', file($url));
        if ($html != NULL) {
            $dom = new DOMDocument;
            libxml_use_internal_errors(true);
            $dom->loadHTML($html);
            $links = $dom->getElementsByTagName('a');
            $matches = array();
            foreach ($links as $key => $link) {
                if (preg_match('#minecraft_server.[0-9a-z.]*.jar#', $link->getAttribute('href'), $matches)) {
                    $key = $this->getVersionFromFileName($matches[0]);
                    $result[$link->getAttribute('href')] = $key;
                }
            }
        }
        return $result;
    }

    /**
     * return stable versions of servers 
     * @return array link => version
     */
    public function getStableJars() {
        return $this->parseForLinks('https://minecraft.net/download');
    }

    /**
     * return snapshot versions of servers
     * @return array version => link
     */
    public function getSnapshotsJars() {
        return $this->parseForLinks('https://mojang.com/');
    }

    /**
     * Download file from $url to directory in $path
     * @param string $url
     * @param string $path
     * @param string $version
     */
    public function download($url, $path, $version) {
        set_time_limit(0);
        //$fp = fopen($path . 'minecraft_server.' . $version . '.jar', 'w+');
        $raw = file_get_contents($url);
//        ... check if $raw has anything useful in it
        file_put_contents($path . 'minecraft_server.' . $version . '.jar', $raw);
//        ... check if the file showed up
    }

    /**
     * return all filenames that are already downloaded
     * @param string $path
     * @return array 'filename' => version
     */
    public function getAvailableJars($path) {
        $result = array();
        foreach (Finder::findFiles('minecraft_server.[0-9a-z.]*.jar')->in($path)->orderByName() as $file) {
            $result[$file->getFilename()] = $this->getVersionFromFileName($file->getFilename());
        }
        return array_reverse($result);
    }

    /**
     * get version from minecraft server filename
     * @param string $filename
     * @return string
     */
    public static function getVersionFromFileName($filename) {
        return substr($filename, 17, -4);
    }

}
