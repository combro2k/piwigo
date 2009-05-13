{* $Id: /piwigo/trunk/admin/template/goto/extend_for_templates.tpl 7055 2009-03-19T19:51:54.545257Z nikrou  $ *}
<div class="titrePage"><h2>{'extend_for_templates'|@translate}</h2>
</div>
{if isset($extents)}
<h4>{'Replacement of original templates'|@translate}</h4>
<form method="post" name="extend_for_templates" id="extend_for_templates" action="">
  <table class="table2">
    <tr class="throw">
      <th>{'Replacers'|@translate}</th>
      <th>{'Original templates'|@translate}</th>
      <th>{'Optional URL keyword'|@translate}</th>
      <th>{'Bound template'|@translate}</th>
    </tr>
    {foreach from=$extents item=tpl name=extent_loop}
    <tr class="{if $smarty.foreach.extent_loop.index is odd}row1{else}row2{/if}">
      <td>
        <input type="hidden" name="reptpl[]" value="{$tpl.replacer}">
        {$tpl.replacer}
      </td>
      <td>
        {html_options name=original[] output=$tpl.original_tpl values=$tpl.original_tpl selected=$tpl.selected_tpl}
      </td>
      <td>
        {html_options name=url[] output=$tpl.url_parameter values=$tpl.url_parameter selected=$tpl.selected_url}
      </td>
      <td>
        {html_options name=bound[] output=$tpl.bound_tpl values=$tpl.bound_tpl selected=$tpl.selected_bound}
      </td>
    </tr>
    {/foreach}
  </table>
  {if !is_adviser()}
  <p>
    <input class="submit" type="submit" value="{'Submit'|@translate}" name="submit">
  </p>
  {/if}
</form>
{/if}
