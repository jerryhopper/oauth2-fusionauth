<?php

namespace JerryHopper\Directus\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;
use JerryHopper\Directus\OAuth2\Client\Provider\Exception\FusionAuthIdentityProviderException;

class FusionAuth extends AbstractProvider
{
    use BearerAuthorizationTrait;

    protected $baseUrl;
    protected $apiVersion = 'v1';

    protected $urlAuthorize;
    protected $urlAccessToken;
    protected $urlResourceOwnerDetails;


    /**
     * Get authorization url to begin OAuth flow
     *
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return $this->urlAuthorize;
    }

    /**
     * Get access token url to retrieve token
     *
     * @param  array $params
     *
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->urlAccessToken;
    }

    /**
     * Get provider url to fetch user details
     *
     * @param  AccessToken $token
     *
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->urlResourceOwnerDetails;
    }

    /**
     * Get the default scopes used by this provider.
     *
     * This should not be a complete list of all scopes, but the minimum
     * required for the provider user interface!
     *
     * @return array
     */
    protected function getDefaultScopes()
    {
        return [];
    }

    /**
     * Check a provider response for errors.
     *
     * @link   https://developer.okta.com/reference/error_codes/
     *
     * @param  ResponseInterface $response
     * @param  string $data Parsed response data
     * @throws \JerryHopper\Directus\OAuth2\Client\Provider\Exception\FusionAuthIdentityProviderException
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (isset($data['error'])) {
            throw FusionAuthIdentityProviderException::oauthException($response, $data);
        }
    }

    /**
     * Generate a user object from a successful user details request.
     *
     * @param array $response
     * @param AccessToken $token
     *
     * @return ResourceOwnerInterface
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        $user = new FusionAuthResourceOwner($response);

        return $user->setDomain($this->getBaseApiUrl());
    }

    /**
     * Gets the api base url
     *
     * @return string
     */
    protected function getBaseApiUrl()
    {
        return $this->baseUrl . '/' . $this->apiVersion;
    }
}
