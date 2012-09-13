<?php
namespace GitHub;

class User
{
    protected $token = null;

    public function __construct(\Guzzle\Service\ClientInterface $http)
    {
        $this->http = $http;
    }

    // todo: handle login failure and credentials expiry
    public function login($user, $password)
    {
        if (!file_exists('.github')) {
            $request = $this->http->post(
                '/authorizations',
                null,
                json_encode(
                    array(
                        'scopes' => array('repo'),
                        'note' => 'Incubator Helper'
                    )
                )
            );

            $response = $request->setAuth($user, $password)->send();
            $auth = json_decode($response->getBody());
            file_put_contents('.github', $response->getBody());
        } else {
            $auth = json_decode(file_get_contents('.github'));
        }

        $this->token = $auth->token;
    }

    //todo: ensure user is logged in and token is set
    public function auth($request)
    {
        $request->setHeader('Authorization', "token {$this->token}");
        return $request;
    }
}
