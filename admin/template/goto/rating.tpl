{* $Id$ *}
<h2>{'Rating'|@translate} [{$NB_ELEMENTS} {'elements'|@translate}]</h2>

<form action="{$F_ACTION}" method="GET" id="update" class="filter">
  <fieldset>
    <legend>{'Filter'|@translate}</legend>

    <label>
      {'Sort by'|@translate}
      <select name="order_by">
        {html_options options=$order_by_options selected=$order_by_options_selected}
      </select>
    </label>

    <label>
      {'Users'|@translate}
      <select name="users">
        {html_options options=$user_options selected=$user_options_selected}
      </select>
    </label>

    <label>
      {'Number of items'|@translate}
      <input type="text" name="display" size="2" value="{$DISPLAY}">
    </label>

    <label>
      &nbsp;
    <input class="submit" type="submit" name="submit_filter" value="{'Submit'|@translate}" />
    </label>
    <input type="hidden" name="page" value="rating" />
  </fieldset>
</form>

<div class="navigationBar">{$NAVBAR}</div>
<table width="99%">
<tr class="throw">
  <td>{'File'|@translate}</td>
  <td>{'Number of rates'|@translate}</td>
  <td>{'Average rate'|@translate}</td>
  <td>{'Controversy'|@translate}</td>
  <td>{'Sum of rates'|@translate}</td>
  <td>{'Rate'|@translate}</td>
  <td>{'Username'|@translate}</td>
  <td>{'Rate date'|@translate}</td>
  <td></td>
</tr>
{foreach from=$images item=image name=image}
<tr valign="top" class="{if $smarty.foreach.image.index is odd}row1{else}row2{/if}">
  <td rowspan="{$image.NB_RATES_TOTAL+1}"><a href="{$image.U_URL}"><img src="{$image.U_THUMB}" alt="{$image.FILE}" title="{$image.FILE}"></a></td>
  <td rowspan="{$image.NB_RATES_TOTAL+1}"><strong>{$image.NB_RATES}/{$image.NB_RATES_TOTAL}</strong></td>
  <td rowspan="{$image.NB_RATES_TOTAL+1}"><strong>{$image.AVG_RATE}</strong></td>
  <td rowspan="{$image.NB_RATES_TOTAL+1}"><strong>{$image.STD_RATE}</strong></td>
  <td rowspan="{$image.NB_RATES_TOTAL+1}" style="border-right: 1px solid;" ><strong>{$image.SUM_RATE}</strong></td>
</tr>
{foreach from=$image.rates item=rate name=rate}
<tr class="{if ($smarty.foreach.image.index+$smarty.foreach.rate.index) is odd}row1{else}row2{/if}">
    <td>{$rate.RATE}</td>
    <td><b>{$rate.USER}</b></td>
    <td><span class="date">{$rate.DATE}</span></td>
    <td><a href="{$rate.U_DELETE}" {$TAG_INPUT_ENABLED}><img src="{$themeconf.admin_icon_dir}/delete.png" class="button" style="border:none;vertical-align:middle; margin-left:5px;" alt="[{'delete'|@translate}]"/></a></td>
</tr>
{/foreach} {*rates*}
{/foreach} {*images*}
</table>

<div class="navigationBar">{$NAVBAR}</div>
