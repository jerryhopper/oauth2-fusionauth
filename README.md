# FusionAuth Provider for OAuth 2.0 Client

This package provides FusionAuth OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Installation

To install, use composer:

```
composer require jerryhopper/directus-oauth2-fusionauth
```

## Usage

Usage is the same as The League's OAuth client, using `\JerryHopper\Directus\OAuth2\Client\Provider\FusionAuth` as the provider.

### Authorization Code Flow

```php
$provider = new \JerryHopper\Directus\OAuth2\Client\Provider\FusionAuth([
    'clientId'          => '{client-id}',
    'clientSecret'      => '{client-secret}',
    'redirectUri'       => 'https://example.com/callback-url',
    'urlAuthorize'            => 'fusionauth:9011/oauth2/authorize',
    'urlAccessToken'          => 'fusionauth:9011/oauth2/token',
    'urlResourceOwnerDetails' => 'fusionauth:9011/oauth2/userinfo',
]);

if (!isset($_GET['code'])) {

    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $provider->getState();
    header('Location: '.$authUrl);
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

    unset($_SESSION['oauth2state']);
    exit('Invalid state');

} else {

    // Try to get an access token (using the authorization code grant)
    $token = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
    ]);

    // Optional: Now you have a token you can look up a users profile data
    try {

        // We got an access token, let's now get the user's details
        $user = $provider->getResourceOwner($token);

        // Use these details to create a new profile
        printf('Hello %s!', $user->getNickname());

    } catch (Exception $e) {

        // Failed to get user details
        exit('Oh dear...');
    }

    // Use this to interact with an API on the users behalf
    echo $token->getToken();
}
```

### Managing Scopes

When creating your authorization URL, you can specify the state and scopes your application may authorize.

```php
$options = [
    'state' => 'OPTIONAL_CUSTOM_CONFIGURED_STATE',
    'scope' => ['openid email profile']
];

$authorizationUrl = $provider->getAuthorizationUrl($options);
```
If neither are defined, the provider will utilize internal defaults.

At the time of authoring this documentation, the [following scopes are available](https://fusionauth.io/docs/v1/tech/oauth/endpoints).

- openid
- offline_access 
- offline_access 
- email
- phone
- address
- groups

## Testing

``` bash
$ ./vendor/bin/phpunit
```
