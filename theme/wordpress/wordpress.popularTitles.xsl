<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:import href="wordpress.xsl" />
  <xsl:output method="html"/>
  <xsl:template name="content">

    <xsl:call-template name="pagination">
  		<xsl:with-param name="currentPage" select="//REPORT/INDICATORS/CURRENT_PAGE"/>
  		<xsl:with-param name="recordsPerPage" select="//REPORT/INDICATORS/PER_PAGE" />
  		<xsl:with-param name="records" select="//REPORT/INDICATORS/INDICATOR"/>
  	</xsl:call-template>

    <table class="widefat">
      <thead>
        <tr>
          <th style="text-align:center;width:40px;">#</th>
          <th class="manage-column" style="text-align:center;"><xsl:value-of select="//REPORT/TRANSLATE/TITLE"/></th>
          <th class="manage-column" style="text-align:center;width:150px;"><xsl:value-of select="//REPORT/TRANSLATE/UNIQURI"/></th>
          <th class="manage-column" style="text-align:center;width:150px;"><xsl:value-of select="//REPORT/TRANSLATE/VIEW"/></th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <th colspan="3"><xsl:value-of select="//REPORT/TRANSLATE/TOTALREQUEST"/></th>
          <th style="text-align:center;"><xsl:value-of select="sum(//REPORT/INDICATORS/INDICATOR/COUNT)"/></th>
        </tr>
        <tr>
          <th colspan="3"><xsl:value-of select="//REPORT/TRANSLATE/TOTALUNIQTITLE"/></th>
          <th style="text-align:center;"><xsl:value-of select="count(//REPORT/INDICATORS/INDICATOR)"/></th>
        </tr>
      </tfoot>
      <tbody>
        <xsl:variable name="maxUniq">
          <xsl:call-template name="maximum">
            <xsl:with-param name="pSequence" select="//REPORT/INDICATORS/INDICATOR/COUNT"/>
          </xsl:call-template>
        </xsl:variable>
        <xsl:variable name="minUniq">
          <xsl:call-template name="minimum">
            <xsl:with-param name="pSequence" select="//REPORT/INDICATORS/INDICATOR/COUNT"/>
          </xsl:call-template>
        </xsl:variable>
        <xsl:for-each select="//REPORT/INDICATORS/INDICATOR">
          <xsl:if test="position() &gt; //REPORT/INDICATORS/PER_PAGE * (//REPORT/INDICATORS/CURRENT_PAGE - 1)">
            <xsl:if test="position() &lt;= //REPORT/INDICATORS/PER_PAGE * //REPORT/INDICATORS/CURRENT_PAGE">
              <tr>
                <xsl:if test="position() mod 2 = 1">
                  <xsl:attribute name="class">alternate</xsl:attribute>
                </xsl:if>
                <td align="center"><xsl:value-of select="position()"/>.</td>
                <td>
                  <xsl:choose>
                    <xsl:when test="TITLE=''">
                      <xsl:value-of select="//REPORT/TRANSLATE/NOTITLE"/>
                    </xsl:when>
                    <xsl:otherwise>
                      <xsl:value-of select="TITLE"/>
                    </xsl:otherwise>
                  </xsl:choose>
                </td>
                <td align="center">
                  <a onclick="jQuery('#extend{position()}').toggle();return false;" href="" class="button button-small"><xsl:value-of select="count(URI)"/> »</a>
                </td>
                <td align="center">
                  <xsl:if test="$maxUniq=COUNT">
                    <xsl:if test="COUNT&gt;0">
                      <xsl:attribute name="style">background-color:#efe;color:#0f0;font-weight:bold;</xsl:attribute>
                    </xsl:if>
                  </xsl:if>
                  <xsl:if test="$minUniq=COUNT">
                    <xsl:if test="COUNT&gt;0">
                      <xsl:attribute name="style">background-color:#fee;color:#f00;font-weight:bold;</xsl:attribute>
                    </xsl:if>
                  </xsl:if>
                  <xsl:value-of select="COUNT"/>
                </td>
              </tr>
              <tr id="extend{position()}" style="display:none;">
                <xsl:if test="position() mod 2 = 1">
                  <xsl:attribute name="class">alternate</xsl:attribute>
                </xsl:if>
                <td colspan="4">
                  <xsl:for-each select="URI">
                    <div style="margin-left:100px;border-bottom: 1px solid #CCC;"><xsl:value-of select="position()"/>. <a href="{.}" target="_blank">
                    <xsl:choose>
                      <xsl:when test=".='/'">
                        <xsl:value-of select="//REPORT/TRANSLATE/MAINPAGE"/>
                      </xsl:when>
                      <xsl:otherwise>
                        <xsl:value-of select="."/>
                      </xsl:otherwise>
                    </xsl:choose>
                    </a></div>
                  </xsl:for-each>
                </td>
              </tr>
              <tr>
                <xsl:if test="position() mod 2 = 1">
                  <xsl:attribute name="class">alternate</xsl:attribute>
                </xsl:if>
                <xsl:if test="$maxUniq&gt;0">
                  <td colspan="4" style="padding: 0 8px 3px 8px;">
                    <div class="progress"><div class="percent"><xsl:value-of select='format-number(COUNT * 100 div sum(//REPORT/INDICATORS/INDICATOR/COUNT),"#.##")'/>%</div><div class="bar">
                      <xsl:attribute name="style">width:<xsl:value-of select='COUNT * 100 div sum(//REPORT/INDICATORS/INDICATOR/COUNT)'/>%</xsl:attribute>
                    </div></div>
                  </td>
                </xsl:if>
              </tr>
            </xsl:if>
          </xsl:if>
        </xsl:for-each>
      </tbody>
    </table>

    <xsl:call-template name="pagination">
  		<xsl:with-param name="currentPage" select="//REPORT/INDICATORS/CURRENT_PAGE"/>
  		<xsl:with-param name="recordsPerPage" select="//REPORT/INDICATORS/PER_PAGE" />
  		<xsl:with-param name="records" select="//REPORT/INDICATORS/INDICATOR"/>
  	</xsl:call-template>

  </xsl:template>
</xsl:stylesheet>