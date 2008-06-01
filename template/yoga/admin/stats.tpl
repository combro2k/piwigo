{* $Id$ *}

<div class="titrePage">
  {$TABSHEET}
  <ul class="categoryActions">
    <li>
      <a
        href="{$U_HELP}"
        onclick="popuphelp(this.href); return false;"
        title="{'Help'|@translate}"
      >
        <img src="{$themeconf.icon_dir}/help.png" class="button" alt="(?)">
      </a>
    </li>
  </ul>
  <h2>{'History'|@translate} {$TABSHEET_TITLE}</h2>
</div>

<h3>{$L_STAT_TITLE}</h3>

<table class="table2" id="dailyStats">
	<tr class="throw">
		<th>{$PERIOD_LABEL}</th>
		<th>{'Pages seen'|@translate}</th>
		<th></th>
	</tr>

{if not empty($statrows)}
{foreach from=$statrows item=row}
	<tr>
		<td style="white-space: nowrap">{$row.VALUE}</td>
		<td class="number">{$row.PAGES}</td>
		<td><div class="statBar" style="width:{$row.WIDTH}px"></div></td>
	</tr>
{/foreach}
{/if}

</table>
