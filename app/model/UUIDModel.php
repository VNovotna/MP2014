<?php

/**
 * UUIDModel gets UUID from mojang api
 *
 * @author viky
 */
class UUIDModel extends \Nette\Object {

    /**
     * @param string $userName
     * @param boolean $resultAsArray
     * @return string|array uuid of $usrName or array('uuid' => $uuid, 'name' => $name)
     * @throws ProfileNotUniqueException
     * @throws ProfileNotFoundException
     */
    public function getUuid($userName, $resultAsArray = FALSE) {
        $data = $this->makeRequest($userName);
        if ($data->size > 1) {
            throw new ProfileNotUniqueException;
        }if ($data->size == 0) {
            throw new ProfileNotFoundException;
        }
        if($resultAsArray){
            return array('uuid' => $data->profiles[0]->id, 'name' => $data->profiles[0]->name);
        }
        return $data->profiles[0]->id;
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
