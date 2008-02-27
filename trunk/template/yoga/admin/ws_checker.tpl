<!-- DEV TAG: not smarty migrated -->
<!-- $Id$ -->

<div class="titrePage">
  <ul class="categoryActions">
    <li><a href="{U_HELP}" onclick="popuphelp(this.href); return false;" title="{lang:Help}"><img src="{themeconf:icon_dir}/help.png" class="button" alt="(?)"></a></li>
  </ul>
  <h2>{lang:title_wscheck} - {lang:web_services}</h2>
</div>

<!-- BEGIN update_result -->
<ul>
  {update_result.UPD_ELEMENT}
</ul>
<!-- END update_result -->

<!-- Add Access -->
<form method="post" name="adding_access" action="{F_STATUS_ACTION}">
  <!-- Current Default -->
  <fieldset>
    <legend>{lang:ws_adding_legend}</legend>
    <table>
    <!-- Access key -->
      <tr>
        <td>
          <label for="KeyName">{lang:Confidential partner key} </label>
        </td>
        <td>
          <input type="text" maxlength="35" size="35" name="add_partner"
            id="add_partner" value="{F_ADD_PARTNER}"
            title="{lang:Basis of access key calculation}" />
        </td>
      </tr>

    <!-- Target (cat/ids, tag/ids, or list/ids ids=id,id-id,...) -->
      <tr>
        <td>
          <label for="Access">{lang:Target}</label>
        </td>
        <td>
          <input type="text" maxlength="128" size="35" name="add_target"
            id="add_target" value="{F_ADD_ACCESS}"
            title="{lang:Facultative and restrictive option}" />
          <i><small> ({lang:Access: see help text for more})
          </small></i>
        </td>
      </tr>

    <!-- Restricted access to specific request -->
      <tr>
        <td>
          <label for="add_request">{lang:Restrict access to}</label>
        </td>
        <td>
          <select name="add_request" id="add_request" style="width: 18em"  
            onfocus="this.className='focus';" 
            onblur="this.className='nofocus';">
            <!-- BEGIN add_request -->
            <option value="{add_request.VALUE}" 
              {add_request.SELECTED}>{add_request.CONTENT}
            </option> 
            <!-- END add_request -->
          </select> 
          <i><small> ({lang:ws_Methods})</small></i>
        </td>
      </tr>

     <!-- Limit number of images information to be return -->
     <tr>
        <td>
          <label for="add_limit">{lang:Returned images limit}</label>
        </td>
        <td>
          <select name="add_limit" id="add_limit" style="width: 10em"  
            onfocus="this.className='focus';" 
            onblur="this.className='nofocus';">
            <!-- BEGIN add_limit -->
            <option value="{add_limit.VALUE}"
              {add_limit.SELECTED}>{add_limit.CONTENT}
            </option>
            <!-- END add_limit -->
          </select>
        </td>
      </tr>

    <!-- Open service is postponed by n days -->
    <!-- In comment currently
      <tr>
        <td>
          <label for="add_start">{lang:Postponed availability in days}</label>
        </td>
        <td>
          <select name="add_start" id="add_start" style="width: 10em" 
            onfocus="this.className='focus';" 
            onblur="this.className='nofocus';">
            <!-- BEGIN add_start -->
            <option value="{add_start.VALUE}"
              {add_start.SELECTED}>{add_start.CONTENT}
            </option> 
            <!-- END add_start -->
          </select>
        </td>
      </tr> -->

    <!-- Opened service only for n days -->
      <tr>
        <td>
          <label for="add_end">{lang:Duration in days}</label>
        </td>
        <td>
          <select name="add_end" id="add_end" style="width: 10em"  
            onfocus="this.className='focus';" 
            onblur="this.className='nofocus';">
            <!-- BEGIN add_end -->
            <option value="{add_end.VALUE}" 
              {add_end.SELECTED}>{add_end.CONTENT}
            </option> 
            <!-- END add_end -->
          </select>
        </td>
      </tr>

    <!-- Idendify your partner (name / website / phone) as you want -->
      <tr>
        <td>
          <label for="add_Comment">{lang:ws_Comment}</label>
          <br />
        </td>
        <td>
          <textarea name="add_comment" id="add_comment" 
            rows="4" cols="80">{lang:Comment to identify your partner clearly}</textarea>  
        </td>
      </tr>

    <!-- Add submit button -->
      <tr>
        <td>
        </td>
        <td>
          <input class="submit" type="submit" name="wsa_submit" style="width: 10em; padding-top: 3px;"
            value="{lang:Submit}" {TAG_INPUT_ENABLED} 
            title="{lang:Add this access definition}" />
        </td>
      </tr>      
    </table>
  </fieldset>
</form>

<!-- BEGIN acc_list -->
<!-- Access list -->
<form method="post" name="preferences" action="{F_STATUS_ACTION}">
  <input type="hidden" name="partner_prev" value="{F_PREV_PARTNER}">
  <input type="hidden" name="request_prev" value="{F_PREV_REQUEST}">
  <input type="hidden" name="high_prev" value="{F_PREV_HIGH}">
  <input type="hidden" name="normal_prev" value="{F_PREV_NORMAL}">
  <input type="hidden" name="order_prev" value="{F_PREV_ORDER}">
  <input type="hidden" name="dir5n_prev" value="{F_PREV_DIR5N}">
  <!-- Delete / Update Selected -->
  <fieldset>
    <legend>{lang:ws_update_legend}</legend>
    <table class="table2">
      <tr class="throw">
        <th>&nbsp;</th>
        <th>{lang:ws_KeyName}</th>
        <th>{lang:ws_Access}</th>
        <th>{lang:ws_End}</th>
        <th>{lang:ws_Request}</th>
        <th>{lang:ws_Limit}</th>
        <th>{lang:ws_Comment}</th>
      </tr>
      <!-- BEGIN access -->
      <tr class="{acc_list.access.CLASS}">
        <td>
          <input type="radio" name="selection" 
            value="{acc_list.access.ID}" id="selection-{acc_list.access.ID}">
        </td>
        <td><label for="selection-{acc_list.access.ID}">{acc_list.access.NAME}</label></td>
        <td>{acc_list.access.TARGET}</td>
        <td>{acc_list.access.END}</td>
        <td>{acc_list.access.REQUEST}</td>
        <td>{acc_list.access.LIMIT}</td>
        <td>{acc_list.access.COMMENT}</td>
      </tr>
      <!-- END user -->
    </table>

    <table>   
    <tr>    
    <td>
    {lang:ws_delete_legend}
    </td>
    <td>
    <input type="radio" name="delete_confirmation" 
      value="true">
    <input class="submit" type="submit" name="wsX_submit" style="width: 10em; padding-top: 3px;"
      value="{lang:Delete}" {TAG_INPUT_ENABLED}>
    </td>
    </tr>
    </table>
    <hr>    
    <table>
    <tr>
    <td>
    <span class="property">
      <label for="upd_end">{lang:Modify End from Now +} </label>
    </span> 
    <select name="upd_end" id="upd_end"  style="width: 10em" 
      onfocus="this.className='focus';" 
      onblur="this.className='nofocus';">
    <!-- BEGIN upd_end -->
      <option value="{acc_list.upd_end.VALUE}" {acc_list.upd_end.SELECTED}>
        {acc_list.upd_end.CONTENT}
      </option> 
    <!-- END upd_end -->
    </select> 
    <input class="submit" type="submit" name="wsu_submit" style="width: 10em; padding-top: 3px;"
       value="{lang:Submit}" {TAG_INPUT_ENABLED}>
    </td>
    <td>
    <i><small> ({lang:Web Services availability duration in days})</small></i>
    </td>
    </tr>
    </table>

  </fieldset>
</form>
<!-- BEGIN ws_status -->
    <h3>{acc_list.ws_status.VALUE}</h3>
<!-- END ws_status -->
<!-- END acc_list -->
