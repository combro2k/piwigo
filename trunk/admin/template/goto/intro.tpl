{* $Id: /piwigo/trunk/admin/template/goto/intro.tpl 7037 2009-03-13T23:43:50.771219Z plg  $ *}
<h2>{'title_default'|@translate}</h2>
<dl style="padding-top: 30px;">
  <dt>{'Piwigo version'|@translate}</dt>
  <dd>
    <ul>
      <li><a href="{$PHPWG_URL}"  onclick="window.open(this.href, ''); 
          return false;">Piwigo</a> {$PWG_VERSION}</li>
      <li><a href="{$U_CHECK_UPGRADE}">{'Check for upgrade'|@translate}</a></li>
    </ul>
  </dd>

  <dt>{'Environment'|@translate}</dt>
  <dd>
    <ul>
      <li>{'Operating system'|@translate}: {$OS}</li>
      <li>PHP: {$PHP_VERSION} (<a href="{$U_PHPINFO}" onclick="window.open(this.href, ''); 
          return false;">{'Show info'|@translate}</a>)  [{$PHP_DATATIME}]</li>
      <li>MySQL: {$MYSQL_VERSION} [{$DB_DATATIME}]</li>
    </ul>
  </dd>

  <dt>{'Database'|@translate}</dt>
  <dd>
    <ul>
      <li>
        {$DB_ELEMENTS}
        {if isset($waiting)}
        (<a href="{$waiting.URL}">{$waiting.INFO}</a>)
        {/if}

        {if isset($first_added)}
        ({$first_added.DB_DATE})
        {/if}
      </li>
      <li>{$DB_CATEGORIES} ({$DB_IMAGE_CATEGORY})</li>
      <li>{$DB_TAGS} ({$DB_IMAGE_TAG})</li>
      <li>{$DB_USERS}</li>
      <li>{$DB_GROUPS}</li>
      <li>
        {$DB_COMMENTS}
        {if isset($unvalidated)}
        (<a href="{$unvalidated.URL}">{$unvalidated.INFO}</a>)
        {/if}
      </li>
    </ul>
  </dd>
</dl>


<form name="QuickSynchro" action="{$U_CAT_UPDATE}" method="post" id="QuickSynchro" style="display: block; text-align:right;">
<div>
<input type="hidden" name="sync" value="files" checked="checked">
<input type="hidden" name="display_info" value="1" checked="checked">
<input type="hidden" name="add_to_caddie" value="1" checked="checked">
<input type="hidden" name="privacy_level" value="0" checked="checked">
<input type="hidden" name="sync_meta" checked="checked">
<input type="hidden" name="simulate" value="0">
<input type="hidden" name="subcats-included" value="1" checked="checked">
</div>
<div class="bigbutton">
<span class="bigtext">{'Quick Local Synchronization'|@translate}</span>
<input type="submit" value="" name="submit">
</div>
</form>
