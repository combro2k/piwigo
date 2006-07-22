<!-- $Id$ -->
<div class="titrePage">
  <ul class="categoryActions">
    <li><a href="{U_HELP}" onclick="popuphelp(this.href); return false;" title="{lang:Help}"><img src="{themeconf:icon_dir}/help.png" class="button" alt="(?)"></a></li>
  </ul>
  <h2>{lang:title_configuration}</h2>
</div>

<form method="post" action="{F_ACTION}" class="properties">

<!-- BEGIN general -->
<fieldset id="generalConf">
  <legend>{lang:conf_general_title}</legend>

  <ul>
    <li>
      <span class="property">
        <label for="gallery_title">{lang:Gallery title}</label>
      </span>
      <input type="text" maxlength="255" size="50" name="gallery_title" id="gallery_title" value="{general.CONF_GALLERY_TITLE}" />
    </li>

    <li>
      <span class="property">
        <label for="page_banner">{lang:Page banner}</label>
      </span>
      <textarea class="description" name="page_banner" id="page_banner">{general.CONF_PAGE_BANNER}</textarea>
    </li>

    <li>
      <span class="property">
        <label for="gallery_url">{lang:Gallery URL}</label>
      </span>
      <input type="text" maxlength="255" size="50" name="gallery_url" id="gallery_url" value="{general.CONF_GALLERY_URL}" />
    </li>

    <li>
      <span class="property">{lang:History}</span>
      <label><input type="radio" class="radio" name="log" value="true" {general.HISTORY_YES} />{lang:Yes}</label>
      <label><input type="radio" class="radio" name="log" value="false" {general.HISTORY_NO} />{lang:No}</label>
    </li>

    <li>
      <span class="property">{lang:Lock gallery}</span>
      <label><input type="radio" class="radio" name="gallery_locked" value="true" {general.GALLERY_LOCKED_YES} />{lang:Yes}</label>
      <label><input type="radio" class="radio" name="gallery_locked" value="false" {general.GALLERY_LOCKED_NO} />{lang:No}</label>
    </li>
    
    <li>
      <span class="property">{lang:Rating}</span>
      <label><input type="radio" class="radio" name="rate" value="true" {general.RATE_YES} />{lang:Yes}</label>
      <label><input type="radio" class="radio" name="rate" value="false" {general.RATE_NO} />{lang:No}</label>
    </li>

    <li>
      <span class="property">{lang:Rating by guests}</span>
      <label><input type="radio" class="radio" name="rate_anonymous" value="true" {general.RATE_ANONYMOUS_YES} />{lang:Yes}</label>
      <label><input type="radio" class="radio" name="rate_anonymous" value="false" {general.RATE_ANONYMOUS_NO} />{lang:No}</label>
    </li>
  </ul>
</fieldset>
<!-- END general -->

<!-- BEGIN comments -->
<fieldset id="commentsConf">
  <legend>{lang:conf_comments_title}</legend>

  <ul>
    <li>
      <span class="property">{lang:Comments for all}</span>
      <label><input type="radio" class="radio" name="comments_forall" value="true" {comments.COMMENTS_ALL_YES} />{lang:Yes}</label>
      <label><input type="radio" class="radio" name="comments_forall" value="false" {comments.COMMENTS_ALL_NO} />{lang:No}</label>
    </li>

    <li>
      <span class="property">
        <label for="nb_comment_page">{lang:Number of comments per page}</label>
      </span>
      <input type="text" size="3" maxlength="4" name="nb_comment_page" id="nb_comment_page" value="{comments.NB_COMMENTS_PAGE}" />
    </li>

    <li>
      <span class="property">{lang:Validation}</span>
      <label><input type="radio" class="radio" name="comments_validation" value="true" {comments.VALIDATE_YES} />{lang:Yes}</label>
      <label><input type="radio" class="radio" name="comments_validation" value="false" {comments.VALIDATE_NO} />{lang:No}</label>
    </li>
  </ul>
</fieldset>
<!-- END comments -->
<!-- BEGIN default -->
<fieldset id="commentsConf">
  <legend>{lang:conf_default_title}</legend>

  <ul>
    <li>
      <span class="property">
        <label for="default_language">{lang:Language}</label>
      </span>
      <select name="default_language" id="default_language">
        <!-- BEGIN language_option -->
        <option value="{default.language_option.VALUE}" {default.language_option.SELECTED}>{default.language_option.CONTENT}</option>
        <!-- END language_option -->
      </select>
    </li>

    <li>
      <span class="property">
        <label for="nb_image_line">{lang:Number of images per row}</label>
      </span>
      <input type="text" size="3" maxlength="2" id="nb_image_line" name="nb_image_line" value="{default.NB_IMAGE_LINE}" />
    </li>

    <li>
      <span class="property">
        <label for="nb_line_page">{lang:Number of rows per page}</label>
      </span>
      <input type="text" size="3" maxlength="2" id="nb_line_page" name="nb_line_page" value="{default.NB_ROW_PAGE}" />
    </li>

    <li>
      <span class="property">
        <label for="default_template">{lang:Interface theme}</label>
      </span>
      <select name="default_template" id="default_template">
        <!-- BEGIN template_option -->
        <option value="{default.template_option.VALUE}" {default.template_option.SELECTED}>{default.template_option.CONTENT}</option>
        <!-- END template_option -->
      </select>
    </li>

    <li>
      <span class="property">
        <label for="recent_period">{lang:Recent period}</label>
      </span>
      <input type="text" size="3" maxlength="2" name="recent_period" id="recent_period" value="{default.CONF_RECENT}" />
    </li>

    <li>
      <span class="property">{lang:Expand all categories}</span>
      <label><input type="radio" class="radio" name="auto_expand" value="true" {default.EXPAND_YES} />{lang:Yes}</label>
      <label><input type="radio" class="radio" name="auto_expand" value="false" {default.EXPAND_NO} />{lang:No}</label>
    </li>

    <li>
      <span class="property">{lang:Show number of comments}</span>
      <label><input type="radio" class="radio" name="show_nb_comments" value="true" {default.SHOW_COMMENTS_YES} />{lang:Yes}</label>
      <label><input type="radio" class="radio" name="show_nb_comments" value="false" {default.SHOW_COMMENTS_NO} />{lang:No}</label>
    </li>

    <li>
      <span class="property">
        <label for="default_maxwidth">{lang:Maximum width of the pictures}</label>
      </span>
      <input type="text" size="4" maxlength="4" id="default_maxwidth" name="default_maxwidth" value="{default.MAXWIDTH}" />
    </li>

    <li>
      <span class="property">
        <label for="default_maxheight">{lang:Maximum height of the pictures}</label>
      </span>
      <input type="text" size="4" maxlength="4" id="default_maxheight" name="default_maxheight" value="{default.MAXHEIGHT}" />
    </li>
  </ul>
</fieldset>
<!-- END default -->

  <p>
    <input type="submit" name="submit" value="{lang:Submit}" {TAG_INPUT_ENABLED}>
    <input type="reset" name="reset" value="{lang:Reset}">
  </p>
</form>
