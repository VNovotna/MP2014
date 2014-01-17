<?php

/**
 * GameUpdater offers downloading server .jar from minecraft.net and mojang.com
 * Not the best code, but it's working now
 * @author viky
 */
class GameUpdateModel {
    /**
     * parse whole page in url searching for links for minecraft_server
     * @param string $url
     * @return array link => version
     */
    private function parseForLinks($url) {
        $html = implode('', file($url));
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        $links = $dom->getElementsByTagName('a');
        $matches = array();
        $result = array();
        foreach ($links as $key => $link) {
            if (preg_match('#minecraft_server.[0-9a-z.]*.jar#', $link->getAttribute('href'), $matches)) {
                $key = $this->getVersionFromFileName($matches[0]);
                $result[$link->getAttribute('href')] = $key;
            }
        }
        return $result;
    }

    /**
     * get version from minecraft server filename
     * @param string $filename
     * @return string
     */
    private function getVersionFromFileName($filename) {
        return substr($filename, 17, -4);
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
 * return all filenames that are already downloaded
 * @param string $path
 * @return array version => 'filename'
 */
    public function getAvailableJars($path) {
        foreach (Finder::findFiles('minecraft_server.[0-9a-z.]*.jar')->in($path) as $key => $file) {
            echo $key; // $key je řetězec s názvem souboru včetně cesty
            echo $file; // $file je objektem SplFileInfo
        }
        return array();
    }

}
