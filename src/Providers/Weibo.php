<?php

/**
 * 新浪微博OAuth
 * @link https://open.weibo.com/wiki/%E6%8E%88%E6%9D%83%E6%9C%BA%E5%88%B6
 * @link https://open.weibo.com/wiki/Oauth2/authorize
 * @link https://open.weibo.com/wiki/Oauth2/access_token
 * @link https://open.weibo.com/wiki/Oauth2/get_token_info
 * @link https://open.weibo.com/wiki/Oauth2/revokeoauth2
 */

namespace Xxtime\Oauth\Providers;


use Xxtime\Oauth\DefaultException;

class Weibo extends ProviderAbstract
{


    private $endpoint = 'https://api.weibo.com';


    public function __construct(array $option)
    {
        parent::__construct($option);

        if (empty($this->option['clientId']) || empty($this->option['clientSecret'])) {
            throw new DefaultException('missing clientId or clientSecret');
        }
    }


    /**
     * request code, this will redirect
     * @see https://open.weibo.com/wiki/Oauth2/authorize
     */
    public function getCode()
    {
        $uri = "/oauth2/authorize";

        $params = [
            "client_id"    => $this->option["clientId"],
            "redirect_uri" => $this->option["redirect"],
            "display"      => "mobile",
            "forcelogin"   => false,
            "language"     => "zh",
        ];
        if (isset($this->option["scope"])) {
            $params["scope"] = $this->option["scope"];
        }
        if (isset($this->option["state"])) {
            $params["state"] = $this->option["state"];
        }
        if (isset($this->option["display"])) {
            $params["display"] = $this->option["display"];
        }

        $url = $this->endpoint . $uri . "?" . http_build_query($params);
        header("Location:" . $url);
        return;
    }


    /**
     * @link https://open.weibo.com/wiki/Oauth2/access_token
     * @param string $code
     * @return mixed
     */
    public function getAccessToken($code = "")
    {
        if (!$code) {
            throw new DefaultException("no code");
        }
        $uri    = "/oauth2/access_token";
        $params = [
            "client_id"     => $this->option["clientId"],
            "client_secret" => $this->option["clientSecret"],
            "grant_type"    => "authorization_code",
            "code"          => $code,
            "redirect_uri"  => $this->option["redirect"],
        ];
        $json   = $this->http->post($this->endpoint . $uri, $params);
        $data   = json_decode($json, true);
        if (isset($data["error_code"])) {
            throw new DefaultException($data["error_description"]);
        }
        $result = [
            "accessToken" => $data["access_token"],
            "expires"     => $data["expires_in"],
            "uid"         => $data["uid"],
        ];

        return $result;
    }

    public function verify($id = '', $token = '')
    {
        // TODO: Implement verify() method.
    }


}
