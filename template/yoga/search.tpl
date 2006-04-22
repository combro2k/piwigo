<!-- $Id:$ -->
<div id="content">

  <div class="titrePage">
    <ul class="categoryActions">
      <li><a href="{U_HELP}" onclick="popuphelp(this.href); return false;" title="{lang:Help}"><img src="{themeconf:icon_dir}/help.png" class="button" alt="(?)"></a></li>
      <li><a href="{U_HOME}" title="{lang:return to homepage}" rel="home"><img src="{themeconf:icon_dir}/home.png" class="button" alt="{lang:home}"/></a></li>
    </ul>
    <h2>{lang:Search}</h2>
  </div>

<!-- TO DO -->
<form method="post" name="post" action="{S_SEARCH_ACTION}">
<!-- BEGIN errors -->
<div class="errors">
  <ul>
    <!-- BEGIN error -->
    <li>{errors.error.ERROR}</li>
    <!-- END error -->
  </ul>
</div>
<!-- END errors -->
<table width="100%" cellpadding="2">
  <tr>
    <td width="50%" colspan="2"><b>{lang:search_keywords} : </b>
    <td colspan="2" valign="top">
	  <input type="text" style="width: 300px" name="search_allwords" size="30" />
	  <br />
	  <input type="radio" name="mode" value="AND" checked="checked" /> {lang:search_mode_and}<br />
	  <input type="radio" name="mode" value="OR" /> {lang:search_mode_or}
	</td>
  </tr>
  <tr>
    <td colspan="2"><b>{lang:search_author} :</b>
    <td colspan="2" valign="middle">
	  <input type="text" style="width: 300px" name="search_author" size="30" />
	</td>
  </tr>

  <tr>
    <td colspan="2"><b>{lang:Search tags} :</b></td>
    <td colspan="2" valign="middle">
      {TAG_SELECTION}
      <br /><label><input type="radio" name="tag_mode" value="AND" checked="checked" /> {lang:All tags}</label>
      <br /><label><input type="radio" name="tag_mode" value="OR" /> {lang:Any tag}</label>
    </td>
  </tr>

  <tr>
    <td colspan="2"><b>{lang:search_date} :</b>
    <td colspan="2" valign="middle">
      <table>
        <tr>
          <td>{lang:search_date_from} :</td>
          <td>
            <select name="start_day">
              <!-- BEGIN start_day -->
              <option {start_day.SELECTED} value="{start_day.VALUE}">{start_day.OPTION}</option>
              <!-- END start_day -->
            </select>
            <select name="start_month">
              <!-- BEGIN start_month -->
              <option {start_month.SELECTED} value="{start_month.VALUE}">{start_month.OPTION}</option>
              <!-- END start_month -->
            </select>
	    <input name="start_year" type="text" size="4" maxlength="4">&nbsp;
	    <a href="#" onClick="document.post.start_day.value={TODAY_DAY};document.post.start_month.value={TODAY_MONTH};document.post.start_year.value={TODAY_YEAR};">{lang:today}</a>
          </td>
        </tr>
        <tr>
          <td>{lang:search_date_to} :</td>
          <td>
            <select name="end_day">
              <!-- BEGIN end_day -->
              <option {end_day.SELECTED} value="{end_day.VALUE}">{end_day.OPTION}</option>
              <!-- END end_day -->
            </select>
            <select name="end_month">
              <!-- BEGIN end_month -->
              <option {end_month.SELECTED} value="{end_month.VALUE}">{end_month.OPTION}</option>
              <!-- END end_month -->
            </select>
            <input name="end_year" type="text" size="4" maxlength="4">&nbsp;
	    <a href="#" onClick="document.post.end_day.value={TODAY_DAY};document.post.end_month.value={TODAY_MONTH};document.post.end_year.value={TODAY_YEAR};">{lang:today}</a>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr class="admin">
    <th colspan="4">{lang:search_options}</th>
  </tr>
  <tr>
    <td width="25%" ><b>{lang:search_categories} : </b>
    <td width="25%" nowrap="nowrap">
	  <select style="width:200px" name="cat[]" multiple="multiple" size="8">
      <!-- BEGIN category_option -->
        <option value="{category_option.VALUE}">{category_option.OPTION}</option>
      <!-- END category_option -->
      </select>
	</td>
    <td width="25%" nowrap="nowrap"><b>{lang:search_subcats_included} : </b></td>
    <td width="25%" nowrap="nowrap">
	  <input type="radio" name="subcats-included" value="1" checked="checked" />{lang:yes}&nbsp;&nbsp;
	  <input type="radio" name="subcats-included" value="0" />{lang:no}
	</td>
   </tr>
   <tr>
    <td width="25%" nowrap="nowrap"><b>{lang:search_date_type} : </b></td>
    <td width="25%" nowrap="nowrap">
	  <input type="radio" name="date_type" value="date_creation" checked="checked" />{lang:Creation date}<br />
	  <input type="radio" name="date_type" value="date_available" />{lang:Post date}
	</td>
	<td><b>{lang:search_sort} : </b></td>
    <td nowrap="nowrap">
	  <input type="radio" name="sd" value="AND" />{lang:search_ascending}<br />
	  <input type="radio" name="sd" value="d" checked="checked" />{lang:search_descending}
	</td>
  </tr>
<tr>
<td align="center" valign="bottom" colspan="4" height="38">
<input type="submit" name="submit" value="{lang:submit}" class="bouton" />&nbsp;&nbsp;
<input type="reset" value="{lang:reset}" class="bouton" />
</td>
</table>
</form>

<script type="text/javascript"><!--
document.post.search_allwords.focus();
//--></script>

</div> <!-- content -->
