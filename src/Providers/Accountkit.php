<?php

/**
 * AccountKit文档
 * @link https://developers.facebook.com/docs/accountkit/graphapi
 */
namespace Xxtime\Oauth\Providers;


use Xxtime\Oauth\DefaultException;

class Accountkit extends ProviderAbstract
{


    private $endpoint = 'https://graph.accountkit.com/v1.2';


    public function __construct(array $option)
    {
        parent::__construct($option);

        if (empty($this->option['clientId'])) {
            throw new DefaultException('missing clientId');
        }
    }


    public function verify($id = '', $token = '')
    {
        $url = $this->endpoint . "/me/?access_token={$token}";
        $payload = json_decode($this->http->get($url), true);

        // check
        if (!empty($payload['error'])) {
            throw new DefaultException($payload['error']['message']);
        }

        if ($payload['application']['id'] != $this->option['clientId']) {
            throw new DefaultException('clientId not match');
        }

        if ($payload['id'] != $id) {
            throw new DefaultException('id error');
        }

        // return
        return [
            'id'     => $payload['id'],
            'name'   => '',
            'avatar' => '',
            'mobile' => $payload['phone']['number'],
        ];
    }

}