{* $Id: /piwigo/trunk/template/yoga/footer.tpl 7037 2009-03-13T23:43:50.771219Z plg  $ *}
<div id="copyright">
 <a name="EoP"></a> <!-- End of Page -->
 {if isset($debug.TIME) }
 {'generation_time'|@translate} {$debug.TIME} ({$debug.NB_QUERIES} {'sql_queries_in'|@translate} {$debug.SQL_TIME}) -
 {/if}

 {* Please, do not remove this copyright. If you really want to,
      contact us on http://piwigo.org to find a solution on how
      to show the origin of the script...
  *}

  {'powered_by'|@translate}
  <a href="{$PHPWG_URL}" class="Piwigo">
  <span class="Piwigo">Piwigo</span></a>
  {$VERSION}
  {if isset($CONTACT_MAIL)}
  - {'send_mail'|@translate}
  <a href="mailto:{$CONTACT_MAIL}?subject={'title_send_mail'|@translate|@escape:url}">{'Webmaster'|@translate}</a>
  {/if}


{if isset($footer_elements)}
{foreach from=$footer_elements item=v}
{$v}
{/foreach}
{/if}
</div> <!-- the_page -->
{if isset($debug.QUERIES_LIST)}
<div id="debug">
{$debug.QUERIES_LIST}
</div>
{/if}
</div> <!-- copyright -->
</body>
</html>