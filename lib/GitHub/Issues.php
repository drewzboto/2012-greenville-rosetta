<?php
namespace GitHub;

class Issues
{
    protected $http = null;

    public function __construct(\Guzzle\Service\ClientInterface $http)
    {
        $this->http = $http;
    }

    public function get(User $user, $org, $repo)
    {
        $request = $this->http->get("/repos/{$org}/{$repo}/issues");
        try {
            $response = $user->auth($request)->send();
        } catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {
            return false;
        }

        return json_decode($response->getBody());
    }
}
