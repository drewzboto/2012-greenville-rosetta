<?php
namespace GitHub;

class Issue
{
    protected $http = null;

    public function __construct(\Guzzle\Service\ClientInterface $http)
    {
        $this->http = $http;
    }

    public function get(User $user, $org, $repo, $id)
    {
        $request = $this->http->get("/repos/{$org}/{$repo}/issues/$id");
        try {
            $response = $user->auth($request)->send();
        } catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {
            return false;
        }

        return json_decode($response->getBody());
    }

    public function create(User $user, $org, $repo, $data)
    {
        $request = $this->http->post("/repos/{$org}/{$repo}/issues", null, json_encode($data));
        try {
            $response = $user->auth($request)->send();
        } catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {
            echo $e->getMessage();
            return false;
        }
        $data = json_decode($response->getBody(true));
        return $data->number;
    }

    public function patch(User $user, $org, $repo, $id, $data)
    {
        $request = $this->http->patch("/repos/{$org}/{$repo}/issues/{$id}", null, json_encode($data));
        try {
            $response = $user->auth($request)->send();
        } catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {
            echo $e->getMessage();
            return false;
        }

        return true;
    }
}
