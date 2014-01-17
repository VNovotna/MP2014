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
     * @return type
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
                $key = substr($matches[0], 17, -4);
                $result[$key] = $link->getAttribute('href');
            }
        }
        return $result;
    }

    public function getListOfStableJars() {
        return $this->parseForLinks('https://minecraft.net/download');
    }

    public function getListOfSnapshotsJars() {
        return $this->parseForLinks('https://mojang.com/');
    }

}
