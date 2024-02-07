# MauticAuth0Bundle Installation Guide

## Introduction
MauticAuth0Bundle is a bundle for Mautic that enables integration with Auth0 tokens, allowing authentication and authorization via Auth0.

## Installation Steps

1. **Composer Installation:**

    Install the bundle via composer:

    ```bash
    composer require cience/mautic-auth0-bundle
    ```

2. **Bundle Activation:**

    Activate the bundle by adding it to your `config/bundles_local.php`:

    ```php
    <?php
	$bundles[] = new Cinece\MauticApiAuth0Bundle\MauticApiAuth0Bundle();
    ```

3. **Configuration:**

    Add the following configuration to your `app/config/local.php`:

    ```php
    // app/config/local.php

    return array(
        // Other configuration...
        'auth0_strategy'              => 'api',
        'auth0_domain'                => '',
        'auth0_client_id'             => '',
        'auth0_client_secret'         => '',
        'auth0_audience'              => [
         ],
        'auth0_authorized_machines'   => [          
        ]
    );
    ```

    Also, add the following to your `app/config/security_local.php`:

    ```php
    // app/config/security_local.php

    $container->loadFromExtension('mautic_api_auth0', [
        'sdk' => [
            'strategy' => '%env(string:MAUTIC_AUTH0_STRATEGY)%',
            'domain' => '%env(string:MAUTIC_AUTH0_DOMAIN)%',
            'client_id' => '%env(string:MAUTIC_AUTH0_CLIENT_ID)%',
            'client_secret' => '%env(string:MAUTIC_AUTH0_CLIENT_SECRET)%',
            'audience' => '%env(json:MAUTIC_AUTH0_AUDIENCE)%',
        ]    
    ]);
    ```

5. **Adding Security Settings**

In `app/config/security.php` change your API settings to be like this
```php
//app/config/security.php

    'api' => [
        'pattern'            => '^/api',
        'fos_oauth'          => true,
        'mautic_plugin_auth' => true,
        'stateless'          => true,
        'http_basic'         => true,
        'mautic_api_auth0'   => true,
    ],

```
5. **Clear Cache:**

    Clear the cache to ensure the changes take effect:

    ```bash
    php bin/console cache:clear

    ```

6. **Testing:**

    Test your Mautic installation to ensure that the Auth0 integration is working as expected.

## Conclusion

Congratulations! You have successfully installed and configured the MauticAuth0Bundle for integration with Auth0 tokens. You can now authenticate and authorize users via Auth0 in your Mautic application. If you encounter any issues, please refer to the documentation or seek support from the Mautic community.
