<?php
namespace GitHub;

class Issue
{
    protected $http = null;

    public function __construct(\Guzzle\Service\ClientInterface $http)
    {
        $this->http = $http;
    }

    public function post(User $user, $org, $repo, $issue)
    {
        $request = $this->http->post("/repos/{$org}/{$repo}/issues", null, $issue);
        try {
            $response = $user->auth($request)->send();
        } catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {
            return false;
        }

        return json_decode($response->getBody());
    }
}
