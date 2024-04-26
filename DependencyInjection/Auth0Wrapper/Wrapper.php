<?php

namespace Cinece\MauticApiAuth0Bundle\DependencyInjection\Auth0Wrapper;
use Auth0\SDK\Auth0;
use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Exception\ConfigurationException;
use GuzzleHttp\Client;

class Wrapper
{
    private array $configs;
    private array $auth0Clients;
    private Client $httpClient;

    public function __construct(array $configs, Client $httpClient)
    {
        $this->configs = $configs['clients'];
        $this->httpClient = $httpClient;        
        foreach($this->configs as $name => $config) {
            try{                                
                $fixedConfig = $this->snakeToCamelKeys($config['sdk']);                
                $sdkConfig = new SdkConfiguration($fixedConfig);
                $sdkConfig->setHttpClient($this->httpClient);
                $this->auth0Clients[$name] = new Auth0($sdkConfig);
            }catch(ConfigurationException $e){
                throw new \Exception('Provided Auth0 Configration is wrong, check ' . $name . ' Error is ' . $e->getMessage());
            }
        }
    }

    function snakeToCamelKeys($array) {
        $result = array();
        foreach ($array as $key => $value) {
            // Convert underscore-separated words to camelCase
            $camelKey = preg_replace_callback('/(_[a-z])/', function($match) {
                return strtoupper($match[0][1]);
            }, $key);
            // Remove underscores
            $camelKey = str_replace('_', '', $camelKey);
            // Make first character lowercase
            $camelKey = lcfirst($camelKey);
            $result[$camelKey] = $value;
        }
        return $result;
    }

    public function getConfigs(): array
    {
        return $this->configs;
    }

    public function getAuth0Client(string $name): Auth0
    {
        if (!isset($this->auth0Clients[$name])) {
            $this->auth0Clients[$name] = new Auth0($this->configs[$name]);
        }

        return $this->auth0Clients[$name];
    }

    public function getAuth0Clients(): array
    {
        return $this->auth0Clients;
    }

}