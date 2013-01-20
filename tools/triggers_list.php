<?php
$core = array(
array(
  'name' => 'allow_increment_element_hit_count',
  'type' => 'trigger_event',
  'vars' => array('bool', 'content_not_set'),
  'files' => array('picture.php'),
),
array(
  'name' => 'begin_delete_elements',
  'type' => 'trigger_action',
  'vars' => array('array', 'ids'),
  'files' => array('admin\include\functions.inc.php (delete_elements)'),
),
array(
  'name' => 'blockmanager_apply',
  'type' => 'trigger_action',
  'vars' => array('object', 'menublock'),
  'files' => array('include\block.class.php (BlockManager::apply)'),
  'infos' => 'use this trigger to modify existing menu blocks',
),
array(
  'name' => 'blockmanager_prepare_display',
  'type' => 'trigger_action',
  'vars' => array('object', 'this'),
  'files' => array('include\block.class.php (BlockManager::prepare_display)'),
),
array(
  'name' => 'blockmanager_register_blocks',
  'type' => 'trigger_action',
  'vars' => array('object', 'menu'),
  'files' => array('include\block.class.php (BlockManager::load_registered_blocks)'),
  'infos' => 'use this trigger to add menu block',
),
array(
  'name' => 'clean_iptc_value',
  'type' => 'trigger_event',
  'vars' => array('string', 'value'),
  'files' => array('include\functions_metadata.inc.php (clean_iptc_value)'),
),
array(
  'name' => 'combined_css',
  'type' => 'trigger_event',
  'vars' => array('string', 'href', 'int', 'file_ver0', 'int', 'file_ver1'),
  'files' => array('include\template.class.php (Template::flush)'),
),
array(
  'name' => 'combined_css_postfilter',
  'type' => 'trigger_event',
  'vars' => array('string', 'css'),
  'files' => array('include\template.class.php (Template::process_css)'),
),
array(
  'name' => 'combined_script',
  'type' => 'trigger_event',
  'vars' => array('string', 'ret', 'string', 'script'),
  'files' => array('include\template.class.php (Template::make_script_src)'),
),
array(
  'name' => 'delete_categories',
  'type' => 'trigger_action',
  'vars' => array('array', 'ids'),
  'files' => array('admin\include\functions.inc.php (delete_categories)'),
),
array(
  'name' => 'delete_elements',
  'type' => 'trigger_action',
  'vars' => array('array', 'ids'),
  'files' => array('admin\include\functions.inc.php (delete_elements)'),
),
array(
  'name' => 'delete_user',
  'type' => 'trigger_action',
  'vars' => array('int', 'user_id'),
  'files' => array('admin\include\functions.inc.php (delete_user)'),
),
array(
  'name' => 'element_set_global_action',
  'type' => 'trigger_action',
  'vars' => array('string', 'action', 'array', 'collection'),
  'files' => array('admin\batch_manager_global.php'),
),
array(
  'name' => 'format_exif_data',
  'type' => 'trigger_event',
  'vars' => array('array', 'exif', 'string', 'filename', 'array', 'map'),
  'files' => array('include\functions_metadata.inc.php (get_exif_data)'),
),
array(
  'name' => 'functions_history_included',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('admin\include\functions_history.inc.php'),
),
array(
  'name' => 'functions_mail_included',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('include\functions_mail.inc.php'),
),
array(
  'name' => 'get_admin_advanced_features_links',
  'type' => 'trigger_event',
  'vars' => array('array', 'advanced_features'),
  'files' => array('admin\maintenance.php'),
),
array(
  'name' => 'get_admin_plugin_menu_links',
  'type' => 'trigger_event',
  'vars' => array('array', null),
  'files' => array('admin.php'),
  'infos' => 'use this trigger to add links into admin plugins menu',
),
array(
  'name' => 'get_admins_site_links',
  'type' => 'trigger_event',
  'vars' => array('array', 'plugin_links', 'int', 'site_id', 'bool', 'is_remote'),
  'files' => array('admin\site_manager.php'),
),
array(
  'name' => 'get_batch_manager_prefilters',
  'type' => 'trigger_event',
  'vars' => array('array', 'prefilters'),
  'files' => array('admin\batch_manager_global.php'),
  'infos' => 'use this trigger to add prefilters into batch manager global',
),
array(
  'name' => 'get_categories_menu_sql_where',
  'type' => 'trigger_event',
  'vars' => array('string', 'where', 'bool', 'user_expand', 'bool', 'filter_enabled'),
  'files' => array('include\functions_category.inc.php (get_categories_menu)'),
),
array(
  'name' => 'get_category_preferred_image_orders',
  'type' => 'trigger_event',
  'vars' => array('array', null),
  'files' => array('include\functions_category.inc.php (get_category_preferred_image_orders)'),
),
array(
  'name' => 'get_download_url',
  'type' => 'trigger_event',
  'vars' => array('string', 'url', 'array', 'element_info'),
  'files' => array('include\functions_picture.inc.php (get_download_url)'),
),
array(
  'name' => 'get_element_metadata_available',
  'type' => 'trigger_event',
  'vars' => array('bool', null, 'string', 'element_path'),
  'files' => array('picture.php'),
),
array(
  'name' => 'get_element_url',
  'type' => 'trigger_event',
  'vars' => array('string', 'url', 'array', 'element_info'),
  'files' => array('include\functions_picture.inc.php (get_element_url)'),
),
array(
  'name' => 'get_high_location',
  'type' => 'trigger_event',
  'vars' => array('string', 'location', 'array', 'element_info'),
  'files' => array('include\functions_picture.inc.php (get_high_location)'),
),
array(
  'name' => 'get_high_url',
  'type' => 'trigger_event',
  'vars' => array('string', 'url', 'array', 'element_info'),
  'files' => array('include\functions_picture.inc.php (get_high_url)'),
),
array(
  'name' => 'get_history',
  'type' => 'trigger_event',
  'vars' => array('array', null, 'array', 'page_search', 'array', 'types'),
  'files' => array('admin\history.php'),
),
array(
  'name' => 'get_image_location',
  'type' => 'trigger_event',
  'vars' => array('string', 'path', 'array', 'element_info'),
  'files' => array('include\functions_picture.inc.php (get_image_location)'),
),
array(
  'name' => 'get_popup_help_content',
  'type' => 'trigger_event',
  'vars' => array('string', 'help_content', 'string', 'page'),
  'files' => array('admin\popuphelp.php', 'popuphelp.php'),
),
array(
  'name' => 'get_pwg_themes',
  'type' => 'trigger_event',
  'vars' => array('array', 'themes'),
  'files' => array('include\functions.inc.php (get_pwg_themes)'),
),
array(
  'name' => 'get_thumbnail_title',
  'type' => 'trigger_event',
  'vars' => array('string', 'title', 'array', 'info'),
  'files' => array('include\functions.inc.php (get_thumbnail_title)'),
),
array(
  'name' => 'get_comments_derivative_params',
  'type' => 'trigger_event',
  'vars' => array('ImageStdParams', null),
  'files' => array('comments.php'),
  'infos' => 'New in 2.4',
),
array(
  'name' => 'get_index_album_derivative_params',
  'type' => 'trigger_event',
  'vars' => array('ImageStdParams', null),
  'files' => array('includecategory_cats.php', 'include\category_default.inc.php'),
  'infos' => 'New in 2.4',
),
array(
  'name' => 'get_src_image_url',
  'type' => 'trigger_event',
  'vars' => array('string', 'url', 'SrcImage', 'this'),
  'files' => array('include\derivative.inc.php (SrcImage::__construct)'),
  'infos' => 'New in 2.4',
),
array(
  'name' => 'get_derivative_url',
  'type' => 'trigger_event',
  'vars' => array('string', 'url', 'ImageStdParams', null, 'SrcImage', 'this', 'string', 'rel_url'),
  'files' => array('include\derivative.inc.php (SrcImage::url, SrcImage::get_url)'),
  'infos' => 'New in 2.4',
),
array(
  'name' => 'get_tag_alt_names',
  'type' => 'trigger_event',
  'vars' => array('array', null, 'array', 'raw_name'),
  'files' => array('admin\tags.php', 'admin\include\functions.php (get_taglist)'),
  'infos' => 'New in 2.4',
),
array(
  'name' => 'init',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('include\common.inc.php'),
  'infos' => 'this action is called just after the common initialization, $conf, $user and $page (partial) variables are availables',
),
array(
  'name' => 'invalidate_user_cache',
  'type' => 'trigger_action',
  'vars' => array('bool', 'full'),
  'files' => array('admin\include\functions.inc.php (invalidate_user_cache)'),
),
array(
  'name' => 'list_check_integrity',
  'type' => 'trigger_action',
  'vars' => array('object', 'this'),
  'files' => array('admin\include\check_integrity.class.php (check_integrity::check)'),
),
array(
  'name' => 'load_image_library',
  'type' => 'trigger_action',
  'vars' => array('object', 'this'),
  'files' => array('admin\include\image.class.php (pwg_image::__construct)'),
),
array(
  'name' => 'load_profile_in_template',
  'type' => 'trigger_action',
  'vars' => array('array', 'userdata'),
  'files' => array('profile.php (load_profile_in_template)'),
),
array(
  'name' => 'loading_lang',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('include\common.inc.php', 'include\functions.inc.php (redirect_html)', 'include\functions_mail.inc.php (switch_lang_to)', 'nbm.php'),
),
array(
  'name' => 'loc_after_page_header',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('include\page_header.php'),
),
array(
  'name' => 'loc_begin_about',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('about.php'),
),
array(
  'name' => 'loc_begin_admin',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('admin.php'),
),
array(
  'name' => 'loc_begin_admin_page',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('admin.php'),
),
array(
  'name' => 'loc_begin_cat_list',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('admin\cat_list.php'),
),
array(
  'name' => 'loc_begin_cat_modify',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('admin\cat_modify.php'),
),
array(
  'name' => 'loc_begin_element_set_global',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('admin\batch_manager_global.php'),
),
array(
  'name' => 'loc_begin_element_set_unit',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('admin\batch_manager_unit.php'),
),
array(
  'name' => 'loc_begin_index',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('index.php'),
),
array(
  'name' => 'loc_begin_index_category_thumbnails',
  'type' => 'trigger_action',
  'vars' => array('array', 'categories'),
  'files' => array('include\category_cats.inc.php'),
),
array(
  'name' => 'loc_begin_index_thumbnails',
  'type' => 'trigger_action',
  'vars' => array('array', 'pictures'),
  'files' => array('include\category_default.inc.php'),
),
array(
  'name' => 'loc_begin_page_header',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('include\page_header.php'),
),
array(
  'name' => 'loc_begin_page_tail',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('include\page_tail.php'),
),
array(
  'name' => 'loc_begin_picture',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('picture.php'),
),
array(
  'name' => 'loc_begin_profile',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('profile.php'),
),
array(
  'name' => 'loc_begin_password',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('password.php'),
  'infos' => 'New in 2.5',
),
array(
  'name' => 'loc_begin_register',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('register.php'),
  'infos' => 'New in 2.5',
),
array(
  'name' => 'loc_begin_search',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('search.php'),
  'infos' => 'New in 2.5',
),
array(
  'name' => 'loc_begin_tags',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('tags.php'),
  'infos' => 'New in 2.5',
),
array(
  'name' => 'loc_begin_comments',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('comments.php'),
  'infos' => 'New in 2.5',
),
array(
  'name' => 'loc_begin_identification',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('identification.php'),
  'infos' => 'New in 2.5',
),
array(
  'name' => 'loc_begin_notification',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('notification.php'),
  'infos' => 'New in 2.5',
),
array(
  'name' => 'loc_end_password',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('password.php'),
  'infos' => 'New in 2.5',
),
array(
  'name' => 'loc_end_register',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('register.php'),
  'infos' => 'New in 2.5',
),
array(
  'name' => 'loc_end_search',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('search.php'),
  'infos' => 'New in 2.5',
),
array(
  'name' => 'loc_end_tags',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('tags.php'),
  'infos' => 'New in 2.5',
),
array(
  'name' => 'loc_end_comments',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('comments.php'),
  'infos' => 'New in 2.5',
),
array(
  'name' => 'loc_end_identification',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('identification.php'),
  'infos' => 'New in 2.5',
),
array(
  'name' => 'loc_end_notification',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('notification.php'),
  'infos' => 'New in 2.5',
),
array(
  'name' => 'loc_end_admin',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('admin.php'),
),
array(
  'name' => 'loc_end_cat_list',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('admin\cat_list.php'),
),
array(
  'name' => 'loc_end_cat_modify',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('admin\cat_modify.php'),
),
array(
  'name' => 'loc_end_element_set_global',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('admin\batch_manager_global.php'),
),
array(
  'name' => 'loc_end_element_set_unit',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('admin\batch_manager_unit.php'),
),
array(
  'name' => 'loc_end_index',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('index.php'),
),
array(
  'name' => 'loc_end_index_category_thumbnails',
  'type' => 'trigger_event',
  'vars' => array('array', 'tpl_thumbnails_var'),
  'files' => array('include\category_cats.inc.php'),
),
array(
  'name' => 'loc_end_index_thumbnails',
  'type' => 'trigger_event',
  'vars' => array('array', 'tpl_thumbnails_var', 'array', 'pictures'),
  'files' => array('include\category_default.inc.php'),
),
array(
  'name' => 'loc_end_page_header',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('include\page_header.php'),
),
array(
  'name' => 'loc_end_page_tail',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('include\page_tail.php'),
),
array(
  'name' => 'loc_end_picture',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('picture.php'),
),
array(
  'name' => 'loc_end_profile',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('profile.php'),
),
array(
  'name' => 'loc_end_section_init',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('include\section_init.inc.php'),
  'infos' => 'this action is called after section initilization, $page variable is fully defined',
),
array(
  'name' => 'loc_visible_user_list',
  'type' => 'trigger_event',
  'vars' => array('array', 'visible_user_list'),
  'files' => array('admin\user_list.php'),
),
array(
  'name' => 'login_failure',
  'type' => 'trigger_action',
  'vars' => array('string', 'username'),
  'files' => array('include\functions_user.inc.php (try_log_user)'),
),
array(
  'name' => 'login_success',
  'type' => 'trigger_action',
  'vars' => array('string', 'username'),
  'files' => array('include\functions_user.inc.php (auto_login, try_log_user)'),
),
array(
  'name' => 'mail_group_assign_vars',
  'type' => 'trigger_event',
  'vars' => array('array', 'assign_vars'),
  'files' => array('include\functions_mail.inc.php (pwg_mail_group)'),
),
array(
  'name' => 'nbm_event_handler_added',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('admin\notification_by_mail.php'),
),
array(
  'name' => 'nbm_render_global_customize_mail_content',
  'type' => 'trigger_event',
  'vars' => array('string', 'customize_mail_content'),
  'files' => array('admin\notification_by_mail.php (do_action_send_mail_notification)'),
),
array(
  'name' => 'nbm_render_user_customize_mail_content',
  'type' => 'trigger_event',
  'vars' => array('string', 'customize_mail_content', 'string', 'nbm_user'),
  'files' => array('admin\notification_by_mail.php (do_action_send_mail_notification)'),
),
array(
  'name' => 'perform_batch_manager_prefilters',
  'type' => 'trigger_event',
  'vars' => array('array', 'filter_sets', 'string', 'session_prefilter'),
  'files' => array('admin\batch_manager.php'),
),
array(
  'name' => 'picture_pictures_data',
  'type' => 'trigger_event',
  'vars' => array('array', 'picture'),
  'files' => array('picture.php'),
),
array(
  'name' => 'plugins_loaded',
  'type' => 'trigger_action',
  'vars' => array(),
  'files' => array('include\functions_plugins.inc.php (load_plugins)'),
),
array(
  'name' => 'pwg_log_allowed',
  'type' => 'trigger_event',
  'vars' => array('bool', 'do_log', 'int', 'image_id', 'string', 'image_type'),
  'files' => array('include\functions.inc.php (pwg_log)'),
),
array(
  'name' => 'register_user',
  'type' => 'trigger_action',
  'vars' => array('array', 'user'),
  'files' => array('include\functions_user.inc.php (register_user)'),
),
array(
  'name' => 'register_user_check',
  'type' => 'trigger_event',
  'vars' => array('array', 'errors', 'array', 'user'),
  'files' => array('include\functions_user.inc.php (register_user)'),
),
array(
  'name' => 'render_category_description',
  'type' => 'trigger_event',
  'vars' => array('string', 'category_description', 'string', 'action'),
  'files' => array('include\category_cats.inc.php', 'include\section_init.inc.php', 'include\ws_functions.inc.php (ws_categories_getList, ws_categories_getAdminList)'),
),
array(
  'name' => 'render_category_literal_description',
  'type' => 'trigger_event',
  'vars' => array('string', 'category_description'),
  'files' => array('include\category_cats.inc.php'),
),
array(
  'name' => 'render_category_name',
  'type' => 'trigger_event',
  'vars' => array('string', 'category_name', 'string', 'location'),
  'files' => array('admin\cat_list.php', 'include\ws_functions.inc.php (ws_categories_getList, ws_categories_getAdminList, ws_categories_move)'),
),
array(
  'name' => 'render_comment_author',
  'type' => 'trigger_event',
  'vars' => array('string', 'comment_author'),
  'files' => array('admin\comments.php', 'comments.php', 'include\picture_comment.inc.php'),
),
array(
  'name' => 'render_comment_content',
  'type' => 'trigger_event',
  'vars' => array('string', 'comment_content'),
  'files' => array('admin\comments.php', 'comments.php', 'include\picture_comment.inc.php'),
),
array(
  'name' => 'render_element_content',
  'type' => 'trigger_event',
  'vars' => array('string', 'content', 'array', 'current_picture'),
  'files' => array('picture.php'),
),
array(
  'name' => 'render_element_name',
  'type' => 'trigger_event',
  'vars' => array('string', 'element_name'),
  'files' => array('include\functions_html.inc.php (render_element_name)'),
),
array(
  'name' => 'render_element_description',
  'type' => 'trigger_event',
  'vars' => array('string', 'element_secription'),
  'files' => array('picture.php'),
),
array(
  'name' => 'render_lost_password_mail_content',
  'type' => 'trigger_event',
  'vars' => array('string', 'message'),
  'files' => array('password.php (process_password_request)'),
),
array(
  'name' => 'render_page_banner',
  'type' => 'trigger_event',
  'vars' => array('string', 'gallery_title'),
  'files' => array('include\page_header.php'),
),
array(
  'name' => 'render_tag_name',
  'type' => 'trigger_event',
  'vars' => array('string', 'tag_name'),
  'files' => array('admin\include\functions.php (get_taglist)', 'admin\tags.php', 'include\functions_tag.inc.php (get_available_tags, get_all_tags, get_common_tags)', 'index.php'),
),
array(
  'name' => 'render_tag_url',
  'type' => 'trigger_event',
  'vars' => array('string', 'tag_name'),
  'files' => array('include\functions.php (tag_id_from_tag_name, create_tag)', 'admin\tags.php'),
),
array(
  'name' => 'save_profile_from_post',
  'type' => 'trigger_action',
  'vars' => array('int', 'user_id'),
  'files' => array('profile.php (save_profile_from_post)'),
),
array(
  'name' => 'send_mail',
  'type' => 'trigger_event',
  'vars' => array('bool', 'result', 'string', 'mail_to', 'string', 'mail_subject', 'string', 'mail_content', 'array', 'mail_headers'),
  'files' => array('include\functions_mail.inc.php (pwg_mail)'),
),
array(
  'name' => 'send_mail_content',
  'type' => 'trigger_event',
  'vars' => array('string', 'content'),
  'files' => array('include\functions_mail.inc.php (pwg_mail)'),
),
array(
  'name' => 'send_mail_headers',
  'type' => 'trigger_event',
  'vars' => array('array', 'headers'),
  'files' => array('include\functions_mail.inc.php (pwg_mail)'),
),
array(
  'name' => 'send_mail_subject',
  'type' => 'trigger_event',
  'vars' => array('string', 'cvt_subject'),
  'files' => array('include\functions_mail.inc.php (pwg_mail)'),
),
array(
  'name' => 'send_mail_to',
  'type' => 'trigger_event',
  'vars' => array('string', 'to'),
  'files' => array('include\functions_mail.inc.php (pwg_mail)'),
),
array(
  'name' => 'sendResponse',
  'type' => 'trigger_action',
  'vars' => array('string', 'encodedResponse'),
  'files' => array('include\ws_core.inc.php (PwgServer::sendResponse)'),
),
array(
  'name' => 'set_status_header',
  'type' => 'trigger_action',
  'vars' => array('int', 'code', 'string', 'text'),
  'files' => array('include\functions_html.inc.php (set_status_header)'),
),
array(
  'name' => 'tabsheet_before_select',
  'type' => 'trigger_event',
  'vars' => array('array', 'sheets', 'string', 'tabsheet_id'),
  'files' => array('include\tabsheet.class.php (tabsheet::select)'),
  'infos' => 'New in 2.4, use this trigger to add tabs to a tabsheets',
),
array(
  'name' => 'trigger',
  'type' => 'trigger_action',
  'vars' => array('array', null),
  'files' => array('include\functions_plugins.inc.php (trigger_event, trigger_action)'),
),
array(
  'name' => 'user_comment_check',
  'type' => 'trigger_event',
  'vars' => array('string', 'comment_action', 'array', 'comm'),
  'files' => array('include\functions_comment.inc.php (insert_user_comment, update_user_comment)'),
  'infos' => 'use this trigger to add conditions on comment validation',
),
array(
  'name' => 'user_comment_deletion',
  'type' => 'trigger_action',
  'vars' => array('mixed', 'comment_id'),
  'files' => array('include\functions_comment.inc.php (delete_user_comment)'),
  'infos' => '$comment_id is and int or an array of int',
),
array(
  'name' => 'user_comment_insertion',
  'type' => 'trigger_action',
  'vars' => array('array', 'comm'),
  'files' => array('include\picture_comment.inc.php'),
),
array(
  'name' => 'user_comment_validation',
  'type' => 'trigger_action',
  'vars' => array('mixed', 'comment_id'),
  'files' => array('include\functions_comment.inc.php (validate_user_comment)'),
  'infos' => '$comment_id is and int or an array of int',
),
array(
  'name' => 'user_init',
  'type' => 'trigger_action',
  'vars' => array('array', 'user'),
  'files' => array('include\user.inc.php'),
),
array(
  'name' => 'ws_add_methods',
  'type' => 'trigger_action',
  'vars' => array('object', 'this'),
  'files' => array('include\ws_core.inc.php (PwgServer::run)'),
),
array(
  'name' => 'ws_invoke_allowed',
  'type' => 'trigger_event',
  'vars' => array('bool', null, 'string', 'methodName', 'array', 'params'),
  'files' => array('include\ws_core.inc.php (PwgServer::invoke)'),
),
array(
  'name' => 'user_logout',
  'type' => 'trigger_action',
  'vars' => array('int', 'user_id'),
  'files' => array('include\functions_user.inc.php (logout_user)'),
),
array(
  'name' => 'user_login',
  'type' => 'trigger_action',
  'vars' => array('int', 'user_id'),
  'files' => array('include\functions_user.inc.php (log_user)'),
),
array(
  'name' => 'try_log_user',
  'type' => 'trigger_event',
  'vars' => array('boolean', 'success', 'string', 'username', 'string', 'password', 'bool', 'remember_me'),
  'files' => array('include\functions_user.inc.php (try_log_user)'),
  'infos' => 'Used by identification form to check user credentials and log user. If success is true, another login method already succeed. Return true if your method succeed.',
),
);
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" dir="ltr">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Piwigo Core Triggers</title>
  
  <link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css">
  <link rel="stylesheet" type="text/css" href="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/css/jquery.dataTables_themeroller.css">
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
  <script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/jquery.dataTables.min.js"></script>
  
  <style type="text/css">
  /* BEGIN CSS RESET
    http://meyerweb.com/eric/tools/css/reset
    v2.0 | 20110126 | License: none (public domain) */
  html, body, div, span, applet, object, iframe, h1, h2, h3, h4, h5, h6, p, blockquote, pre, a, abbr, acronym, address, big, cite, code,
  del, dfn, em, img, ins, kbd, q, s, samp, small, strike, strong, sub, sup, tt, var, b, u, i, center, dl, dt, dd, ol, ul, li,
  fieldset, form, label, legend, table, caption, tbody, tfoot, thead, tr, th, td, article, aside, canvas, details, embed, 
  figure, figcaption, footer, header, hgroup, menu, nav, output, ruby, section, summary, time, mark, audio, video
  {margin:0;padding:0;border:0;font-size:100%;vertical-align:baseline;}

  article, aside, details, figcaption, figure, footer, header, hgroup, menu, nav, section {display:block;}
  body {line-height:1.1;}
  blockquote, q {quotes:none;}
  blockquote:before, blockquote:after, q:before, q:after {content:'';content:none;}
  table {border-collapse:collapse;border-spacing:0;}
  /* END CSS RESET */
  
  html {font-family:"Corbel","Lucida Grande","Verdana",sans-serif;color:#222;font-size:13px;}

  a {color:#247EBF;text-decoration:none;}
  a:hover {color:#EB9C39;border-bottom-width:1px;border-style:dotted;
    text-shadow:1px 1px 0 #ddd;-moz-text-shadow:1px 1px 0 #ddd;-webkit-text-shadow:1px 1px 0 #ddd;
  }

  h1 {color:#fff;font-size:26px;padding:10px 15px;
    text-shadow:1px 1px 0 #999;-moz-text-shadow:1px 1px 0 #999;-webkit-text-shadow:1px 1px 0 #999;
    background:#45484d;background:-moz-linear-gradient(top, #45484d 0%, #333333 100%);background:-webkit-gradient(linear, left top, left bottom, color-stop(0%,#45484d), color-stop(100%,#333333));background:-webkit-linear-gradient(top, #45484d 0%,#333333 100%);background:-o-linear-gradient(top, #45484d 0%,#333333 100%);background:-ms-linear-gradient(top, #45484d 0%,#333333 100%);background:linear-gradient(to bottom, #45484d 0%,#333333 100%);filter:progid:DXImageTransform.Microsoft.gradient( startColorstr='#45484d', endColorstr='#333333',GradientType=0 );
  }

  #the_header {border-bottom:1px solid #cdcdcd;margin-bottom:1px;}
  #the_footer {background:#EAEAEA;border-top:1px solid #cdcdcd;padding:10px;clear:both;}

  #the_page {padding:20px;background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAMAAAADCAIAAADZSiLoAAAAH0lEQVQImSXHMQEAMAwCMOrfK0jIjuVL2gLBzyHJtgd7wBdU3Vt/7AAAAABJRU5ErkJggg==);}
  
  tfoot input {width:80%;}
  tfoot .search_input {color:#999;}
  tfoot select.search_input option:not(:first-child) {color:#222;}
  </style>
</head>

<body>

<div id="the_header">
  <h1>Piwigo Core Triggers</h1>
</div> <!-- the_header -->

<div id="the_page">
  <table id="list">
  <thead>
    <tr>
      <th>Name</th>
      <th>Type</th>
      <th>Variables</th>
      <th>Usage in the core</th>
      <th>Commentary</th>
    </tr>
  </thead>
  <tbody>

  <?php  
    foreach ($core as $trigger)
    {
      echo '
    <tr>
      <td>'.$trigger['name'].'</td>
      <td>'.$trigger['type'].'</td>
      <td>';
      for ($i=0; $i<count($trigger['vars']); $i+=2)
      {
        if ($i>0) echo ', ';
        echo $trigger['vars'][$i].' '.(!empty($trigger['vars'][$i+1])?'<i>$'.$trigger['vars'][$i+1].'</i>':null);
      }
      echo '
      </td>
      <td>';
      $f=1;
      foreach ($trigger['files'] as $file)
      {
        if (!$f) echo '<br>'; $f=0;
        echo preg_replace('#\((.+)\)#', '(<i>$1</i>)', $file);
      }
      echo '
      </td>
      <td>'.@$trigger['infos'].'</td>
    </tr>';
    }
  ?>

  </tbody>
  <tfoot>
    <tr>
      <td><input type="text" value="Name" class="search_input"></td>
      <td>
        <select class="search_input">
          <option value="">Type</option>
          <option value="trigger_action">trigger_action</option>
          <option value="trigger_event">trigger_event</option>
        </select>
      </td>
      <td><input type="text" value="Variables" class="search_input"></td>
      <td><input type="text" value="Usage" class="search_input"></td>
      <td><input type="text" value="Commentary" class="search_input"></td>
    </tr>
  </tfoot>
  </table>
</div> <!-- the_page -->

<div id="the_footer">
  Copyright &copy; 2002-2013 <a href="http://piwigo.org">Piwigo Team</a>
</div> <!-- the_footer -->

  
<script type="text/javascript">
var oTable = $('#list').dataTable({
  "bJQueryUI": true,
  "aaSorting": [ [0,'asc'] ],
  "sPaginationType": "full_numbers",
  "aLengthMenu": [[10, 30, 50, 70, 90, -1], [10, 30, 50, 70, 90, "All"]],
  "iDisplayLength": 30,
  "oLanguage": {
      "sSearch": "Search all columns :"
  }
});

// search input
$("tfoot td").each(function (i) {
  $('select', this).change(function () {
    oTable.fnFilter($(this).val(), i);
  });
  $('input', this).keyup(function () {
    oTable.fnFilter($(this).val(), i);
  });
});

// search helpers
var asInitVals = new Array();
$("tfoot input").each(function (i) {
  asInitVals[i] = $(this).val();
});
 
$("tfoot input").focus(function () {
  if (this.className == "search_input") {
    $(this).removeClass("search_input");
    $(this).val("");
  }
});
 
$("tfoot input").blur(function (i) {
  if ($(this).val() == "") {
    $(this).addClass("search_input");
    $(this).val(asInitVals[$("tfoot input").index(this)]);
  }
});

$("tfoot select").change(function () {
  if ($(this).val() == "") {
    $(this).addClass("search_input");
  }
  else {
    $(this).removeClass("search_input");
  }
});
</script>

</body>
</html>