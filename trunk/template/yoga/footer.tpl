{* $Id$ *}
<div id="copyright">
 <a name="EoP"></a> <!-- End of Page -->
 {if isset($debug.TIME) }
 {'generation_time'|@translate} {$debug.TIME} ({$debug.NB_QUERIES} {'sql_queries_in'|@translate} {$debug.SQL_TIME}) -
 {/if}

 {* Please, do not remove this copyright. If you really want to,
      contact us on http://phpwebgallery.net to find a solution on how
      to show the origin of the script...
  *}

  {'powered_by'|@translate}
  <a href="http://www.phpwebgallery.net" class="PWG">
  <span class="P">Php</span><span class="W">Web</span><span class="G">Gallery</span></a>
  {$VERSION}
  {if isset($CONTACT_MAIL)}
  - {'send_mail'|@translate}
  <a href="mailto:{$CONTACT_MAIL}?subject={'title_send_mail'|@translate|@escape:url}">{'Webmaster'|@translate}</a>
  {/if}

</div> <!-- copyright -->
{if isset($footer_elemets)}
{foreach from=$footer_elements item=v}
{$v}
{/foreach}
{/if}
</div> <!-- the_page -->

{if isset($debug.QUERIES_LIST)}{$debug.QUERIES_LIST}{/if}
</body>
</html>