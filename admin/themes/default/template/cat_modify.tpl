
{include file='include/autosize.inc.tpl'}
{include file='include/resize.inc.tpl'}

<div class="titrePage">
  <h2>{'Edit album'|@translate}</h2>
</div>

<h3>{$CATEGORIES_NAV}</h3>

<ul class="categoryActions">
  {if cat_admin_access($CAT_ID)}
  <li><a href="{$U_JUMPTO}" title="{'jump to album'|@translate}"><img src="{$themeconf.admin_icon_dir}/category_jump-to.png" class="button" alt="{'jump to album'|@translate}"></a></li>
  {/if}
  {if isset($U_MANAGE_ELEMENTS) }
  <li><a href="{$U_MANAGE_ELEMENTS}" title="{'manage album photos'|@translate}"><img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/category_elements.png" class="button" alt="{'Photos'|@translate}"></a></li>
  {/if}
  <li><a href="{$U_MANAGE_RANKS}" title="{'manage photo ranks'|@translate}"><img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/ranks.png" class="button" alt="{'ranks'|@translate}"></a></li>
  <li><a href="{$U_CHILDREN}" title="{'manage sub-albums'|@translate}"><img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/category_children.png" class="button" alt="{'sub-albums'|@translate}"></a></li>
  {if isset($U_MANAGE_PERMISSIONS) }
  <li><a href="{$U_MANAGE_PERMISSIONS}" title="{'edit album permissions'|@translate}"><img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/category_permissions.png" class="button" alt="{'Permissions'|@translate}"></a></li>
  {/if}
  {if isset($U_SYNC) }
  <li><a href="{$U_SYNC}" title="{'Synchronize'|@translate}"><img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/synchronize.png" class="button" alt="{'Synchronize'|@translate}"></a></li>
  {/if}
  {if isset($U_DELETE) }
  <li><a href="{$U_DELETE}" title="{'delete album'|@translate}"><img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/category_delete.png" class="button" alt="{'delete album'|@translate}" onclick="return confirm('{'Are you sure?'|@translate|@escape:javascript}');"></a></li>
  {/if}
</ul>

<form action="{$F_ACTION}" method="POST" id="catModify">

<fieldset>
  <legend>{'Informations'|@translate}</legend>
  <table>

    {if isset($CAT_FULL_DIR) }
    <tr>
      <td><strong>{'Directory'|@translate}</strong></td>
      <td class="row1">{$CAT_FULL_DIR}</td>
    </tr>
    {/if}
    
    <tr>
      <td><strong>{'Name'|@translate}</strong></td>
      <td>
        <input type="text" class="large" name="name" value="{$CAT_NAME}" maxlength="60">
      </td>
    </tr>
    <tr>
      <td><strong>{'Description'|@translate}</strong></td>
      <td>
        <textarea cols="50" rows="5" name="comment" id="comment" class="description">{$CAT_COMMENT}</textarea>
      </td>
    </tr>
  </table>
</fieldset>

{if isset($move_cat_options) }
<fieldset id="move">
  <legend>{'Move'|@translate}</legend>
  {'Parent album'|@translate}
  <select class="categoryDropDown" name="parent">
    <option value="0">------------</option>
    {html_options options=$move_cat_options selected=$move_cat_options_selected }
  </select>
</fieldset>
{/if}

<fieldset id="options">
  <legend>{'Options'|@translate}</legend>
  <table>
    <tr>
      <td><strong>{'Access type'|@translate}</strong>
      <td>
        {html_radios name='status' values=$status_values output=$status_values|translate selected=$CAT_STATUS}
      </td>
    </tr>
    <tr>
      <td><strong>{'Lock'|@translate}</strong>
      <td>
        {html_radios name='visible' values='true,false'|@explode output='No,Yes'|@explode|translate selected=$CAT_VISIBLE}
      </td>
    </tr>
    <tr>
      <td><strong>{'Comments'|@translate}</strong>
      <td>
        {html_radios name='commentable' values='false,true'|@explode output='No,Yes'|@explode|translate selected=$CAT_COMMENTABLE}
      </td>
    </tr>
  </table>
</fieldset>

<p style="text-align:center;">
  <input class="submit" type="submit" value="{'Submit'|@translate}" name="submit">
  <input class="submit" type="reset" value="{'Reset'|@translate}" name="reset">
</p>

{if isset($representant) }
<fieldset id="representant">
  <legend>{'Representant'|@translate}</legend>
  <table>
    <tr>
      <td align="center">
        {if isset($representant.picture) }
        <a href="{$representant.picture.URL}"><img src="{$representant.picture.SRC}" alt="" class="miniature"></a>
        {else}
        <img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/category_representant_random.png" class="button" alt="{'Random photo'|@translate}" class="miniature">
        {/if}
      </td>
      <td>
        {if $representant.ALLOW_SET_RANDOM }
        <p><input class="submit" type="submit" name="set_random_representant" value="{'Find a new representant by random'|@translate}"></p>
        {/if}

        {if isset($representant.ALLOW_DELETE) }
        <p><input class="submit" type="submit" name="delete_representant" value="{'Delete Representant'|@translate}"></p>
        {/if}
      </td>
    </tr>
  </table>
</fieldset>
{/if}

</form>

<form action="{$F_ACTION}" method="POST" id="links">

<fieldset id="linkAllNew">
  <legend>{'Link all album photos to a new album'|@translate}</legend>

  <table>
    <tr>
      <td>{'Virtual album name'|@translate}</td>
      <td><input type="text" class="large" name="virtual_name"></td>
    </tr>

    <tr>
      <td>{'Parent album'|@translate}</td>
      <td>
        <select class="categoryDropDown" name="parent">
          <option value="0">------------</option>
          {html_options options=$create_new_parent_options }
        </select>
      </td>
    </tr>
  </table>

  <p>
    <input class="submit" type="submit" value="{'Submit'|@translate}" name="submitAdd">
    <input class="submit" type="reset" value="{'Reset'|@translate}" name="reset">
  </p>

</fieldset>

<fieldset id="linkAllExist">
  <legend>{'Link all album photos to some existing albums'|@translate}</legend>

  <table>
    <tr>
      <td>{'Albums'|@translate}</td>
      <td>
        <select class="categoryList" name="destinations[]" multiple="multiple" size="5">
          {html_options options=$category_destination_options }
        </select>
      </td>
    </tr>
  </table>

  <p>
    <input class="submit" type="submit" value="{'Submit'|@translate}" name="submitDestinations">
    <input class="submit" type="reset" value="{'Reset'|@translate}" name="reset">
  </p>

</fieldset>

{if isset($group_mail_options)}
<fieldset id="emailCatInfo">
  <legend>{'Send an information email to group members'|@translate}</legend>

  <table>
    <tr>
      <td><strong>{'Group'|@translate}</strong></td>
      <td>
        <select name="group">
          {html_options options=$group_mail_options}
        </select>
      </td>
    </tr>
    <tr>
      <td><strong>{'Mail content'|@translate}</strong></td>
      <td>
        <textarea cols="50" rows="5" name="mail_content" id="mail_content" class="description">{$MAIL_CONTENT}</textarea>
      </td>
    </tr>

  </table>

  <p>
    <input class="submit" type="submit" value="{'Submit'|@translate}" name="submitEmail">
    <input class="submit" type="reset" value="{'Reset'|@translate}" name="reset">
  </p>

</fieldset>
{/if}

</form>
