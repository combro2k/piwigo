<?php
// +-----------------------------------------------------------------------+
// |                           en_EN/common.lang.php                           |
// +-----------------------------------------------------------------------+
// | application   : PhpWebGallery <http://phpwebgallery.net>              |
// | branch        : 1.4                                                   |
// +-----------------------------------------------------------------------+
// | file          : $RCSfile$
// | last update   : $Date$
// | last modifier : $Author$
// | revision      : $Revision$
// +-----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation                                          |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program; if not, write to the Free Software           |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
// | USA.                                                                  |
// +-----------------------------------------------------------------------+

// Langage informations
$lang_info['language_name'] = 'English';
$lang_info['country'] = 'Great Britain';
$lang_info['charset'] = 'iso-8859-1';
$lang_info['direction'] = 'ltr';
$lang_info['code'] = 'en';

// Main words 

$lang['gallery_index'] = 'Gallery index';
$lang['category'] = 'Category';
$lang['categories'] = 'Categories';
$lang['thumbnail'] = 'Thumbnail';
$lang['thumbnails'] = 'Thumbnails';
$lang['search'] = 'Search';
$lang['comment'] = 'Comment';
$lang['comments'] = 'Comments';
$lang['picture'] = 'Picture';
$lang['pictures'] = 'Pictures';
$lang['no'] = 'No';
$lang['yes'] = 'Yes';
$lang['date'] = 'Date';
$lang['description'] = 'Description';

// Form words 
$lang['submit'] = 'Submit';
$lang['delete'] = 'Delete';
$lang['reset'] = 'Reset';

// Identification
$lang['login'] = 'Login';
$lang['logout'] = 'Logout';
$lang['password'] = 'Password';
$lang['customize'] = 'Customize';
$lang['new'] = 'New';
$lang['identification'] = 'Identification';

//Calendar
$lang['calendar'] = 'calendar';
$lang['calendar_hint'] = 'displays each day with pictures, month per month';
$lang['calendar_picture_hint'] = 'displays pictures added on ';
$lang['month'][1] = 'January';
$lang['month'][2] = 'February';
$lang['month'][3] = 'March';
$lang['month'][4] = 'April';
$lang['month'][5] = 'May';
$lang['month'][6] = 'June';
$lang['month'][7] = 'July';
$lang['month'][8] = 'August';
$lang['month'][9] = 'September';
$lang['month'][10] = 'October';
$lang['month'][11] = 'November';
$lang['month'][12] = 'December';
$lang['day'][0] = 'Sunday';
$lang['day'][1] = 'Monday';
$lang['day'][2] = 'Tuesday';
$lang['day'][3] = 'Wednesday';
$lang['day'][4] = 'Thursday';
$lang['day'][5] = 'Friday';
$lang['day'][6] = 'Saturday';

$lang['only_members'] = 'Only members can access this page';
$lang['invalid_pwd'] = 'Invalid password!';
$lang['access_forbiden'] = 'You are not authorized to access this page';
$lang['diapo_default_page_title'] = 'No category selected';
$lang['hint_category'] = 'shows images at the root of this categry';
$lang['total_images'] = 'total';
$lang['title_menu'] = 'Menu';
$lang['change_login'] = 'change login';
$lang['hint_login'] = 'identification enables site\'s appareance customization';
$lang['hint_customize'] = 'customize the appareance of the gallery';
$lang['hint_search'] = 'search';
$lang['favorite_cat'] = 'favorites';
$lang['favorite_cat_hint'] = 'display your favorites';
$lang['about'] = 'about';
$lang['hint_about'] = 'more informations on PhpWebGallery...';
$lang['admin'] = 'Administration';
$lang['hint_admin'] = 'available for administrators only';
$lang['no_category'] = 'Home';
$lang['page_number'] = 'page number';
$lang['previous_page'] = 'Previous';
$lang['next_page'] = 'Next';
$lang['recent_image'] = 'Image within the';
$lang['days'] = 'days';
$lang['send_mail'] = 'Contact the webmaster';
$lang['title_send_mail'] = 'A comment on your site';
$lang['sub-cat'] = 'subcategories';
$lang['images_available'] = 'images in this category';
$lang['total'] = 'images';
$lang['upload_picture'] = 'Upload a picture';
$lang['registration_date'] = 'registered on';
$lang['creation_date'] = 'created on';
$lang['author'] = 'author';
$lang['size'] = 'size';
$lang['filesize'] = 'filesize';
$lang['file'] = 'file';
$lang['generation_time'] = 'Page generated in';
$lang['favorites'] = 'Favorites';
$lang['search_result'] = 'Search results';
$lang['about_page_title'] = 'About PhpWebGallery';
$lang['about_title'] = 'About...';
if (isset($conf))
$lang['about_message'] = '<div style="text-align:center;font-weigh:bold;">Information about PhpWebGallery</div>
<ul>
  <li>This website uses <a href="'.$conf['site_url'].'" style="text-decoration:underline">PhpWebGallery</a> release '.$conf['version'].'. PhpWebGallery is a web application giving you the possibility to create an online images gallery easily.</li>
  <li>Technicaly, PhpWebGallery is fully developped with PHP (the elePHPant) with a MySQL database (the SQuirreL).</li>
  <li>If you have any suggestions or comments, please visit <a href="'.$conf['site_url'].'" style="text-decoration:underline">PhpWebGallery</a> official site, and its dedicated <a href="'.$conf['forum_url'].'" style="text-decoration:underline">forum</a>.</li>
</ul>';
$lang['about_return'] = 'Back';
$lang['ident_page_title'] = 'Identification';
$lang['ident_title'] = 'Identification';
$lang['ident_register'] = 'Register';
$lang['ident_forgotten_password'] = 'Forget your password ?';
$lang['ident_guest_visit'] = 'Go through the gallery as a visitor';
$lang['customize_page_title'] = 'Customization';
$lang['customize_title'] = 'Customization';
$lang['customize_nb_image_per_row'] = 'number of images per row';
$lang['customize_nb_row_per_page'] = 'number of rows per page';
$lang['customize_language'] = 'language';
$lang['maxwidth'] = 'maximum width of the pictures';
$lang['maxheight'] = 'maximum height of the pictures';
$lang['err_maxwidth'] = 'maximum width must be a number superior to 50';
$lang['err_maxheight'] = 'maximum height must be a number superior to 50';
$lang['previous_image'] = 'Previous';
$lang['next_image'] = 'Next';
$lang['back'] = 'Click on the image to go back to the thumbnails page';
$lang['info_image_title'] = 'Image information';
$lang['link_info_image'] = 'Modify information';
$lang['true_size'] = 'Real size';
$lang['comments_title'] = 'Comments from the users of the site';
$lang['comments_del'] = 'delete this comment';
$lang['comments_add'] = 'Add a comment';

$lang['add_favorites_alt'] = 'Add to favorites';
$lang['add_favorites_hint'] = 'Add this picture to your favorites';
$lang['del_favorites_alt'] = 'Delete from favorites';
$lang['del_favorites_hint'] = 'Delete this picture from your favorites';
$lang['register_page_title'] = 'Registration';
$lang['register_title'] = 'Registration';
$lang['reg_err_login1'] = 'Please, enter a login';
$lang['reg_err_login2'] = 'login mustn\'t end with a space character';
$lang['reg_err_login3'] = 'login mustn\'t start with a space character';
$lang['reg_err_login4'] = 'login mustn\'t contain characters " and \'';
$lang['reg_err_login5'] = 'this login is already used';
$lang['reg_err_pass'] = 'please enter your password again';
$lang['reg_confirm'] = 'confirm';
$lang['reg_err_mail_address'] = 'mail address must be like xxx@yyy.eee (example : jack@altern.org)';
$lang['search_title'] = 'Search';
$lang['invalid_search'] = 'search must be done on 3 caracters or more';
$lang['search_return_main_page'] = 'Return to thumbnails page';
$lang['upload_forbidden'] = 'You can\'t upload pictures in this category';
$lang['upload_file_exists'] = 'A picture\'s name already used';
$lang['upload_filenotfound'] = 'You must choose a picture fileformat for the image';
$lang['upload_cannot_upload'] = 'can\'t upload the picture on the server';
$lang['upload_title'] = 'Upload a picture';
$lang['upload_advise'] = 'Choose an image to place in the category : ';
$lang['upload_advise_thumbnail'] = 'Optional, but recommended : choose a thumbnail to associate to ';
$lang['upload_advise_filesize'] = 'the filesize of the picture must not exceed : ';
$lang['upload_advise_width'] = 'the width of the picture must not exceed : ';
$lang['upload_advise_height'] = 'the height of the picture must not exceed : ';
$lang['upload_advise_filetype'] = 'the picture must be to the fileformat jpg, gif or png';
$lang['upload_err_username'] = 'the username must be given';
$lang['upload_username'] = 'Username';
$lang['upload_successful'] = 'Picture uploaded with success, an administrator will validate it as soon as possible';

$lang['guest'] = 'guest';
$lang['mail_address'] = 'mail address';
$lang['add'] = 'add';
$lang['dissociate'] = 'dissociate';
$lang['mandatory'] = 'obligatory';
$lang['err_date'] = 'wrong date';
$lang['picture'] = 'picture';
$lang['IP'] = 'IP';
$lang['close'] = 'close';
$lang['open'] = 'open';
$lang['keywords'] = 'keywords';
$lang['errors_title'] = 'Errors';
$lang['infos_title'] = 'Informations';
$lang['default'] = 'default';
$lang['category_representative'] = 'representative';
$lang['special_categories'] = 'specials';
$lang['most_visited_cat_hint'] = 'displays most visited pictures';
$lang['most_visited_cat'] = 'most visited';
$lang['best_rated_cat_hint'] = 'displays pictures best rated';
$lang['best_rated_cat'] = 'best rated';
$lang['recent_pics_cat_hint'] = 'Displays most recent pictures';
$lang['recent_pics_cat'] = 'Last pictures';
$lang['recent_cats_cat_hint'] = 'Displays recently updated categories';
$lang['recent_cats_cat'] = 'Last categories';
$lang['visited'] = 'visited';
$lang['times'] = 'times';
$lang['customize_theme'] = 'interface theme';
$lang['customize_expand'] = 'expand all categories';
$lang['customize_show_nb_comments'] = 'show number of comments';
$lang['customize_recent_period'] = 'recent period';
$lang['customize_template'] = 'template';
$lang['err_periods'] = 'recent period must be a positive integer value';
$lang['create_cookie'] = 'create a cookie';
$lang['customize_day'] = 'day';
$lang['customize_week'] = 'week';
$lang['customize_month'] = 'month';
$lang['customize_year'] = 'year';
$lang['slideshow'] = 'slideshow';
$lang['period_seconds'] = 'seconds per picture';
$lang['slideshow_stop'] = 'stop the slideshow';
$lang['download'] = 'download';
$lang['download_hint'] = 'download this file';
$lang['comment_added'] = 'Your comment has been registered';
$lang['comment_to_validate'] = 'An administrator must authorize your comment before it is visible.';
$lang['comment_anti-flood'] = 'Anti-flood system : please wait for a moment before trying to post another comment';
$lang['comment_user_exists'] = 'This login is already used by another user';
$lang['invalid_search'] = 'Searched words must be grater than 3 characters and must not contain punctuation mark';
$lang['search_mode_or'] = 'at least one word';
$lang['search_mode_and'] = 'all the words';
$lang['search_comments'] = 'separate different words with spaces';
$lang['upload_name'] = 'Name of the picture';
$lang['upload_author'] = 'Author (eg "Pierrick LE GALL")';
$lang['upload_creation_date'] = 'Creation date (DD/MM/YYYY)';
$lang['upload_comment'] = 'Comment';
$lang['mail_hello'] = 'Hi,';
$lang['mail_new_upload_subject'] = 'New picture on the website';
$lang['mail_new_upload_content'] = 'A new picture has been uploaded on the gallery. It is waiting for your validation. Let\'s meet in the administration panel to authorize or refuse this picture.';
$lang['mail_new_comment_subject'] = 'New comment on website';
$lang['mail_new_comment_content'] = 'A new comment has been registered on the gallery. If you chose to validate each comment, you first have to validate this comment in the administration panel to make it visible in the gallery.'."\n\n".'You can see last comments in the administration panel';
$lang['connected_user'] = 'connected user';
$lang['title_comments'] = 'Users comments';
$lang['stats_last_days'] = 'last days';
$lang['hint_comments'] = 'See last users comments';
$lang['menu_login'] = 'login';
$lang['update_wrong_dirname'] = 'The name of directories and files must be composed of letters, figures, "-", "_" or "."';
$lang['hello'] = 'Hello';

// search
$lang['search_wrong_date'] = ' : this date is not valid';
$lang['search_wrong_date_order'] = 'the period end must be after the period start';
$lang['search_incoherent_date_search'] = 'when choosing to match all the clauses, you can\'t search "date is" and "date is after" or "date is before"';
$lang['search_or_clauses'] = 'at least one search clause';
$lang['search_and_clauses'] = 'all search clauses';
$lang['search_categories'] = 'categories';
$lang['search_subcats_included'] = 'include sub-categories';
$lang['search_date_included'] = 'included';
$lang['search_date_is'] = 'is';
$lang['search_date_is_after'] = 'is after';
$lang['search_date_is_before'] = 'is before';
$lang['search_file'] = 'file';
$lang['search_name'] = 'name';
$lang['search_comment'] = 'comment';
$lang['search_keywords'] = 'keywords';
$lang['search_author'] = 'author';
$lang['search_date_available'] = 'availability date';
$lang['search_date_creation'] = 'creation date';
$lang['search_one_clause_at_least'] = 'search at least on one search clause';

$lang['picture_show_metadata'] = 'Show file metadata ?';
$lang['picture_hide_metadata'] = 'Hide file metadata';
?>