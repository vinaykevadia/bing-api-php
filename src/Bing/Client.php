<?php

namespace Bing;
use Bing\BingException;
class Client
{
    protected $version;
    protected $base_uri = 'https://api.datamarket.azure.com/Bing/Search/';
    protected $output;

    public function __construct($api_key, $output = 'json')
    {
        $this->output = $output;
        $this->version = 'v1';
        $auth = base64_encode("$api_key:$api_key");
        $data = array(
            'http' => array(
                'request_fulluri' => true,
                'ignore_errors' => true,
                'header' => "Authorization: Basic $auth",
            ),
        );
        $this->base_uri .= $this->version;
        $this->context = stream_context_create($data);
    }

    /**
     * Get search results
     * @param $endpoint
     * @param array $params
     * @param null $query_url
     * @return mixed
     * @throws \Bing\BingException
     */
    public function get($endpoint, $params = array(), $query_url = null)
    {
        if(!$query_url){
            $qs = "?\$format={$this->output}";
            if ($params['Query']) {
                $params['Query'] = "'{$params['Query']}'";
            }
            $qs .= ($params) ? '&'.http_build_query($params) : '';
            $query_url = $this->base_uri.'/'.$endpoint.$qs;
        }else{
            $query_url = $query_url . "&\$format={$this->output}";
        }

        $data = file_get_contents($query_url, 0, $this->context);
        $_data = json_decode($data, true);

        if(is_array($_data) && !empty($_data["d"])){
            return $_data["d"];
        }else {
            throw new BingException("Error while calling bing search api, : $data");
        }

    }
}
