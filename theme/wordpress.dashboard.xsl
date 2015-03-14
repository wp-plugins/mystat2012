<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:import href="wordpress.xsl" />
  <xsl:output method="html"/>
  <xsl:template name="content">
    <xsl:call-template name="graphic"/>
    <table class="widefat">
      <thead>
        <tr>
        <th class="manage-column"></th>
        <th class="manage-column" style="text-align:center;width:150px"><xsl:value-of select="//REPORT/TRANSLATE/VISITORROBO"/></th>
        <th class="manage-column" style="text-align:center;width:150px"><xsl:value-of select="//REPORT/TRANSLATE/VISITORUNIQ"/></th>
        <th class="manage-column" style="text-align:center;width:150px"><xsl:value-of select="//REPORT/TRANSLATE/VISITORVIEW"/></th>
        </tr>
      </thead>
      <tfoot>
      </tfoot>
      <tbody>
        <xsl:variable name="maxUniq">
          <xsl:call-template name="maximum">
            <xsl:with-param name="pSequence" select="//REPORT/INDICATORS/INDICATOR/UNIQ"/>
          </xsl:call-template>
        </xsl:variable>
        <xsl:variable name="minUniq">
          <xsl:call-template name="minimum">
            <xsl:with-param name="pSequence" select="//REPORT/INDICATORS/INDICATOR/UNIQ"/>
          </xsl:call-template>
        </xsl:variable>
        <xsl:for-each select="//REPORT/INDICATORS/INDICATOR">
          <tr>
            <xsl:if test="position() mod 2 = 1">
              <xsl:attribute name="class">alternate</xsl:attribute>
            </xsl:if>
            <xsl:if test="//REPORT/PERIOD/START != //REPORT/PERIOD/END">
               <xsl:attribute name="style">cursor:pointer;</xsl:attribute>
               <xsl:attribute name="onclick">showDayStat(this,<xsl:value-of select="TIMESTAMP"/>);return false;</xsl:attribute>
            </xsl:if>
            <td>
              <xsl:if test="HOLIDAY = 1">
                <xsl:attribute name="style">color:red;</xsl:attribute>
              </xsl:if>
              <xsl:value-of select="NAME"/>
              <span class="spinner inline"></span>
            </td>
            <td align="center"><xsl:value-of select="ROBOT"/></td>
            <td align="center">
              <xsl:if test="$maxUniq=UNIQ">
                <xsl:if test="UNIQ&gt;0">
                  <xsl:attribute name="style">background-color:#efe;color:#0f0;font-weight:bold;</xsl:attribute>
                </xsl:if>
              </xsl:if>
              <xsl:if test="$minUniq=UNIQ">
                <xsl:if test="UNIQ&gt;0">
                  <xsl:attribute name="style">background-color:#fee;color:#f00;font-weight:bold;</xsl:attribute>
                </xsl:if>
              </xsl:if>
              <xsl:value-of select="UNIQ"/>
            </td>
            <td align="center"><xsl:value-of select="COUNT"/></td>
          </tr>
          <tr>
            <xsl:if test="position() mod 2 = 1">
              <xsl:attribute name="class">alternate</xsl:attribute>
            </xsl:if>
            <xsl:if test="$maxUniq&gt;0">
              <td colspan="4" style="padding: 0 8px 3px 8px;">
                <div class="progress"><div class="percent"><xsl:value-of select='round((UNIQ * 100 div sum(//REPORT/INDICATORS/INDICATOR/UNIQ))*100) div 100'/>%</div><div class="bar">
                  <xsl:attribute name="style">width:<xsl:value-of select='UNIQ * 100 div sum(//REPORT/INDICATORS/INDICATOR/UNIQ)'/>%</xsl:attribute>
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
          [new Date((<xsl:value-of select="TIMESTAMP"/> + (new Date().getTimezoneOffset() * 60)) * 1000),<xsl:value-of select="ROBOT"/>,<xsl:value-of select="UNIQ"/>,<xsl:value-of select="COUNT"/>]<xsl:if test="position() != last()">,</xsl:if>
        </xsl:for-each>
      </xsl:if>
    ]
  </xsl:template>
  <xsl:template name="graphic">
    <script type="text/javascript"><![CDATA[
      if(typeof google != 'undefined'){
        google.load('visualization', '1.0', {'packages':['corechart'], 'language':']]><xsl:value-of select="//REPORT/LANGUAGE"/><![CDATA['});
      }
      function viewChart(){
        if(typeof google == 'undefined'){return;}
        var data = new google.visualization.DataTable();
        data.addColumn('datetime', '');
        data.addColumn('number', "]]><xsl:call-template name="escapeQuote"><xsl:with-param name="pText" select="//REPORT/TRANSLATE/VISITORROBO"/></xsl:call-template><![CDATA[");
        data.addColumn('number', "]]><xsl:call-template name="escapeQuote"><xsl:with-param name="pText" select="//REPORT/TRANSLATE/VISITORUNIQ"/></xsl:call-template><![CDATA[");
        data.addColumn('number', "]]><xsl:call-template name="escapeQuote"><xsl:with-param name="pText" select="//REPORT/TRANSLATE/VISITORVIEW"/></xsl:call-template><![CDATA[");
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
      function showDayStat(el,time){
        jQuery(el).children().children('.spinner.inline').css('display','inline-block');
        loadDate(']]><xsl:value-of select="//REPORT/REPORT"/><![CDATA[',moment(new Date((time + (new Date().getTimezoneOffset() * 60)) * 1000)).format('DD.MM.YYYY'),moment(new Date((time + (new Date().getTimezoneOffset() * 60)) * 1000)).format('DD.MM.YYYY'));
      }
      jQuery(document).ready(function($){
        viewChart();
        $(window).load(function(){
          viewChart();
        });
        $(window).resize(function(){
          viewChart();
        });
      });
    ]]></script>
    <div id="mystat_graphic" class="postbox"></div>
  </xsl:template>
</xsl:stylesheet>