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
        // todo: etags / caching of content from GitHub
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

    public function createIssue($data)
    {
        $issue = new \GitHub\Issue($this->http);

        $xslt = new \XSLTProcessor();
        $xsl = new \DOMDocument();
        $xml = new \DOMDocument();

        $xsl->load( "xslt/ticket_to_issue.xslt", LIBXML_NOCDATA);
        $xml->loadXML( $data, LIBXML_NOCDATA);


        $xslt->importStylesheet( $xsl );

        $issueJson = $xslt->transformToXML( $xml );
        if ($issueJson) 
        {   
            $ghResult = $issue->post($this->user, 'RESTFest', '2012-greenville-rosetta', $issueJson);
            return $this->unmap($ghResult);
        }        

    }
}
