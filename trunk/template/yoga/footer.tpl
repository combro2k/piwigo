<div id="copyright">
 <a name="EoP"></a> <!-- End of Page -->
 {if isset($debug.TIME) }
 {'SQL queries in'|@translate} {$debug.TIME} ({$debug.NB_QUERIES} {'SQL queries in'|@translate} {$debug.SQL_TIME}) -
 {/if}

 {* Please, do not remove this copyright. If you really want to,
      contact us on http://piwigo.org to find a solution on how
      to show the origin of the script...
  *}

  {'Powered by'|@translate}
  <a href="{$PHPWG_URL}" class="Piwigo">
  <span class="Piwigo">Piwigo</span></a>
  {$VERSION}
  {if isset($CONTACT_MAIL)}
  - {'Contact'|@translate}
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