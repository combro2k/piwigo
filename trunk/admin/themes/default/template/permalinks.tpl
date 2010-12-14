<div class="titrePage">
  <h2>{'Permalinks'|@translate}</h2>
</div>

<form method="post" action="">
<fieldset><legend>{'Add/delete a permalink'|@translate}</legend>
  <label>{'Album'|@translate}:
    <select name="cat_id">
      <option value="0">------</option>
      {html_options options=$categories selected=$categories_selected}
    </select>
  </label>

  <label>{'Permalink'|@translate}:
    <input name="permalink">
  </label>

  <label>{'Save to permalink history'|@translate}:
    <input type="checkbox" name="save" checked="checked">
  </label>

  <p>
    <input type="submit" class="submit" name="set_permalink" value="{'Submit'|@translate}">
  </p>
  </fieldset>
</form>

<h3>{'Permalinks'|@translate}</h3>
<table class="table2">
	<tr class="throw">
		<td>Id {$SORT_ID}</td>
		<td>{'Album'|@translate} {$SORT_NAME}</td>
		<td>{'Permalink'|@translate} {$SORT_PERMALINK}</td>
	</tr>
{foreach from=$permalinks item=permalink name="permalink_loop"}
	<tr class="{if $smarty.foreach.permalink_loop.index is odd}row1{else}row2{/if}" style="line-height:1.5em;">
		<td style="text-align:center;">{$permalink.id}</td>
		<td>{$permalink.name}</td>
		<td>{$permalink.permalink}</td>
	</tr>
{/foreach}
</table>

<h3>{'Permalink history'|@translate} <a name="old_permalinks"></a></h3>
<table class="table2">
	<tr class="throw">
		<td>Id {$SORT_OLD_CAT_ID}</td>
		<td>{'Album'|@translate}</td>
		<td>{'Permalink'|@translate} {$SORT_OLD_PERMALINK}</td>
		<td>{'Deleted on'|@translate} {$SORT_OLD_DATE_DELETED}</td>
		<td>{'Last hit'|@translate} {$SORT_OLD_LAST_HIT}</td>
		<td style="width:20px;">{'Hit'|@translate} {$SORT_OLD_HIT}</td>
		<td style="width:5px;"></td>
	</tr>
{foreach from=$deleted_permalinks item=permalink}
	<tr style="line-height:1.5em;">
		<td style="text-align:center;">{$permalink.cat_id}</td>
		<td>{$permalink.name}</td>
		<td>{$permalink.permalink}</td>
		<td>{$permalink.date_deleted}</td>
		<td>{$permalink.last_hit}</td>
		<td>{$permalink.hit}</td>
		<td><a href="{$permalink.U_DELETE}"><img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/delete.png" alt="[{'Delete'|@translate}]" class="button"></a></td>
	</tr>
{/foreach}
</table>
