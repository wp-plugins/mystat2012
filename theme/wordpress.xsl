<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="html"/>
  <xsl:template match="/">
    <style>
      body{cursor:default;}
      .wrap #mystat_graphic{margin-bottom:10px;padding:20px;}
      .wrap .spinner.inline{float: none;margin: -6px 10px;}
      .wrap .wp-filter{margin:0;margin-bottom:10px;}
      .wrap .wp-filter .period{padding:10px 0;line-height:27px;}
      .wrap .wp-filter .period .text{line-height:28px;margin-right:20px;}
      .wrap .pressthis a{cursor:default;}
      .wrap .pressthis a:after{width: 130px;}
      .wrap .postbox-container .hndle{cursor:pointer;}
      .wrap .postbox-container .inside .button{width:100%;}
      .wrap .progress{height: 10px;width: 100%;line-height: 2em;padding: 0;overflow: hidden;border-radius: 22px;background: #DDD;box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);}
      .wrap .progress .percent{position: relative;width: 100%;padding: 0;font-size: 9px;color: #FFF;text-align: center;line-height: 11px;font-weight: 400;text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);}
      .wrap .progress .bar{width: 0;height: 100%;margin-top: -11px;border-radius: 22px;background-color: #0074A2;box-shadow: inset 0 0 2px rgba(0, 0, 0, 0.3);}
    </style>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <div class="wrap">
      <h2><xsl:value-of select="//REPORT/TITLE"/></h2>
      <p class="pressthis"><a><span><xsl:value-of select="//REPORT/SUBTITLE"/></span></a></p>
      
      <div id="poststuff">
        <div id="post-body" class="columns-2">
          <div id="post-body-content" style="position: relative;">
            <div class="wp-filter">
              <div class="period"><span class="text"><xsl:value-of select="//REPORT/TRANSLATE/PERIODREPORT"/></span> <a class="button" data-range="{//REPORT/PERIOD/START} - {//REPORT/PERIOD/END}" id="dataselectrange"><span class="data"><xsl:value-of select="//REPORT/PERIOD/START"/> - <xsl:value-of select="//REPORT/PERIOD/END"/></span> <span class="spinner"></span></a></div>
            </div>
            <xsl:call-template name="content"/>
          </div>
          <div id="postbox-container-1" class="postbox-container">
            <div id="side-sortables" class="meta-box-sortables ui-sortable" style="">
              <xsl:call-template name="menu"/>
            </div>
          </div>
        </div>    
      </div>
    </div>
    <script type="text/javascript"><![CDATA[
      function loadDate(report,dateStart,dateEnd){
        jQuery.ajax({
          url: ajaxurl,
          data: {
            action: 'mystat',
            report: report,
            datestart: dateStart,
            dateend: dateEnd
          },
          dataType: 'html',
          type: 'POST',
          success: function(data, textStatus){
            jQuery('#mystat').html(data);
            jQuery(document).scrollTop(0);
            if(typeof viewChart !='undefined'){
              viewChart();
            }
          },
          error: function(){
            document.location.reload();
          }
        });
      }
      function selectMenu(el,report){
        var ddt = jQuery('#dataselectrange').data('range').split(' - ');
        jQuery(el).width(jQuery(el).width()-35);
        jQuery(el).parent().children('.spinner').show();
        loadDate(report,ddt[0],ddt[1]);
      }
      jQuery(document).ready(function($){
        $('.wrap .postbox-container .hndle, .wrap .postbox-container .handlediv').click(function(){
          $(this).parent('.postbox').toggleClass('closed');
        });
        $('#dataselectrange').dateRangePicker({
          shortcuts: {
            'next-days': null,
            'next': null,
            'prev-days': [1,7,30],
            'prev' : ['week','month']
          },
          separator: ' - ',
          language: ']]><xsl:value-of select="translate(//REPORT/LANGUAGE,'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz')"/><![CDATA[',
          format: 'DD.MM.YYYY',
          endDate: new Date(),
          showPrevMonth: true,
          startOfWeek: 'monday',
          minDays: 1,
          maxDays: 365,
          getValue: function(){
            return $('#dataselectrange .data').html();
          },
          setValue: function(s){
            $('#dataselectrange .data').html(s);
          }
        }).bind('datepicker-close',function(event,obj){
          if(obj.value!=$('#dataselectrange').attr('data-range')){
            $('#dataselectrange .spinner').show();
            loadDate(']]><xsl:value-of select="//REPORT/REPORT"/><![CDATA[',moment(obj.date1).format('DD.MM.YYYY'),moment(obj.date2).format('DD.MM.YYYY'));
          }
        });
      });
    ]]></script>
  </xsl:template>
  <xsl:template name="content"></xsl:template>
  <xsl:template name="menu">
    <xsl:for-each select="//REPORT/MENU">
      <div class="postbox">
        <div class="handlediv"><br/></div>
        <h3 class="hndle"><span><xsl:value-of select="TITLE"/></span></h3>
        <div class="inside">
          <ul>
            <xsl:for-each select="ITEM">
            <li>
              <div>
              <span class="spinner"></span>
              <a class="button button-small" onclick="selectMenu(this,'{@code}');return false;">
                <xsl:if test="//REPORT/REPORT = @code">
                  <xsl:attribute name="class">button button-small button-primary</xsl:attribute>
                </xsl:if>
                <xsl:value-of select="."/>
              </a>
              </div>
            </li>
            </xsl:for-each>
          </ul>
        </div>
      </div>
    </xsl:for-each>
  </xsl:template>
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
</xsl:stylesheet>