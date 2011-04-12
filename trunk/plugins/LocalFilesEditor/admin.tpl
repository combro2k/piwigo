{combine_script id="codemirror" path="plugins/LocalFilesEditor/codemirror/lib/codemirror.js"}
{combine_script id="codemirror.xml" require="codemirror" path="plugins/LocalFilesEditor/codemirror/mode/xml/xml.js"}
{combine_script id="codemirror.javascript" require="codemirror" path="plugins/LocalFilesEditor/codemirror/mode/javascript/javascript.js"}
{combine_script id="codemirror.css" require="codemirror" path="plugins/LocalFilesEditor/codemirror/mode/css/css.js"}
{combine_script id="codemirror.clike" require="codemirror" path="plugins/LocalFilesEditor/codemirror/mode/clike/clike.js"}
{combine_script id="codemirror.htmlmixed" require="codemirror.xml,codemirror.javascript,codemirror.css" path="plugins/LocalFilesEditor/codemirror/mode/htmlmixed/htmlmixed.js"}
{combine_script id="codemirror.php" require="codemirror.xml,codemirror.javascript,codemirror.css,codemirror.clike" path="plugins/LocalFilesEditor/codemirror/mode/php/php.js"}

{combine_css path="plugins/LocalFilesEditor/codemirror/lib/codemirror.css"}
{combine_css path="plugins/LocalFilesEditor/codemirror/mode/xml/xml.css"}
{combine_css path="plugins/LocalFilesEditor/codemirror/mode/javascript/javascript.css"}
{combine_css path="plugins/LocalFilesEditor/codemirror/mode/css/css.css"}
{combine_css path="plugins/LocalFilesEditor/codemirror/mode/clike/clike.css"}
{combine_css path="plugins/LocalFilesEditor/locfiledit.css"}

{footer_script}
function loadCodemirror() {ldelim}
  editor = CodeMirror.fromTextArea(document.getElementById("text"), {ldelim}
    matchBrackets: true,
    mode: "{$CODEMIRROR_MODE}",
    tabMode: "shift"
  });
  jQuery("#showedit, #hideedit").toggle();
  jQuery.post("plugins/LocalFilesEditor/update_config.php", {ldelim} editarea: "on"});
}

function unloadCodemirror() {ldelim} 
  editor.toTextArea();
  jQuery("#showedit, #hideedit").toggle();
  jQuery.post("plugins/LocalFilesEditor/update_config.php", {ldelim} editarea: "off"});
}

{if $LOAD_CODEMIRROR == 'on'}
if (document.getElementById("text") != null) loadCodemirror();
{/if}
{/footer_script}

<div class="titrePage">
  <h2>LocalFiles Editor</h2>
</div>

<form method="post" class="properties" action="{$F_ACTION}" ENCTYPE="multipart/form-data" name="form">

<div id="LocalFilesEditor">

<input type="hidden" value="{$zone_edit.EDITED_FILE}" name="edited_file"/>

{if isset ($create_tpl)}
  <table>
    <tr>
      <td style="text-align: right;">{'locfiledit_new_filename'|@translate}</td>
      <td style="text-align: left;"><input type="text" size="55" maxlength="50" value="{$create_tpl.NEW_FILE_NAME}" name="tpl_name"/></td>
    </tr>
    <tr>
      <td style="text-align: right;">{'locfiledit_parent_directory'|@translate}</td>
      <td style="text-align: left;">{html_options name=tpl_parent options=$create_tpl.PARENT_OPTIONS selected=$create_tpl.PARENT_SELECTED}</td>
    </tr>
    <tr>
      <td style="text-align: right;">{'locfiledit_model'|@translate}</td>
      <td style="text-align: left;">{html_options name=tpl_model options=$create_tpl.MODEL_OPTIONS selected=$create_tpl.MODEL_SELECTED}</td>
    </tr>
  </table>
<br><br>
<input class="submit" type="submit" value="{'Submit'|@translate}" name="create_tpl" />
{/if}

{if isset ($css_lang_tpl)}
{html_options name=file_to_edit options=$css_lang_tpl.OPTIONS selected=$css_lang_tpl.SELECTED}
<input class="submit" type="submit" value="{'locfiledit_edit'|@translate}" name="edit" />
<br><br>
  {if isset ($css_lang_tpl.NEW_FILE_URL)}
  <span class="{$css_lang_tpl.NEW_FILE_CLASS}">
  <a href="{$css_lang_tpl.NEW_FILE_URL}">{'locfiledit_new_tpl'|@translate}</a>
  </span>
  {/if}
{/if}

<div style="overflow:auto;"> {* top bar buttons *}
{if !empty($show_default)}
{foreach from=$show_default item=file name=default_loop}
<span class="top_right">
<a href="{$file.SHOW_DEFAULT}" onclick="window.open( this.href, 'local_file', 'location=no,toolbar=no,menubar=no,status=no,resizable=yes,scrollbars=yes,width=800,height=700' ); return false;">{'locfiledit_show_default'|@translate} "{$file.FILE}"</a>
</span>
{if !($smarty.foreach.default_loop.last)}<br>{/if}
{/foreach}
{/if}

{if isset ($zone_edit)}
<b>{$zone_edit.FILE_NAME}</b>

</div> {* top bar buttons *}

<textarea rows="30" cols="90" name="text" id="text">{$zone_edit.CONTENT_FILE}</textarea>
<div id="editarea_buttons">
<a href="javascript:loadCodemirror();" id="showedit">[{'locfiledit_enable_codemirror'|@translate}]</a>
<a href="javascript:unloadCodemirror();" id="hideedit">[{'locfiledit_disable_codemirror'|@translate}]</a>
</div>

<br>

<input class="submit" type="submit" value="{'locfiledit_save_file'|@translate}" name="submit"/>
{if isset ($restore)}
<input class="submit" type="submit" value="{'locfiledit_restore'|@translate}" name="restore" onclick="return confirm('{'locfiledit_restore_confirm'|@translate|escape:'javascript'}');"/>
{/if}

{if isset ($restore_infos)}
<br><br>
{'locfiledit_save_bak'|@translate}
{/if}

{/if}
</div>
</form>
