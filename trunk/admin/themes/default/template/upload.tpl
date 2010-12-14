<div class="titrePage">
  <h2>{'Waiting'|@translate} {$TABSHEET_TITLE}</h2>
</div>

<h3>{'Pictures waiting for validation'|@translate}</h3>

<form action="{$F_ACTION}" method="post" id="waiting">
  <table style="width:99%;" >
    <tr class="throw">
      <td style="width:20%;">{'Album'|@translate}</td>
      <td style="width:20%;">{'Date'|@translate}</td>
      <td style="width:20%;">{'File'|@translate}</td>
      <td style="width:20%;">{'Thumbnail'|@translate}</td>
      <td style="width:20%;">{'Author'|@translate}</td>
      <td style="width:1px;">&nbsp;</td>
    </tr>
    
    {if not empty($pictures) }
    {foreach from=$pictures item=picture name=picture_loop}
    <tr class="{if $smarty.foreach.picture_loop.index is odd}row1{else}row2{/if}">
      <td style="white-space:nowrap;">{$picture.CATEGORY_IMG}</td>
      <td style="white-space:nowrap;">{$picture.DATE_IMG}</td>
      <td style="white-space:nowrap;">
        <a href="{$picture.PREVIEW_URL_IMG}" title="{$picture.FILE_TITLE}">{$picture.FILE_IMG}</a>
      </td>
      <td style="white-space:nowrap;">
        {if not empty($picture.thumbnail) }
        <a href="{$picture.thumbnail.PREVIEW_URL_TN_IMG}" title="{$picture.thumbnail.FILE_TN_TITLE}">{$picture.thumbnail.FILE_TN_IMG}</a>
        {/if}
      </td>
      <td style="white-space:nowrap;">
        <a href="mailto:{$picture.UPLOAD_EMAIL}">{$picture.UPLOAD_USERNAME}</a>
      </td>
      <td style="white-space:nowrap;">
        <label><input type="radio" name="action-{$picture.ID_IMG}" value="validate"> {'Validate'|@translate}</label>
        <label><input type="radio" name="action-{$picture.ID_IMG}" value="reject"> {'Reject'|@translate}</label>
      </td>
    </tr>
    {/foreach}
    {/if}
  </table>

  <p class="bottomButtons">
    <input type="hidden" name="list" value="{$LIST}">
    <input class="submit" type="submit" name="submit" value="{'Submit'|@translate}">
    <input class="submit" type="submit" name="validate-all" value="{'Validate All'|@translate}">
    <input class="submit" type="submit" name="reject-all" value="{'Reject All'|@translate}">
    <input class="submit" type="reset" value="{'Reset'|@translate}">
  </p>

</form>
