{combine_script id='jquery.jgrowl' load='footer' require='jquery' path='themes/default/js/plugins/jquery.jgrowl_minimized.js'}
{combine_script id='jquery.plupload' load='footer' require='jquery' path='themes/default/js/plugins/plupload/plupload.full.min.js'}
{combine_script id='jquery.plupload.queue' load='footer' require='jquery' path='themes/default/js/plugins/plupload/jquery.plupload.queue/jquery.plupload.queue.min.js'}
{combine_script id='jquery.ui.progressbar' load='footer'}

{combine_css path="themes/default/js/plugins/jquery.jgrowl.css"}
{combine_css path="themes/default/js/plugins/plupload/jquery.plupload.queue/css/jquery.plupload.queue.css"}

{include file='include/colorbox.inc.tpl'}
{include file='include/add_album.inc.tpl'}

{combine_script id='LocalStorageCache' load='footer' path='admin/themes/default/js/LocalStorageCache.js'}

{combine_script id='jquery.selectize' load='footer' path='themes/default/js/plugins/selectize.min.js'}
{combine_css id='jquery.selectize' path="themes/default/js/plugins/selectize.default.css"}

{footer_script}
{* <!-- CATEGORIES --> *}
var categoriesCache = new CategoriesCache({
  serverKey: '{$CACHE_KEYS.categories}',
  serverId: '{$CACHE_KEYS._hash}',
  rootUrl: '{$ROOT_URL}'
});

categoriesCache.selectize(jQuery('[data-selectize=categories]'), {
  filter: function(categories, options) {
    if (categories.length > 0) {
      jQuery("#albumSelection, .selectFiles, .showFieldset").show();
      options.default = categories[0].id;
    }
    
    return categories;
  }
});

jQuery('[data-add-album]').pwgAddAlbum({ cache: categoriesCache });

var uploadify_path = '{$uploadify_path}';
var upload_id = '{$upload_id}';
var session_id = '{$session_id}';
var pwg_token = '{$pwg_token}';
var buttonText = "{'Select files'|@translate}";
var sizeLimit = Math.round({$upload_max_filesize} / 1024); /* in KBytes */

var noAlbum_message = "{'Select an album'|translate}";

{literal}
jQuery(document).ready(function(){
  jQuery("#uploadWarningsSummary a.showInfo").click(function() {
    jQuery("#uploadWarningsSummary").hide();
    jQuery("#uploadWarnings").show();
    return false;
  });

  jQuery("#showPermissions").click(function() {
    jQuery(this).parent(".showFieldset").hide();
    jQuery("#permissions").show();
    return false;
  });

	jQuery("#uploader").pluploadQueue({
		// General settings
		// runtimes : 'html5,flash,silverlight,html4',
		runtimes : 'html5',

		// url : '../upload.php',
		url : 'ws.php?method=pwg.images.upload&format=json',

		// User can upload no more then 20 files in one go (sets multiple_queues to false)
		max_file_count: 100,
		
		chunk_size: '500kb',
		
		filters : {
			// Maximum file size
			max_file_size : '1000mb',
			// Specify what files to browse for
			mime_types: [
				{title : "Image files", extensions : "jpeg,jpg,gif,png"},
				{title : "Zip files", extensions : "zip"}
			]
		},

		// Rename files by clicking on their titles
		// rename: true,
		
		// Sort files
		sortable: true,

		// Enable ability to drag'n'drop files onto the widget (currently only HTML5 supports that)
		dragdrop: true,

    init : {
      BeforeUpload: function(up, file) {
        console.log('[BeforeUpload]', file);

        // You can override settings before the file is uploaded
        // up.setOption('url', 'upload.php?id=' + file.id);
        up.setOption(
          'multipart_params',
          {
            category : jQuery("select[name=category] option:selected").val(),
            level : jQuery("select[name=level] option:selected").val(),
            pwg_token : pwg_token
            // name : file.name
          }
        );
      },

      FileUploaded: function(up, file, info) {
        // Called when file has finished uploading
        console.log('[FileUploaded] File:', file, "Info:", info);
      
        var data = jQuery.parseJSON(info.response);
      
        jQuery("#uploadedPhotos").parent("fieldset").show();
      
        html = '<a href="admin.php?page=photo-'+data.result.image_id+'" target="_blank">';
        html += '<img src="'+data.result.src+'" class="thumbnail" title="'+data.result.name+'">';
        html += '</a> ';
      
        jQuery("#uploadedPhotos").prepend(html);

        up.removeFile(file);
      }
    }
	});

{/literal}
});
{/footer_script}

<div class="titrePage">
  <h2>{'Upload Photos'|@translate} {$TABSHEET_TITLE}</h2>
</div>

<div id="photosAddContent">

{*
<div class="infos">
  <ul>
    <li>%d photos added..</li>
  </ul>
</div>
*}

{if count($setup_errors) > 0}
<div class="errors">
  <ul>
  {foreach from=$setup_errors item=error}
    <li>{$error}</li>
  {/foreach}
  </ul>
</div>
{else}

  {if count($setup_warnings) > 0}
<div class="warnings">
  <ul>
    {foreach from=$setup_warnings item=warning}
    <li>{$warning}</li>
    {/foreach}
  </ul>
  <div class="hideButton" style="text-align:center"><a href="{$hide_warnings_link}">{'Hide'|@translate}</a></div>
</div>
  {/if}


{if !empty($thumbnails)}
<fieldset>
  <legend>{'Uploaded Photos'|@translate}</legend>
  <div>
  {foreach from=$thumbnails item=thumbnail}
    <a href="{$thumbnail.link}" class="externalLink">
      <img src="{$thumbnail.src}" alt="{$thumbnail.file}" title="{$thumbnail.title}" class="thumbnail">
    </a>
  {/foreach}
  </div>
  <p id="batchLink"><a href="{$batch_link}">{$batch_label}</a></p>
</fieldset>
<p style="margin:10px"><a href="{$another_upload_link}">{'Add another set of photos'|@translate}</a></p>
{else}

<form id="uploadForm" enctype="multipart/form-data" method="post" action="{$form_action}">
{if $upload_mode eq 'multiple'}
    <input name="upload_id" value="{$upload_id}" type="hidden">
{/if}

    <fieldset>
      <legend>{'Drop into album'|@translate}</legend>

      <span id="albumSelection" style="display:none">
      <select data-selectize="categories" data-value="{$selected_category|@json_encode|escape:html}"
        name="category" style="width:400px"></select>
      <br>{'... or '|@translate}</span>
      <a href="#" data-add-album="category" title="{'create a new album'|@translate}">{'create a new album'|@translate}</a>
    </fieldset>

    <p class="showFieldset" style="display:none"><a id="showPermissions" href="#">{'Manage Permissions'|@translate}</a></p>

    <fieldset id="permissions" style="display:none">
      <legend>{'Who can see these photos?'|@translate}</legend>

      <select name="level" size="1">
        {html_options options=$level_options selected=$level_options_selected}
      </select>
    </fieldset>

    <fieldset class="selectFiles" style="display:none">
      <legend>{'Select files'|@translate}</legend>
 
    {if isset($original_resize_maxheight)}<p class="uploadInfo">{'The picture dimensions will be reduced to %dx%d pixels.'|@translate:$original_resize_maxwidth:$original_resize_maxheight}</p>{/if}

    <p id="uploadWarningsSummary">{$upload_max_filesize_shorthand}B. {$upload_file_types}. {if isset($max_upload_resolution)}{$max_upload_resolution}Mpx{/if} <a class="icon-info-circled-1 showInfo" title="{'Learn more'|@translate}"></a></p>

    <p id="uploadWarnings">
{'Maximum file size: %sB.'|@translate:$upload_max_filesize_shorthand}
{'Allowed file types: %s.'|@translate:$upload_file_types}
  {if isset($max_upload_resolution)}
{'Approximate maximum resolution: %dM pixels (that\'s %dx%d pixels).'|@translate:$max_upload_resolution:$max_upload_width:$max_upload_height}
  {/if}
    </p>


	<div id="uploader">
		<p>Your browser doesn't have HTML5 support.</p>
	</div>

    </fieldset>

</form>

<div id="uploadProgress" style="display:none">
{'Photo %s of %s'|@translate:'<span id="progressCurrent">1</span>':'<span id="progressMax">10</span>'}
<br>
<div id="progressbar"></div>
</div>

<fieldset style="display:none">
  <legend>{'Uploaded Photos'|@translate}</legend>
  <div id="uploadedPhotos"></div>
</fieldset>

{/if} {* empty($thumbnails) *}
{/if} {* $setup_errors *}

</div> <!-- photosAddContent -->
