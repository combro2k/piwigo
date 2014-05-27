{include file='include/autosize.inc.tpl'}
{include file='include/dbselect.inc.tpl'}
{include file='include/datepicker.inc.tpl'}

{combine_script id='LocalStorageCache' load='footer' path='admin/themes/default/js/LocalStorageCache.js'}

{combine_script id='jquery.selectize' load='footer' path='themes/default/js/plugins/selectize.min.js'}
{combine_css id='jquery.selectize' path="themes/default/js/plugins/selectize.default.css"}

{footer_script}
(function(){
{* <!-- CATEGORIES --> *}
var categoriesCache = new CategoriesCache({
  serverKey: '{$CACHE_KEYS.categories}',
  serverId: '{$CACHE_KEYS._hash}',
  rootUrl: '{$ROOT_URL}'
});

categoriesCache.selectize(jQuery('[data-selectize=categories]'), { {if $STORAGE_ALBUM}
  filter: function(categories, options) {
    options.default = (this.name == 'associate[]') ? {$STORAGE_ALBUM} : undefined;
    return categories;
  }
{/if} });

{* <!-- TAGS --> *}
var tagsCache = new TagsCache({
  serverKey: '{$CACHE_KEYS.tags}',
  serverId: '{$CACHE_KEYS._hash}',
  rootUrl: '{$ROOT_URL}'
});

tagsCache.selectize(jQuery('[data-selectize=tags]'));

{* <!-- DATEPICKER --> *}
jQuery(function(){ {* <!-- onLoad needed to wait localization loads --> *}
  jQuery('[data-datepicker]').pwgDatepicker({ showTimepicker: true });
});
}());
{/footer_script}

<h2>{$TITLE} &#8250; {'Edit photo'|@translate} {$TABSHEET_TITLE}</h2>

<form action="{$F_ACTION}" method="post" id="catModify">

  <fieldset>
    <legend>{'Informations'|@translate}</legend>

    <table>

      <tr>
        <td id="albumThumbnail">
          <img src="{$TN_SRC}" alt="{'Thumbnail'|@translate}" class="Thumbnail">
        </td>
        <td id="albumLinks" style="width:400px;vertical-align:top;">
          <ul style="padding-left:15px;margin:0;">
            <li>{$INTRO.file}</li>
            <li>{$INTRO.add_date}</li>
            <li>{$INTRO.added_by}</li>
            <li>{$INTRO.size}</li>
            <li>{$INTRO.stats}</li>
            <li>{$INTRO.id}</li>
          </ul>
        </td>
        <td class="photoLinks">
          <ul>
          {if isset($U_JUMPTO) }
            <li><a class="icon-eye" href="{$U_JUMPTO}">{'jump to photo'|@translate} →</a></li>
          {/if}
          {if !url_is_remote($PATH)}
            <li><a class="icon-arrows-cw" href="{$U_SYNC}">{'Synchronize metadata'|@translate}</a></li>

            <li><a class="icon-trash" href="{$U_DELETE}" onclick="return confirm('{'Are you sure?'|@translate|@escape:javascript}');">{'delete photo'|@translate}</a></li>
          {/if}
          </ul>
        </td>
      </tr>
    </table>

  </fieldset>

  <fieldset>
    <legend>{'Properties'|@translate}</legend>

    <p>
      <strong>{'Title'|@translate}</strong>
      <br>
      <input type="text" class="large" name="name" value="{$NAME|@escape}">
    </p>

    <p>
      <strong>{'Author'|@translate}</strong>
      <br>
      <input type="text" class="large" name="author" value="{$AUTHOR}">
    </p>

    <p>
      <strong>{'Creation date'|@translate}</strong>
      <br>
      <input type="hidden" name="date_creation" value="{$DATE_CREATION}">
      <label>
        <i class="icon-calendar"></i>
        <input type="text" data-datepicker="date_creation" data-datepicker-unset="date_creation_unset" readonly>
      </label>
      <a href="#" class="icon-cancel-circled" id="date_creation_unset">{'unset'|translate}</a>
    </p>

    <p>
      <strong>{'Linked albums'|@translate}</strong>
      <br>
      <select data-selectize="categories" data-value="{$associated_albums|@json_encode|escape:html}"
        name="associate[]" multiple style="width:600px;"></select>
    </p>

    <p>
      <strong>{'Representation of albums'|@translate}</strong>
      <br>
      <select data-selectize="categories" data-value="{$represented_albums|@json_encode|escape:html}"
        name="represent[]" multiple style="width:600px;"></select>
    </p>

    <p>
      <strong>{'Tags'|@translate}</strong>
      <br>
      <select data-selectize="tags" data-value="{$tag_selection|@json_encode|escape:html}"
        name="tags[]" multiple style="width:600px;" data-selectize-create></select>
    </p>

    <p>
      <strong>{'Description'|@translate}</strong>
      <br>
      <textarea name="description" id="description" class="description">{$DESCRIPTION}</textarea>
    </p>

    <p>
      <strong>{'Who can see this photo?'|@translate}</strong>
      <br>
      <select name="level" size="1">
        {html_options options=$level_options selected=$level_options_selected}
      </select>
   </p>

  <p style="margin:40px 0 0 0">
    <input class="submit" type="submit" value="{'Save Settings'|@translate}" name="submit">
  </p>
</fieldset>

</form>
