<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="html"/>
  <xsl:template name="maximum">
    <xsl:param name="pSequence"/>
    <xsl:for-each select="$pSequence">
      <xsl:sort select="." data-type="number" order="descending"/>
      <xsl:if test="position()=1">
        <xsl:value-of select="."/>
      </xsl:if>
    </xsl:for-each>
  </xsl:template>
  <xsl:template name="minimum">
    <xsl:param name="pSequence"/>
    <xsl:for-each select="$pSequence">
      <xsl:sort select="." data-type="number" order="ascending"/>
      <xsl:if test="position()=1">
        <xsl:value-of select="."/>
      </xsl:if>
    </xsl:for-each>
  </xsl:template>
  <xsl:template name="escapeQuote">
    <xsl:param name="pText"/>
    <xsl:if test="string-length($pText) >0">
     <xsl:value-of select="substring-before(concat($pText, '&quot;'), '&quot;')"/>
     <xsl:if test="contains($pText, '&quot;')">
      <xsl:text>\"</xsl:text>
      <xsl:call-template name="escapeQuote">
        <xsl:with-param name="pText" select="substring-after($pText, '&quot;')"/>
      </xsl:call-template>
     </xsl:if>
    </xsl:if>
  </xsl:template>
  <xsl:template name="string-replace-all">
    <xsl:param name="text" />
    <xsl:param name="replace" />
    <xsl:param name="by" />
    <xsl:choose>
      <xsl:when test="contains($text, $replace)">
        <xsl:value-of select="substring-before($text,$replace)" />
        <xsl:value-of select="$by" />
        <xsl:call-template name="string-replace-all">
          <xsl:with-param name="text" select="substring-after($text,$replace)" />
          <xsl:with-param name="replace" select="$replace" />
          <xsl:with-param name="by" select="$by" />
        </xsl:call-template>
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="$text" />
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>
  <xsl:template name="timestampToDate">
    <xsl:param name="seconds"/> 
    <xsl:variable name="hours" select="floor($seconds div (60 * 60))"/>
    <xsl:variable name="divisor_for_minutes" select="$seconds mod (60 * 60)"/>
    <xsl:variable name="minutes" select="floor($divisor_for_minutes div 60)"/>
    <xsl:variable name="divisor_for_seconds" select="$divisor_for_minutes mod 60"/>
    <xsl:variable name="secs" select="ceiling($divisor_for_seconds)"/>
    <xsl:choose>
      <xsl:when test="$hours &lt; 10">
        <xsl:text>0</xsl:text><xsl:value-of select="$hours"/><xsl:text>hh</xsl:text>
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="$hours"/><xsl:text>hh</xsl:text>
      </xsl:otherwise>
    </xsl:choose>
    <xsl:choose>
      <xsl:when test="$minutes &lt; 10">
        <xsl:text>0</xsl:text><xsl:value-of select="$minutes"/><xsl:text>mm</xsl:text>
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="$minutes"/><xsl:text>mm</xsl:text>
      </xsl:otherwise>
    </xsl:choose>
    <xsl:choose>
      <xsl:when test="$secs &lt; 10">
        <xsl:text>0</xsl:text><xsl:value-of select="$secs"/><xsl:text>ss</xsl:text>
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="$secs"/><xsl:text>ss</xsl:text>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>
  <xsl:template name="sumBy">
    <xsl:param name="element" />
    <xsl:param name="from" select="1"/>
    <xsl:param name="to" select="count($element)"/>
    <xsl:param name="sum" select="0" />
    <xsl:if test="$from &lt;= $to">
      <xsl:call-template name="sumBy">
        <xsl:with-param name="element" select="$element"/>
        <xsl:with-param name="from" select="$from + 1"/>
        <xsl:with-param name="to" select="$to"/>
        <xsl:with-param name="sum" select="$sum + $element[$from]"/>
      </xsl:call-template>
    </xsl:if>
    <xsl:if test="$from &gt; $to">
      <xsl:value-of select="$sum"/>
    </xsl:if>
  </xsl:template>
</xsl:stylesheet>