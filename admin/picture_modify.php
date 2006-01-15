<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2005 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
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

if(!defined("PHPWG_ROOT_PATH"))
{
  die('Hacking attempt!');
}
include_once(PHPWG_ROOT_PATH.'admin/include/isadmin.inc.php');

// +-----------------------------------------------------------------------+
// |                          synchronize metadata                         |
// +-----------------------------------------------------------------------+

if (isset($_GET['sync_metadata']))
{
  $query = '
SELECT path
  FROM '.IMAGES_TABLE.'
  WHERE id = '.$_GET['image_id'].'
;';
  list($path) = mysql_fetch_row(pwg_query($query));
  update_metadata(array($_GET['image_id'] => $path));

  array_push($page['infos'], l10n('Metadata synchronized from file'));
}

//--------------------------------------------------------- update informations

// first, we verify whether there is a mistake on the given creation date
if (isset($_POST['date_creation_action'])
    and 'set' == $_POST['date_creation_action'])
{
  if (!checkdate(
        $_POST['date_creation_month'],
        $_POST['date_creation_day'],
        $_POST['date_creation_year'])
    )
  {
    array_push($page['errors'], $lang['err_date']);
  }
}

if (isset($_POST['submit']) and count($page['errors']) == 0)
{
  $data = array();
  $data{'id'} = $_GET['image_id'];
  $data{'name'} = $_POST['name'];
  $data{'author'} = $_POST['author'];

  if ($conf['allow_html_descriptions'])
  {
    $data{'comment'} = @$_POST['description'];
  }
  else
  {
    $data{'comment'} = strip_tags(@$_POST['description']);
  }

  if (isset($_POST['date_creation_action']))
  {
    if ('set' == $_POST['date_creation_action'])
    {
      $data{'date_creation'} = $_POST['date_creation_year']
                                 .'-'.$_POST['date_creation_month']
                                 .'-'.$_POST['date_creation_day'];
    }
    else if ('unset' == $_POST['date_creation_action'])
    {
      $data{'date_creation'} = '';
    }
  }

  $keywords = get_keywords($_POST['keywords']);
  if (count($keywords) > 0)
  {
    $data{'keywords'} = implode(',', $keywords);
  }
  else
  {
    $data{'keywords'} = '';
  }

  mass_updates(
    IMAGES_TABLE,
    array(
      'primary' => array('id'),
      'update' => array_diff(array_keys($data), array('id'))
      ),
    array($data)
    );

  array_push($page['infos'], l10n('Picture informations updated'));
}
// associate the element to other categories than its storage category
if (isset($_POST['associate'])
    and isset($_POST['cat_dissociated'])
    and count($_POST['cat_dissociated']) > 0)
{
  $datas = array();
  foreach ($_POST['cat_dissociated'] as $category_id)
  {
    array_push($datas, array('image_id' => $_GET['image_id'],
                             'category_id' => $category_id));
  }
  mass_inserts(IMAGE_CATEGORY_TABLE, array('image_id', 'category_id'), $datas);

  update_category($_POST['cat_dissociated']);
}
// dissociate the element from categories (but not from its storage category)
if (isset($_POST['dissociate'])
    and isset($_POST['cat_associated'])
    and count($_POST['cat_associated']) > 0)
{
  $query = '
DELETE FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE image_id = '.$_GET['image_id'].'
    AND category_id IN ('.implode(',',$_POST['cat_associated'] ).')
';
  pwg_query($query);
  update_category($_POST['cat_associated']);
}
// elect the element to represent the given categories
if (isset($_POST['elect'])
    and isset($_POST['cat_dismissed'])
    and count($_POST['cat_dismissed']) > 0)
{
  $datas = array();
  foreach ($_POST['cat_dismissed'] as $category_id)
  {
    array_push($datas,
               array('id' => $category_id,
                     'representative_picture_id' => $_GET['image_id']));
  }
  $fields = array('primary' => array('id'),
                  'update' => array('representative_picture_id'));
  mass_updates(CATEGORIES_TABLE, $fields, $datas);
}
// dismiss the element as representant of the given categories
if (isset($_POST['dismiss'])
    and isset($_POST['cat_elected'])
    and count($_POST['cat_elected']) > 0)
{
  set_random_representant($_POST['cat_elected']);
}

// retrieving direct information about picture
$query = '
SELECT *
  FROM '.IMAGES_TABLE.'
  WHERE id = '.$_GET['image_id'].'
;';
$row = mysql_fetch_array(pwg_query($query));

$storage_category_id = $row['storage_category_id'];

// Navigation path

$date = isset($_POST['date_creation']) && empty($page['errors'])
?$_POST['date_creation']:date_convert_back(@$row['date_creation']);

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

$template->set_filenames(
  array(
    'picture_modify' => 'admin/picture_modify.tpl'
    )
  );

$template->assign_vars(
  array(
    'U_SYNC' =>
        PHPWG_ROOT_PATH.'admin.php?page=picture_modify'.
        '&amp;image_id='.$_GET['image_id'].
        (isset($_GET['cat_id']) ? '&amp;cat_id='.$_GET['cat_id'] : '').
        '&amp;sync_metadata=1',
    
    'PATH'=>$row['path'],
    
    'TN_SRC' => get_thumbnail_src($row['path'], @$row['tn_ext']),
    
    'NAME' =>
      isset($_POST['name']) ?
        stripslashes($_POST['name']) : @$row['name'],
    
    'DIMENSIONS' => @$row['width'].' * '.@$row['height'],
    
    'FILESIZE' => @$row['filesize'].' KB',
    
    'REGISTRATION_DATE' =>
      format_date($row['date_available'], 'mysql_datetime', false),
    
    'AUTHOR' => isset($_POST['author']) ? $_POST['author'] : @$row['author'],
    
    'CREATION_DATE' => $date,
    
    'KEYWORDS' =>
      isset($_POST['keywords']) ?
        stripslashes($_POST['keywords']) : @$row['keywords'],
    
    'DESCRIPTION' =>
      isset($_POST['description']) ?
        stripslashes($_POST['description']) : @$row['comment'],
  
    'F_ACTION' =>
        PHPWG_ROOT_PATH.'admin.php'
        .get_query_string_diff(array('sync_metadata'))
    )
  );

// creation date
unset($day, $month, $year);

if (isset($_POST['date_creation_action'])
    and 'set' == $_POST['date_creation_action'])
{
  foreach (array('day', 'month', 'year') as $varname)
  {
    $$varname = $_POST['date_creation_'.$varname];
  }
}
else if (isset($row['date_creation']) and !empty($row['date_creation']))
{
  list($year, $month, $day) = explode('-', $row['date_creation']);
}
else
{
  list($year, $month, $day) = array('', 0, 0);
}
get_day_list('date_creation_day', $day);
get_month_list('date_creation_month', $month);
$template->assign_vars(array('DATE_CREATION_YEAR_VALUE' => $year));
  
$query = '
SELECT category_id, uppercats
  FROM '.IMAGE_CATEGORY_TABLE.' AS ic
    INNER JOIN '.CATEGORIES_TABLE.' AS c
      ON c.id = ic.category_id
  WHERE image_id = '.$_GET['image_id'].'
;';
$result = pwg_query($query);

if (mysql_num_rows($result) > 1)
{
  $template->assign_block_vars('links', array());
}

while ($row = mysql_fetch_array($result))
{
  $name =
    get_cat_display_name_cache(
      $row['uppercats'],
      PHPWG_ROOT_PATH.'admin.php?page=cat_modify&amp;cat_id=',
      false
      );
    
  if ($row['category_id'] == $storage_category_id)
  {
    $template->assign_vars(array('STORAGE_CATEGORY' => $name));
  }
  else
  {
    $template->assign_block_vars('links.category', array('NAME' => $name));
  }
}

// jump to link
//
// 1. find all linked categories that are reachable for the current user.
// 2. if a category is available in the URL, use it if reachable
// 3. if URL category not available or reachable, use the first reachable
//    linked category
// 4. if no category reachable, no jumpto link
$base_url_img = PHPWG_ROOT_PATH.'picture.php';
$base_url_img.= '?image_id='.$_GET['image_id'];
$base_url_img.= '&amp;cat=';
unset($url_img);

$query = '
SELECT category_id
  FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE image_id = '.$_GET['image_id'].'
;';
$authorizeds = array_diff(
  array_from_query($query, 'category_id'),
  explode(',', calculate_permissions($user['id'], $user['status']))
  );

if (isset($_GET['cat_id'])
    and in_array($_GET['cat_id'], $authorizeds))
{
  $url_img = $base_url_img.$_GET['cat_id'];
}
else
{
  foreach ($authorizeds as $category)
  {
    $url_img = $base_url_img.$category;
    break;
  }
}

if (isset($url_img))
{
  $template->assign_block_vars(
    'jumpto',
    array(
      'URL' => $url_img
      )
    );
}
  
// associate to another category ?
$query = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' ON id = category_id
  WHERE image_id = '.$_GET['image_id'].'
    AND id != '.$storage_category_id.'
;';
display_select_cat_wrapper($query, array(), 'associated_option');

$result = pwg_query($query);
$associateds = array($storage_category_id);
while ($row = mysql_fetch_array($result))
{
  array_push($associateds, $row['id']);
}
$query = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE id NOT IN ('.implode(',', $associateds).')
;';
display_select_cat_wrapper($query, array(), 'dissociated_option');

// representing
$query = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE representative_picture_id = '.$_GET['image_id'].'
;';
display_select_cat_wrapper($query, array(), 'elected_option');

$query = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE representative_picture_id != '.$_GET['image_id'].'
    OR representative_picture_id IS NULL
;';
display_select_cat_wrapper($query, array(), 'dismissed_option');

//----------------------------------------------------------- sending html code

$template->assign_var_from_handle('ADMIN_CONTENT', 'picture_modify');
?>
