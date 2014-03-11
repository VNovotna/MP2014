<?php

/**
 * UUIDModel gets UUID from mojang api
 *
 * @author viky
 */
class UUIDModel extends \Nette\Object {

    /**
     * @param string $userName
     * @return string uuid of $usrName
     * @throws ProfileNotUniqueException
     * @throws ProfileNotFoundException
     */
    public function getUuid($userName) {
        $data = $this->makeRequest($userName);
        if ($data->size > 1) {
            throw new ProfileNotUniqueException;
        }if ($data->size == 0) {
            throw new ProfileNotFoundException;
        }
        return $data->profiles[0]->id;
    }

    /**
     * @param string $userName
     * @throws Nette\NotImplementedException
     */
    public function getUuids($userName) {
        throw new Nette\NotImplementedException;
    }

    /**
     * @param string $userName
     * @return mixed from json_decode
     * @throws Nette\FileNotFoundException
     */
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
        $result = @file_get_contents($url, false, $context);
        if ($result !== FALSE) {
            return json_decode($result);
        }
        throw new Nette\FileNotFoundException('Network unreachable');
    }

}

class ProfileNotUniqueException extends RuntimeException {
    
}

class ProfileNotFoundException extends RuntimeException {
    
}
