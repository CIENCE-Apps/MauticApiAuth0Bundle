<?php

namespace Cinece\MauticApiAuth0Bundle\DependencyInjection\Auth0Wrapper;
use Auth0\SDK\Auth0;
use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Exception\ConfigurationException;

class Wrapper
{
    private array $configs;
    private array $auth0Clients;

    public function __construct(array $configs)
    {
        $this->configs = $configs['clients'];
        foreach($this->configs as $name => $config) {
            try{                                
                $sdkConfig = new SdkConfiguration($config['sdk']);
                $this->auth0Clients[$name] = new Auth0($sdkConfig);
            }catch(ConfigurationException $e){
                throw new \Exception('Provided Auth0 Configration is wrong, check ' . $name . ' Error is ' . $e->getMessage());
            }
        }
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