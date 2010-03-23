<div class="titrePage">
  <h2>{'Site manager'|@translate}</h2>
</div>

{if not empty($remote_output)}
<div class="remoteOutput">
  <ul>
    {foreach from=$remote_output item=remote_line}
    <li class="{$remote_line.CLASS}">{$remote_line.CONTENT}</li>
    {/foreach}
  </ul>
</div>
{/if}

{if isset($local_listing)}
{'A local listing.xml file has been found for'|@translate} {$local_listing.URL}
{if isset($local_listing.CREATE)}
<form action="{$F_ACTION}" method="post">
  <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}" />
  <p>
    {'Create this site'|@translate}:
    <input type="hidden" name="no_check" value="1">
    <input type="hidden" name="galleries_url" value="{$local_listing.URL}">
    <input type="submit" name="submit" value="{'Submit'|@translate}" {$TAG_INPUT_ENABLED}>
  </p>
</form>
{/if}
{if isset($local_listing.U_SYNCHRONIZE)}
&nbsp;<a href="{$local_listing.U_SYNCHRONIZE}" title="{'synchronize'|@translate}">{'synchronize'|@translate}</a>
<br><br>
{/if}
{/if}

{if not empty($sites)}
<table class="table2">
	<tr class="throw">
		<td>{'Local'|@translate} / {'Remote'|@translate}</td>
		<td>{'Actions'|@translate}</td>
	</tr>
  {foreach from=$sites item=site name=site}
  <tr style="text-align:left" class="{if $smarty.foreach.site.index is odd}row1{else}row2{/if}"><td>
    <a href="{$site.NAME}">{$site.NAME}</a><br>({$site.TYPE}, {$site.CATEGORIES} {'Categories'|@translate}, {$pwg->l10n_dec('%d image','%d images',$site.IMAGES)})
  </td><td>
    [<a href="{$site.U_SYNCHRONIZE}" title="{'synchronize'|@translate}">{'synchronize'|@translate}</a>]
    {if isset($site.U_DELETE)}
      [<a href="{$site.U_DELETE}" onclick="return confirm('{'Are you sure?'|@translate|escape:'javascript'}');"
                title="{'delete'|@translate}" {$TAG_INPUT_ENABLED}>{'delete'|@translate}</a>]
    {/if}
    {if isset($site.remote)}
      <br>
      [<a href="{$site.remote.U_TEST}" title="{'test'|@translate}" {$TAG_INPUT_ENABLED}>{'test'|@translate}</a>]
      [<a href="{$site.remote.U_GENERATE}" title="{'generate listing'|@translate}" {$TAG_INPUT_ENABLED}>{'generate listing'|@translate}</a>]
      [<a href="{$site.remote.U_CLEAN}" title="{'clean'|@translate}" {$TAG_INPUT_ENABLED}>{'clean'|@translate}</a>]
    {/if}
    {if not empty($site.plugin_links)}
        <br>
      {foreach from=$site.plugin_links item=plugin_link}
        [<a href="{$plugin_link.U_HREF}" title='{$plugin_link.U_HINT}' {$TAG_INPUT_ENABLED}>{$plugin_link.U_CAPTION}</a>]
      {/foreach}
    {/if}
  </td></tr>
  {/foreach}
</table>
{/if}

<form action="{$F_ACTION}" method="post">
  <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}" />
  <p>
    <label for="galleries_url" >{'Create a new site : (give its URL to create_listing_file.php)'|@translate}</label>
    <input type="text" name="galleries_url" id="galleries_url">
  </p>
  <p>
    <input class="submit" type="submit" name="submit" value="{'Submit'|@translate}" {$TAG_INPUT_ENABLED}>
  </p>
</form>
