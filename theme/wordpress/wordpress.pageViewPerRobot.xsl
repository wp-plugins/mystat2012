<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:import href="wordpress.xsl" />
  <xsl:output method="html"/>
  <xsl:template name="content">
    <xsl:call-template name="graphic"/>
    <table class="widefat">
      <thead>
        <tr>
          <th class="manage-column" style="text-align:center;"><xsl:value-of select="//REPORT/TRANSLATE/VIEW"/></th>
          <th class="manage-column" style="text-align:center;width:150px;"><xsl:value-of select="//REPORT/TRANSLATE/ROBOT"/></th>
        </tr>
      </thead>
      <tfoot>
        <xsl:if test="//REPORT/INDICATORS/ALL_ROBOT&gt;0">
          <tr class="alternate">
            <th><xsl:value-of select="//REPORT/TRANSLATE/AVERAGE"/></th>
            <th style="text-align:center;"><xsl:value-of select="format-number(//REPORT/INDICATORS/TOTAL_VIEW div //REPORT/INDICATORS/ALL_ROBOT,'#.##')"/></th>
          </tr>
        </xsl:if>
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
          <tr>
            <xsl:if test="position() mod 2 = 1">
              <xsl:attribute name="class">alternate</xsl:attribute>
            </xsl:if>
            <td><xsl:value-of select="NAME"/></td>
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
          <tr>
            <xsl:if test="position() mod 2 = 1">
              <xsl:attribute name="class">alternate</xsl:attribute>
            </xsl:if>
            <xsl:if test="$maxUniq&gt;0">
              <td colspan="2" style="padding: 0 8px 3px 8px;">
                <div class="progress"><div class="percent"><xsl:value-of select='format-number(COUNT * 100 div sum(//REPORT/INDICATORS/INDICATOR/COUNT),"#.##")'/>%</div><div class="bar">
                  <xsl:attribute name="style">width:<xsl:value-of select='COUNT * 100 div sum(//REPORT/INDICATORS/INDICATOR/COUNT)'/>%</xsl:attribute>
                </div></div>
              </td>
            </xsl:if>
          </tr>
        </xsl:for-each>
      </tbody>
    </table>
  </xsl:template>
  <xsl:template name="graphicjsondata">
    [
      <xsl:if test="count(//REPORT/INDICATORS/INDICATOR) > 0">
        <xsl:for-each select="//REPORT/INDICATORS/INDICATOR">
          ["<xsl:call-template name="escapeQuote"><xsl:with-param name="pText" select="NAME"/></xsl:call-template>",<xsl:value-of select="COUNT"/>]<xsl:if test="position() != last()">,</xsl:if>
        </xsl:for-each>
      </xsl:if>
    ]
  </xsl:template>
  <xsl:template name="graphic">
    <div id="mystat_graphic" class="postbox"></div>
    <script type="text/javascript"><![CDATA[
      if(typeof google != 'undefined' && typeof google.visualization == 'undefined'){
        google.load('visualization', '1.0', {'callback':function(){},'packages':['corechart'], 'language':']]><xsl:value-of select="//REPORT/LANGUAGE"/><![CDATA['});
        google.setOnLoadCallback(viewChart);
      }
      function viewChart(){
        if(typeof google == 'undefined' || typeof google.visualization == 'undefined' || typeof google.visualization.DataTable == 'undefined'){return;}
        var data = new google.visualization.DataTable();
        data.addColumn('string', '');
        data.addColumn('number', "]]><xsl:call-template name="escapeQuote"><xsl:with-param name="pText" select="//REPORT/TRANSLATE/ROBOT"/></xsl:call-template><![CDATA[");
        data.addRows(]]><xsl:call-template name="graphicjsondata"/><![CDATA[);
        var options = {
          height: 400,
          legend: {
            position: 'none'
          },
          vAxis: {
            format: '#'
          },
          dataOpacity: 0.9,
          theme: 'maximized',
          focusTarget: 'category'
        };
        var chart = new google.visualization.ColumnChart(document.getElementById('mystat_graphic'));
        chart.draw(data, options);
      }
    ]]></script>
  </xsl:template>
</xsl:stylesheet>