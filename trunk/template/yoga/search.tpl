{* $Id$ *}
<div id="content">

  <div class="titrePage">
    <ul class="categoryActions">
      <li><a href="{$U_HELP}" onclick="popuphelp(this.href); return false;" title="{'Help'|@translate}" rel="nofollow"><img src="{$themeconf.icon_dir}/help.png" class="button" alt="(?)"></a></li>
      <li><a href="{$U_HOME}" title="{'return to homepage'|@translate}" rel="home"><img src="{$themeconf.icon_dir}/home.png" class="button" alt="{'home'|@translate}"/></a></li>
    </ul>
    <h2>{'Search'|@translate}</h2>
  </div>

{if isset($errors) }
<div class="errors">
  <ul>
    {foreach from=$errors item=error}
    <li>{$error}</li>
    {/foreach}
  </ul>
</div>
{/if}

<form class="filter" method="post" name="search" action="{$F_SEARCH_ACTION}">
<fieldset>
  <legend>{'Filter'|@translate}</legend>
  <label>{'search_keywords'|@translate}
    <input type="text" style="width: 300px" name="search_allwords" size="30"  />
  </label>
  <ul>
    <li><label>
      <input type="radio" name="mode" value="AND" checked="checked" />{'search_mode_and'|@translate}
    </label></li>
    <li><label>
      <input type="radio" name="mode" value="OR" />{'search_mode_or'|@translate}
    </label></li>
  </ul>
  <label>{'search_author'|@translate}
    <input type="text" style="width: 300px" name="search_author" size="30"  />
  </label>
</fieldset>

{if isset($TAG_SELECTION)}
<fieldset>
  <legend>{'Search tags'|@translate}</legend>
  {$TAG_SELECTION}
  <label><span><input type="radio" name="tag_mode" value="AND" checked="checked" /> {'All tags'|@translate}</span></label>
  <label><span><input type="radio" name="tag_mode" value="OR" /> {'Any tag'|@translate}</span></label>
</fieldset>
{/if}

<fieldset>
  <legend>{'search_date'|@translate}</legend>
  <ul>
    <li><label>{'search_date_type'|@translate}</label></li>
    <li><label>
      <input type="radio" name="date_type" value="date_creation" checked="checked" />{'Creation date'|@translate}
    </label></li>
    <li><label>
      <input type="radio" name="date_type" value="date_available" />{'Post date'|@translate}
    </label></li>
  </ul>
  <ul>
    <li><label>{'search_date_from'|@translate}</label></li>
    <li>
      <select name="start_day">
          <option value="0">--</option>
        {section name=day start=1 loop=31}
          <option value="{$smarty.section.day.index}" {if $smarty.section.day.index==$START_DAY_SELECTED}selected="selected"{/if}>{$smarty.section.day.index}</option>
        {/section}
      </select>
      <select name="start_month">
        {html_options options=$month_list selected=$START_MONTH_SELECTED}
      </select>
      <input name="start_year" type="text" size="4" maxlength="4" >
    </li>
    <li>
      <a href="#" onClick="document.search.start_day.value={$smarty.now|date_format:"%d"};document.search.start_month.value={$smarty.now|date_format:"%m"};document.search.start_year.value={$smarty.now|date_format:"%Y"};return false;">{'today'|@translate}</a>
    </li>
  </ul>
  <ul>
    <li><label>{'search_date_to'|@translate}</label></li>
    <li>
      <select name="end_day">
          <option value="0">--</option>
        {section name=day start=1 loop=31}
          <option value="{$smarty.section.day.index}" {if $smarty.section.day.index==$END_DAY_SELECTED}selected="selected"{/if}>{$smarty.section.day.index}</option>
        {/section}
      </select>
      <select name="end_month">
        {html_options options=$month_list selected=$END_MONTH_SELECTED}
      </select>
      <input name="end_year" type="text" size="4" maxlength="4" >
    </li>
    <li>
      <a href="#" onClick="document.search.end_day.value={$smarty.now|date_format:"%d"};document.search.end_month.value={$smarty.now|date_format:"%m"};document.search.end_year.value={$smarty.now|date_format:"%Y"};return false;">{'today'|@translate}</a>
    </li>
  </ul>
</fieldset>

<fieldset>
  <legend>{'search_options'|@translate}</legend>
  <label>{'search_categories'|@translate}
    <select class="categoryList" name="cat[]" multiple="multiple" >
      {html_options options=$category_options selected=$category_options_selected}
    </select>
  </label>
  <ul>
    <li><label>{'search_subcats_included'|@translate}</label></li>
    <li><label>
      <input type="radio" name="subcats-included" value="1" checked="checked" />{'yes'|@translate}
    </label></li>
    <li><label>
      <input type="radio" name="subcats-included" value="0" />{'no'|@translate}
    </label></li>
  </ul>
</fieldset>
<p>
  <input class="submit" type="submit" name="submit" value="{'submit'|@translate}" />
  <input class="submit" type="reset" value="{'reset'|@translate}" />
</p>
</form>

<script type="text/javascript"><!--
document.search.search_allwords.focus();
//--></script>

</div> <!-- content -->
