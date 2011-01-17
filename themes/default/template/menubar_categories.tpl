<dt>
  {if isset($U_START_FILTER)}
  <a href="{$U_START_FILTER}" title="{'display only recently posted photos'|@translate}" rel="nofollow"><img src="{$ROOT_URL}{$themeconf.icon_dir}/start_filter.png" class="button" alt="start filter"></a>
  {/if}
  {if isset($U_STOP_FILTER)}
  <a href="{$U_STOP_FILTER}" title="{'return to the display of all photos'|@translate}"><img src="{$ROOT_URL}{$themeconf.icon_dir}/stop_filter.png" class="button" alt="stop filter"></a>
  {/if}
	<a href="{$block->data.U_CATEGORIES}">{'Albums'|@translate}</a>
</dt>
<dd>
{assign var='ref_level' value=0}
{foreach from=$block->data.MENU_CATEGORIES item=cat}
  {if $cat.LEVEL > $ref_level}
  <ul>
  {else}
    </li>
    {'</ul></li>'|@str_repeat:$ref_level-$cat.LEVEL}
  {/if}
    <li {if $cat.SELECTED}class="selected"{/if}>
      <a href="{$cat.URL}" {if $cat.IS_UPPERCAT}rel="up"{/if} title="{$cat.TITLE}">{$cat.NAME}</a>
      {if $cat.count_images > 0}
      <span class="{if $cat.nb_images > 0}menuInfoCat{else}menuInfoCatByChild{/if}" title="{$cat.TITLE}">[{$cat.count_images}]</span>
      {/if}
      {if !empty($cat.icon_ts)}
      <img title="{$cat.icon_ts.TITLE}" src="{$ROOT_URL}{$themeconf.icon_dir}/recent{if $cat.icon_ts.IS_CHILD_DATE}_by_child{/if}.png" class="icon" alt="(!)">
      {/if}
  {assign var='ref_level' value=$cat.LEVEL}
{/foreach}
{'</li></ul>'|@str_repeat:$ref_level}

	<p class="totalImages">{$pwg->l10n_dec('%d photo', '%d photos', $block->data.NB_PICTURE)}</p>
</dd>
