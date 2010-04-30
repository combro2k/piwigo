{include file='include/tag_selection.inc.tpl'}
{include file='include/datepicker.inc.tpl'}

{literal}
<script type="text/javascript">
  pwg_initialization_datepicker("#date_creation_day", "#date_creation_month", "#date_creation_year", "#date_creation_linked_date", "#date_creation_action_set");
</script>
{/literal}

{known_script id="jquery.fcbkcomplete" src=$ROOT_URL|@cat:"themes/default/js/plugins/jquery.fcbkcomplete.js"}
{literal}
<script type="text/javascript">
  $(document).ready(function() {
    $("#tags").fcbkcomplete({
      json_url: "admin.php?fckb_tags=1",
      cache: false,
      filter_case: false,
      filter_hide: true,
      firstselected: true,
      filter_selected: true,
      maxitems: 10,
      newel: true
    });
  });
</script>
{/literal}

<h2>{'Batch management'|@translate}</h2>

<h3>{$CATEGORIES_NAV}</h3>

{if !empty($thumbnails)}
  <p style="text-align:center;">
    {'global mode'|@translate}
    | <a href="{$U_UNIT_MODE}">{'unit mode'|@translate}</a>
  </p>

  <fieldset>

    <legend>{'Display options'|@translate}</legend>

    <p>{'elements per page'|@translate}:
        <a href="{$U_DISPLAY}&amp;display=20">20</a>
      | <a href="{$U_DISPLAY}&amp;display=50">50</a>
      | <a href="{$U_DISPLAY}&amp;display=100">100</a>
      | <a href="{$U_DISPLAY}&amp;display=all">{'all'|@translate}</a>
    </p>

  </fieldset>

  <form action="{$F_ACTION}" method="post">

  <fieldset>

    <legend>{'Elements'|@translate}</legend>

    {if !empty($navbar) }{include file='navigation_bar.tpl'|@get_extent:'navbar'}{/if}

  {if !empty($thumbnails)}
    <ul class="thumbnails">
      {foreach from=$thumbnails item=thumbnail}
      <li><span class="wrap1">
          <label>
            <span class="wrap2">
        {if $thumbnail.LEVEL > 0}
        <em class="levelIndicatorB">{$thumbnail.LEVEL}</em>
        <em class="levelIndicatorF" title="{$pwg->l10n($pwg->sprintf('Level %d',$thumbnail.LEVEL))}">{$thumbnail.LEVEL}</em>
        {/if}
            <span>
              <img src="{$thumbnail.TN_SRC}"
                 alt="{$thumbnail.FILE}"
                 title="{$thumbnail.TITLE}"
                 class="thumbnail">
            </span></span>
            <input type="checkbox" name="selection[]" value="{$thumbnail.ID}">
          </label>
          </span>
      </li>
      {/foreach}
    </ul>
  {/if}

  </fieldset>

  {if isset($show_delete_form) }
  <fieldset>
    <legend>{'Deletions'|@translate}</legend>
    <p style="font-style:italic">{'Note: Only deletes photos added with pLoader'|@translate}</p>
    <p>
      {'target'|@translate}
      <label><input type="radio" name="target_deletion" value="all"> {'all'|@translate}</label>
      <label><input type="radio" name="target_deletion" value="selection" checked="checked"> {'selection'|@translate}</label>
    </p>
    <p>
    <label><input type="checkbox" name="confirm_deletion" value="1"> {'confirm'|@translate}</label>
    <input class="submit" type="submit" value="{'Delete selected photos'|@translate}" name="delete" {$TAG_INPUT_ENABLED}>
    </p>
  </fieldset>
  {/if}

  <fieldset>

    <legend>{'Form'|@translate}</legend>

    <table>

      <tr>
        <td>{'associate to category'|@translate}</td>
        <td>
          <select style="width:400px" name="associate" size="1">
            <option value="0">------------</option>
            {html_options options=$associate_options }
         </select>
        </td>
      </tr>

      <tr>
        <td>{'dissociate from category'|@translate}</td>
        <td>
          <select style="width:400px" name="dissociate" size="1">
            <option value="0">------------</option>
            {if !empty($dissociate_options)}{html_options options=$dissociate_options }{/if}
          </select>
        </td>
      </tr>

      <tr>
        <td>{'add tags'|@translate}</td>
        <td>
<select id="tags" name="add_tags">
</select>
        </td>
      </tr>

      {if !empty($DEL_TAG_SELECTION)}
      <tr>
        <td>{'remove tags'|@translate}</td>
        <td>{$DEL_TAG_SELECTION}</td>
      </tr>
      {/if}

      <tr>
        <td>{'Author'|@translate}</td>
        <td>
          <label><input type="radio" name="author_action" value="leave" checked="checked"> {'leave'|@translate}</label>
          <label><input type="radio" name="author_action" value="unset"> {'unset'|@translate}</label>
          <label><input type="radio" name="author_action" value="set" id="author_action_set"> {'set to'|@translate}</label>
          <input onchange="document.getElementById('author_action_set').checked = true;" type="text" class="large" name="author" value="">
        </td>
      </tr>

      <tr>
        <td>{'title'|@translate}</td>
        <td>
          <label><input type="radio" name="name_action" value="leave" checked="checked"> {'leave'|@translate}</label>
          <label><input type="radio" name="name_action" value="unset"> {'unset'|@translate}</label>
          <label><input type="radio" name="name_action" value="set" id="name_action_set"> {'set to'|@translate}</label>
          <input onchange="document.getElementById('name_action_set').checked = true;" type="text" class="large" name="name" value="">
        </td>
      </tr>

      <tr>
        <td>{'Creation date'|@translate}</td>
        <td>
          <label><input type="radio" name="date_creation_action" value="leave" checked="checked"> {'leave'|@translate}</label>
          <label><input type="radio" name="date_creation_action" value="unset"> {'unset'|@translate}</label>
          <label><input type="radio" name="date_creation_action" value="set" id="date_creation_action_set"> {'set to'|@translate}</label>
          <select id="date_creation_day" name="date_creation_day">
             <option value="0">--</option>
            {section name=day start=1 loop=32}
              <option value="{$smarty.section.day.index}" {if $smarty.section.day.index==$DATE_CREATION_DAY}selected="selected"{/if}>{$smarty.section.day.index}</option>
            {/section}
          </select>
          <select id="date_creation_month" name="date_creation_month">
            {html_options options=$month_list selected=$DATE_CREATION_MONTH}
          </select>
          <input id="date_creation_year"
                 name="date_creation_year"
                 type="text"
                 size="4"
                 maxlength="4"
                 value="{$DATE_CREATION_YEAR}">
          <input id="date_creation_linked_date" name="date_creation_linked_date" type="hidden" size="10" disabled="disabled">
        </td>
      </tr>

    <tr>
      <td>{'Who can see these photos?'|@translate}</td>
      <td>
        <label><input type="radio" name="level_action" value="leave" checked="checked">{'leave'|@translate}</label>
        <label><input type="radio" name="level_action" value="set" id="level_action_set">{'set to'|@translate}</label>
        <select onchange="document.getElementById('level_action_set').checked = true;" name="level" size="1">
          {html_options options=$level_options selected=$level_options_selected}
        </select>
      </td>
    </tr>

    </table>

    <p>
      {'target'|@translate}
      <label><input type="radio" name="target" value="all"> {'all'|@translate}</label>
      <label><input type="radio" name="target" value="selection" checked="checked"> {'selection'|@translate}</label>
    </p>


    <p><input class="submit" type="submit" value="{'Submit'|@translate}" name="submit" {$TAG_INPUT_ENABLED}></p>

  </fieldset>

  <fieldset>

    <legend>{'Caddie management'|@translate}</legend>

    <ul style="list-style-type:none;">
      {if ($IN_CADDIE)}
      <li><label><input type="radio" name="caddie_action" value="empty_all"> {'Empty caddie'|@translate}</label></li>
      <li><label><input type="radio" name="caddie_action" value="empty_selected"> {'Take selected elements out of caddie'|@translate}</label></li>
      {else}
      <li><label><input type="radio" name="caddie_action" value="add_selected"> {'Add selected elements to caddie'|@translate}</label></li>
      {/if}
    </ul>

    <p><input class="submit" type="submit" value="{'Submit'|@translate}" name="submit_caddie"></p>

  </fieldset>

  </form>

{else}
  <div class="infos"><p>{'Caddie is currently empty'|@translate}</p></div>
{/if}
