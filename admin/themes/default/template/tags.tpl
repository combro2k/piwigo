{include file='include/tag_selection.inc.tpl'}

<div class="titrePage">
  <h2>{'Manage tags'|@translate}</h2>
</div>

<form action="{$F_ACTION}" method="post">
  {if isset($EDIT_TAGS_LIST)}
  <fieldset>
    <legend>{'Edit tags'|@translate}</legend>
    <input type="hidden" name="edit_list" value="{$EDIT_TAGS_LIST}">
    <table class="table2">
      <tr class="throw">
        <th>{'Current name'|@translate}</th>
        <th>{'New name'|@translate}</th>
      </tr>
      {foreach from=$tags item=tag}
      <tr>
        <td>{$tag.NAME}</td>
        <td><input type="text" name="tag_name-{$tag.ID}" value="{$tag.NAME}" size="30"></td>
      </tr>
      {/foreach}
    </table>

    <p>
      <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">
      <input class="submit" type="submit" name="submit" value="{'Submit'|@translate}">
      <input class="submit" type="reset" value="{'Reset'|@translate}">
    </p>
  </fieldset>
  {/if}

  <fieldset>
    <legend>{'Add a tag'|@translate}</legend>

    <label>
      {'New tag'|@translate}
      <input type="text" name="add_tag" size="30">
    </label>
    
    <p><input class="submit" type="submit" name="add" value="{'Submit'|@translate}"></p>
  </fieldset>

  <fieldset>
    <legend>{'Tag selection'|@translate}</legend>
    
    {$TAG_SELECTION}

    <p>
      <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">
      <input class="submit" type="submit" name="edit" value="{'Edit selected tags'|@translate}">
      <input class="submit" type="submit" name="delete" value="{'Delete selected tags'|@translate}" onclick="return confirm('{'Are you sure?'|@translate}');">
    </p>
  </fieldset>

</form>
