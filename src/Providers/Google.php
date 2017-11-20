<?php

/**
 * 前端后端参考文档
 * @link https://developers.google.com/identity/sign-in/web/sign-in
 * @link https://developers.google.com/identity/sign-in/web/backend-auth
 *
 * Use Google's public keys (available in JWK or PEM format) to verify the token's signature.
 * The value of aud in the ID token is equal to one of your app's client IDs.
 * The value of iss in the ID token is equal to accounts.google.com or https://accounts.google.com.
 * The expiry time (exp) of the ID token has not passed.
 *
 * 验证返回信息
 * {
 *  // These six fields are included in all Google ID Tokens.
 *  "iss": "https://accounts.google.com",
 *  "sub": "110169484474386276334",
 *  "azp": "1008719970978-hb24n2dstb40o45d4feuo2ukqmcc6381.apps.googleusercontent.com",
 *  "aud": "1008719970978-hb24n2dstb40o45d4feuo2ukqmcc6381.apps.googleusercontent.com",
 *  "iat": "1433978353",
 *  "exp": "1433981953",
 *
 *  // These seven fields are only included when the user has granted the "profile" and
 *  // "email" OAuth scopes to the application.
 *  "email": "testuser@gmail.com",
 *  "email_verified": "true",
 *  "name" : "Test User",
 *  "picture": "https://lh4.googleusercontent.com/-kYgzyAWpZzJ/ABCDEFGHI/AAAJKLMNOP/tIXL9Ir44LE/s99-c/photo.jpg",
 *  "given_name": "Test",
 *  "family_name": "User",
 *  "locale": "en"
 * }
 *
 */
namespace Xxtime\Oauth\Providers;


use Xxtime\Oauth\DefaultException;

class Google extends ProviderAbstract
{


    private $endpoint = 'https://www.googleapis.com/oauth2/v3';


    public function __construct(array $option)
    {
        parent::__construct($option);

        if (empty($this->option['clientId'])) {
            throw new DefaultException('missing clientId');
        }
    }


    public function verify($id = '', $token = '')
    {
        if (!$id || !$token) {
            throw new DefaultException('missing argv');
        }

        $url = $this->endpoint . '/tokeninfo?id_token=' . $token;
        $payload = json_decode($this->http->get($url), true);


        // Except
        if (isset($payload['error_description'])) {
            throw new DefaultException($payload['error_description']);
        }


        // check aud
        if (strpos($this->option['clientId'], '-') !== false) {
            $clientId = substr($this->option['clientId'], 0, strpos($this->option['clientId'], '-'));
        }
        else {
            $clientId = $this->option['clientId'];
        }
        if ($clientId != substr($payload['aud'], 0, strpos($payload['aud'], '-'))) {
            throw new DefaultException($payload['clientId error']);
        }


        // check sub
        if ($payload['sub'] != $id) {
            throw new DefaultException('id error');
        }


        // check iss
        if (strpos($payload['iss'], 'accounts.google.com') === false) {
            throw new DefaultException('iss error');
        }


        // check exp [timeout 1h]
        if ($payload['exp'] < time()) {
            throw new DefaultException('timeout');
        }


        return [
            'id'     => $payload['sub'],
            'name'   => $payload['name'],
            'email'  => $payload['email'],
            'avatar' => $payload['picture'],
        ];

    }


}