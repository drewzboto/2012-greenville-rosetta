<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:comments="urn:org.restfest.2012.hackday.helpdesk.comments"
	xmlns:ticket="urn:org.restfest.2012.hackday.helpdesk.ticket"
	xmlns:user="urn:org.restfest.2012.hackday.helpdesk.user"
	>
	<xsl:output method="text"/>
	<xsl:strip-space elements="*" />

	<xsl:template match="ticket:ticket">{
		"title": "<xsl:value-of select="ticket:summary"/>",
		"body": "<xsl:value-of select="ticket:description"/>"
		<xsl:if test="ticket:assignee">
			,"assignee": "<xsl:value-of select="ticket:assignee/user:user/ticket:name"/>",
		</xsl:if>
		<xsl:if test="ticket:state">
			,"state": "<xsl:value-of select="ticket:state"/>,
		</xsl:if>
		<xsl:if test="ticket:tag">
			,"labels": [ 
			<xsl:for-each select="ticket:tag">
				"<xsl:value-of select="."/>"
				<xsl:if test="position()!=last()">,
				</xsl:if>
			</xsl:for-each>	
			]
		</xsl:if>
		}
	</xsl:template>
</xsl:stylesheet>