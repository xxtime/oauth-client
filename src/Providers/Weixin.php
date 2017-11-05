<?php

/**
 * 移动端登陆文档
 * @link https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1419317851&token=&lang=zh_CN
 * @link https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1419317853&token=&lang=zh_CN
 *
 *
 * /sns/oauth2/access_token
 * {
 *    "access_token":"ACCESS_TOKEN",
 *    "expires_in":7200,
 *    "refresh_token":"REFRESH_TOKEN",
 *    "openid":"OPENID",
 *    "scope":"SCOPE",
 *    "unionid":"o6_bmasdasdsad6_2sgVt7hMZOPfL"
 * }
 *
 *
 * /sns/userinfo
 * {
 *    "openid":"OPENID",
 *    "nickname":"NICKNAME",
 *    "sex":1,
 *    "province":"PROVINCE",
 *    "city":"CITY",
 *    "country":"COUNTRY",
 *    "headimgurl": "PICTURE",
 *    "privilege":[
 *      "PRIVILEGE1",
 *      "PRIVILEGE2"
 *    ],
 *    "unionid": " o6_bmasdasdsad6_2sgVt7hMZOPfL"
 * }
 *
 */
namespace Xxtime\Oauth\Providers;


use Xxtime\Oauth\DefaultException;

class Weixin extends ProviderAbstract
{


    private $endpoint = 'https://api.weixin.qq.com';


    protected $accessToken;


    protected $openId;


    public function __construct(array $option)
    {
        parent::__construct($option);

        if (empty($this->option['clientId']) || empty($this->option['clientSecret'])) {
            throw new DefaultException('missing clientId or clientSecret');
        }
    }


    public function verify($id = '', $token = '')
    {
        $query = [
            'appid'      => $this->option['clientId'],
            'secret'     => $this->option['clientSecret'],
            'code'       => $token,
            'grant_type' => 'authorization_code',
        ];
        $url = $this->endpoint . '/sns/oauth2/access_token?' . http_build_query($query);
        $payload = json_decode($this->http->get($url), true);

        if (!empty($payload['errcode'])) {
            throw new DefaultException($payload['errmsg']);
        }

        $this->openId = $payload['openid'];
        $this->accessToken = $payload['access_token'];

        $response = $this->getInfo();

        return [
            'id'     => $response['unionid'],
            'name'   => $response['nickname'],
            'avatar' => $response['headimgurl'],
            'gender' => $response['sex']
        ];
    }


    public function getAccountInfo()
    {
        $query = [
            'access_token' => $this->accessToken,
            'openid'       => $this->openId,
        ];
        $url = $this->endpoint . '/sns/userinfo?' . http_build_query($query);
        $response = json_decode($this->http->get($url), true);

        if (!empty($response['errcode'])) {
            throw new DefaultException($response['errmsg']);
        }

        return $response;
    }

}