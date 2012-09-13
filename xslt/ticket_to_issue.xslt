<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:atom="http://www.w3.org/2005/Atom"
    xmlns:comments="urn:org.restfest.2012.hackday.helpdesk.comments"
    xmlns:ticket="urn:org.restfest.2012.hackday.helpdesk.ticket"
>
<xsl:output method="text"/>
	<xsl:template match="ticket:ticket">
	{
	  "title": "<xsl:value-of select="ticket:summary"/>",
	  "body": "<xsl:value-of select="ticket:description"/>",
	  <xsl:if test="ticket:assignee">
  	  "assignee": "<xsl:value-of select="ticket:assignee/ticket:name"/>",
  	  </xsl:if>
	  "labels": [ 
	    <xsl:for-each select="ticket:tag">
	    	<xsl:value-of select="."/>
	    	<xsl:if test="position()!=last()">,
	    	</xsl:if>
	    </xsl:for-each>	
	  ]
	}
	}
	</xsl:template>
</xsl:stylesheet>