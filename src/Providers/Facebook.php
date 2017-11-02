<?php

/**
 * 前后端参考文档
 * @link https://developers.facebook.com/docs/facebook-login/web
 * @link https://developers.facebook.com/docs/facebook-login/access-tokens/debugging-and-error-handling
 * @link https://developers.facebook.com/docs/graph-api/reference/v2.10/debug_token
 * @link https://developers.facebook.com/docs/facebook-login/access-tokens#apptokens
 *
 * {
 *     "data": {
 *         "app_id": 000000000000000,
 *         "application": "Social Cafe",
 *         "expires_at": 1352419328,
 *         "is_valid": true,
 *         "issued_at": 1347235328,
 *         "scopes": [
 *             "email",
 *             "publish_actions"
 *         ],
 *         "user_id": 1207059
 *     }
 * }
 *
 */
namespace Xxtime\Oauth\Providers;


use Xxtime\Oauth\DefaultException;

class Facebook extends ProviderAbstract
{


    private $endpoint = 'https://graph.facebook.com/v2.10';


    public function __construct(array $option)
    {
        parent::__construct($option);
        if (empty($this->option['clientId']) || empty($this->option['clientSecret'])) {
            throw new DefaultException('missing clientId or clientSecret');
        }
    }


    public function verify($id = '', $token = '')
    {
        $data = [
            'input_token'  => $token,
            'access_token' => $this->option['clientId'] . '|' . $this->option['clientSecret'],
        ];
        $url = $this->endpoint . "/debug_token?" . http_build_query($data);
        $response = json_decode($this->http->get($url), true);

        // check
        if (!empty($response['error'])) {
            throw new DefaultException($response['error']['message']);
        }
        if ($response['data']['app_id'] != $this->option['clientId']) {
            throw new DefaultException('clientId not match');
        }
        if ($response['data']['user_id'] != $id) {
            throw new DefaultException('id error');
        }

        // return
        return [
            'id'     => $response['data']['user_id'],
            'name'   => '',
            'avatar' => '',
        ];
    }

}