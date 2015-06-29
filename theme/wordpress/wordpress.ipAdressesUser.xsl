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
          <th class="manage-column" style="text-align:center;"><xsl:value-of select="//REPORT/TRANSLATE/IP"/></th>
          <th class="manage-column" style="text-align:center;width:150px;"><xsl:value-of select="//REPORT/TRANSLATE/VIEW"/></th>
        </tr>
      </thead>
      <tfoot>
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
                  <a onclick="getExtendInfo(this,'extend{position()}','{IP}');return false;" href="" class="button button-small"><xsl:value-of select="IP"/> » <span class="spinner" style="margin: 1px 10px 0;"></span></a>
                </td>
                <td align="center">
                  <xsl:value-of select="COUNT"/>
                </td>
              </tr>
              <tr id="extend{position()}" style="display:none;">
                <xsl:if test="position() mod 2 = 1">
                  <xsl:attribute name="class">alternate</xsl:attribute>
                </xsl:if>
                <td colspan="3" id="extend{position()}ajax"></td>
              </tr>
              <tr>
                <xsl:if test="position() mod 2 = 1">
                  <xsl:attribute name="class">alternate</xsl:attribute>
                </xsl:if>
                <xsl:if test="$maxUniq&gt;0">
                  <td colspan="3" style="padding: 0 8px 3px 8px;">
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
    <script><![CDATA[
      function getExtendInfo(btn,ext,ip){
        if(jQuery('#'+ext+'ajax').html()==''){
          jQuery(btn).children('.spinner').show();
          var ddt = jQuery('#dataselectrange').data('range').split(' - ');
          loadDate(']]><xsl:value-of select="//REPORT/REPORT"/><![CDATA[',ddt[0],ddt[1],{ip:ip},function(data){
            jQuery(btn).children('.spinner').hide();
            if(data.success){
              var text = '<table style="margin-left:150px;width:inherit;" class="widefat">';
              var i = 0;
              jQuery.each(data.whois,function(name,value){
                text+= '<tr'+(i%2==0?' class="alternate"':'')+'><td><b>'+name+'</b></td><td>'+value+'</td></tr>';
                i++;
              });
              text+= '</table>';
              jQuery('#'+ext+'ajax').html(text);
              jQuery('#'+ext).toggle();
              return false;
            }
          });
        }else{
          jQuery('#'+ext).toggle();
        }
      }
    ]]></script>

    <xsl:call-template name="pagination">
  		<xsl:with-param name="currentPage" select="//REPORT/INDICATORS/CURRENT_PAGE"/>
  		<xsl:with-param name="recordsPerPage" select="//REPORT/INDICATORS/PER_PAGE" />
  		<xsl:with-param name="records" select="//REPORT/INDICATORS/INDICATOR"/>
  	</xsl:call-template>

  </xsl:template>
</xsl:stylesheet>