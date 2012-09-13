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

    public function getIssue($id)
    {
        $issue = new \GitHub\Issue($this->http);
        return $this->unmap($issue->get($this->user, 'RESTFest', '2012-greenville-rosetta', $id));
    }

    protected function unmapObject(\StdClass $data)
    {
        $xml = new \SimpleXMLElement('<ticket></ticket>', 0, false, 'urn:org.restfest.2012.hackday.helpdesk.ticket');
        $xml->addChild('summary', $data->title);
        $xml->addChild('description', $data->body);
        foreach($xml->labels as $label) {
            $xml->addChild('tag', $label);
        }
        $xml->addChild('state', $data->state);
        $xml->addChild('created_at', $data->created_at);
        $xml->addChild('updated_at', $data->updated_at);
        $link = $xml->addChild('xmlns:atom:link');
        $link->addAttribute('rel', 'self');
        $link->addAttribute('href', "http://{$_SERVER['HTTP_HOST']}/tickets/{$data->number}");

        return $xml;
    }

    protected function unmap($data)
    {
        if (is_array($data)) {
            $xml = new \SimpleXmlElement('<xmlns:tickets:tickets></xmlns:tickets:tickets>', 0, false, 'urn:org.restfest.2012.hackday.helpdesk.ticket');
            $xml->addAttribute('xmlns:xmlns', 'urn:org.restfest.2012.hackday.helpdesk.ticket');
            $xml->addAttribute('xmlns:xmlns:atom', 'http://www.w3.org/2005/Atom');
            $xml->addAttribute('xmlns:xmlns:comments', 'urn:org.restfest.2012.hackday.helpdesk.comments');
            $xml->addAttribute('xmlns:xmlns:tickets', 'urn:org.restfest.2012.hackday.helpdesk.tickets');
            $xml->addAttribute('xmlns:xmlns:user', 'urn:org.restfest.2012.hackday.helpdesk.user');
            $dom = dom_import_simplexml($xml);
            foreach($data as $object) {
                $domTicket = $dom->ownerDocument->importNode(dom_import_simplexml($this->unmapObject($object)), true);
                $dom->appendChild($domTicket);
            }
        } else {
            $xml = $this->unmapObject($data);
            $xml->addAttribute('xmlns:xmlns', 'urn:org.restfest.2012.hackday.helpdesk.ticket');
            $xml->addAttribute('xmlns:xmlns:atom', 'http://www.w3.org/2005/Atom' );
            $xml->addAttribute('xmlns:xmlns:comments', 'urn:org.restfest.2012.hackday.helpdesk.comments');
        }

        return $xml->asXml();
    }

    protected function map($xml)
    {
        libxml_use_internal_errors(true);
        $x = simplexml_load_string($xml);
        if (!$x) {
            throw new \InvalidArgumentException();
        }

        $data = array(
            'title' => (string)$x->summary[0],
            'body' => (string)$x->description[0],
            'labels' => array(),
            'state' => (string)$x->state[0]
        );

        foreach($x->tag as $tag) {
            $data['labels'][] = (string)$tag[0];
        }
        return $data;
    }

    public function updateIssue($id, $content)
    {
        $data = $this->map($content);
        $issue = new \GitHub\Issue($this->http);
        return $issue->patch($this->user, 'RESTFest', '2012-greenville-rosetta', $id, $data);
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
            $ghResult = $issue->create($this->user, 'RESTFest', '2012-greenville-rosetta', $issueJson);
            return $this->unmap($ghResult);
        }        

    }
}
