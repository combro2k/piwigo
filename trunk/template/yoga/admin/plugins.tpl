{* $Id$ *}

<div class="titrePage">
  <h2>{'Plugins'|@translate}
  </h2>
</div>

{if isset($plugins) }
<table class="table2">
<thead><tr class="throw">
  <td>{'Name'|@translate}</td>
  <td>{'Version'|@translate}</td>
  <td>{'Description'|@translate}</td>
  <td>{'Actions'|@translate}</td>
</tr></thead>
{foreach from=$plugins item=plugin name=plugins_loop}
<tr class="{if $smarty.foreach.plugins_loop.index is odd}row1{else}row2{/if}">
  <td>{$plugin.NAME}</td>
  <td>{$plugin.VERSION}</td>
  <td>{$plugin.DESCRIPTION}</td>
  <td>
  {foreach from=$plugin.actions item=action}
  <a href="{$action.U_ACTION}"
{if isset($action.CONFIRM) }
    onclick="return confirm('{'Are you sure?'|@translate|@escape:javascript}');"
{/if}
    {$TAG_INPUT_ENABLED}>{$action.L_ACTION}</a>
  {/foreach}
  </td>
</tr>
{/foreach}
</table>
{/if}