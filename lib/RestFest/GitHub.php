<?php
namespace RestFest;

class GitHub
{
    protected $http;

    protected $user;

    public function __construct($http, $user)
    {
        $this->http = $http;
        $this->user = $user;
    }

    public function getIssues()
    {
        $issues = new \GitHub\Issues($this->http);
        return $this->unmap($issues->get($this->user, 'RESTFest', '2012-greenville-rosetta'));
    }

    protected function unmap($data)
    {
        // TODO: Actually unmap, rather than json_encode
        return json_encode($data);
    }

    public function getIssue()
    {
    }
}
