<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
class ConnectionService {

    protected $accessKey;
    protected $retrieveHeaders = true;
    function __construct($accessKey) {
        $this->accessKey = $accessKey;
    }    

    public function connectShop($url, $payload) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->useAuthHeaders());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        // curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);

        
        $result = curl_exec($ch);
        
        $output = $this->parseCurlResponse($result, $ch);
        curl_close($ch);
        
        return $output['body'];
    }

    // parse response utils
    protected function parseCurlResponse($result, $ch) {
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header_text = substr($result, 0, $header_size);
        $body = substr($result, $header_size);
        $json = json_decode($body, TRUE);

        return array(
            'body' => $json,
            'headers' => $header_text
        );
    }
    
    // use API authorization
    protected function useAuthHeaders() {
        return array(
            'x-wpstorybook-access-key: ' . $this->accessKey,
            'Content-Type: application/json',
            'Accept-Encoding: gzip, deflate',
            'Accept: application/json',
        );
    }

    public function getStatus($url, $payload) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->useAuthHeaders());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        // curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);

        
        $result = curl_exec($ch);
        
        $output = $this->parseCurlResponse($result, $ch);
        curl_close($ch);
        
        return $output['body'];
    }

    public function sendUpdate($url, $payload) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->useAuthHeaders());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);
        $result = curl_exec($ch);
        $output = $this->parseCurlResponse($result, $ch);
        curl_close($ch);
        
        return $output['body'];
    }
}
?>