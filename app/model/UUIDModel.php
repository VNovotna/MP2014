<?php

/**
 * UUIDModel gets UUID from mojang api
 *
 * @author viky
 */
class UUIDModel extends \Nette\Object {

    public function getUuid($userName) {
        $data = $this->makeRequest($userName);
        if ($data->size > 1) {
            throw new ProfileNotUniqueException;
        }
        return $data->profiles[0]->id;
    }

    public function getUuids($userName) {
        throw new Nette\NotImplementedException;
    }

    private function makeRequest($userName) {
        $url = 'https://api.mojang.com/profiles/page/1';
        $options = array(
            'http' => array(
                'header' => "Content-Type: application/json\r\n",
                'method' => 'POST',
                'content' => '{"name":"' . $userName . '","agent":"minecraft"}'
            ),
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return json_decode($result);
    }

}

class ProfileNotUniqueException extends Exception {
    
}
