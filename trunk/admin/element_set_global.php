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

/**
 * Management of elements set. Elements can belong to a category or to the
 * user caddie.
 *
 */

if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

// +-----------------------------------------------------------------------+
// |                       global mode form submission                     |
// +-----------------------------------------------------------------------+

if (isset($_POST['submit']))
{
  $collection = array();

//   echo '<pre>';
//   print_r($_POST);
//   echo '</pre>';
//   exit();

  switch ($_POST['target'])
  {
    case 'all' :
    {
      $collection = $page['cat_elements_id'];
      break;
    }
    case 'selection' :
    {
      if (!isset($_POST['selection']) or count($_POST['selection']) == 0)
      {
        array_push($page['errors'], l10n('Select at least one picture'));
      }
      else
      {
        $collection = $_POST['selection'];
      }
      break;
    }
  }

  if (isset($_POST['add_tags']) and count($collection) > 0)
  {
    add_tags($_POST['add_tags'], $collection);
  }

  if (isset($_POST['del_tags']) and count($collection) > 0)
  {
    $query = '
DELETE
  FROM '.IMAGE_TAG_TABLE.'
  WHERE image_id IN ('.implode(',', $collection).')
    AND tag_id IN ('.implode(',', $_POST['del_tags']).')
;';
    pwg_query($query);
  }

  if ($_POST['associate'] != 0 and count($collection) > 0)
  {
    $datas = array();

    $query = '
SELECT image_id
  FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE category_id = '.$_POST['associate'].'
;';
    $associated = array_from_query($query, 'image_id');

    $associable = array_diff($collection, $associated);

    if (count($associable) != 0)
    {
      foreach ($associable as $item)
      {
        array_push(
          $datas,
          array(
            'category_id' => $_POST['associate'],
            'image_id' => $item
            )
          );
      }

      mass_inserts(
        IMAGE_CATEGORY_TABLE,
        array('image_id', 'category_id'),
        $datas
        );

      check_links();
      update_category(array($_POST['associate']));
    }
  }

  if ($_POST['dissociate'] != 0 and count($collection) > 0)
  {
    // First, we must identify which elements in the collection are really
    // virtually associated with the category
    $query = '
SELECT image_id
  FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE category_id = '.$_POST['dissociate'].'
    AND image_id IN ('.implode(',', $collection).')
    AND is_storage = \'false\'
;';
    $associated_images = array_from_query($query, 'image_id');

    // If the same element is associated to a source and its destinations,
    // dissociating the element with the source implies dissociating the
    // element fwith the destination.
    $destinations_of = get_destinations($_POST['dissociate']);

    $associated_categories = array_merge(
      array($_POST['dissociate']),
      $destinations_of[ $_POST['dissociate'] ]
      );

    // Eventually, deletion of associations
    $query = '
DELETE
  FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE category_id IN ('.implode(',', $associated_categories).'
    AND image_id IN ('.implode(',', $associated_images).')
';
    pwg_query($query);

    // check source/destination links. If category C has for sources A and
    // B, if picture 1 was associated to A and B, the previous code lines
    // have deleted the link between C and 1, while it should be kept due to
    // B. Who said "complicated"?
    check_links();

    update_category($associated_categories);
  }

  $datas = array();
  $dbfields = array('primary' => array('id'), 'update' => array());

  $formfields = array('author', 'name', 'date_creation');
  foreach ($formfields as $formfield)
  {
    if ($_POST[$formfield.'_action'] != 'leave')
    {
      array_push($dbfields['update'], $formfield);
    }
  }

  // updating elements is useful only if needed...
  if (count($dbfields['update']) > 0 and count($collection) > 0)
  {
    $query = '
SELECT id
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', $collection).')
;';
    $result = pwg_query($query);

    while ($row = mysql_fetch_array($result))
    {
      $data = array();
      $data['id'] = $row['id'];

      if ('set' == $_POST['author_action'])
      {
        $data['author'] = $_POST['author'];

        if ('' == $data['author'])
        {
          unset($data['author']);
        }
      }

      if ('set' == $_POST['name_action'])
      {
        $data['name'] = $_POST['name'];

        if ('' == $data['name'])
        {
          unset($data['name']);
        }
      }

      if ('set' == $_POST['date_creation_action'])
      {
        $data['date_creation'] =
          $_POST['date_creation_year']
          .'-'.$_POST['date_creation_month']
          .'-'.$_POST['date_creation_day']
          ;
      }

      array_push($datas, $data);
    }
    // echo '<pre>'; print_r($datas); echo '</pre>';
    mass_updates(IMAGES_TABLE, $dbfields, $datas);
  }
}

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+
$template->set_filenames(
  array('element_set_global' => 'admin/element_set_global.tpl'));

$base_url = PHPWG_ROOT_PATH.'admin.php';

// $form_action = $base_url.'?page=element_set_global';

$template->assign_vars(
  array(
    'CATEGORIES_NAV'=>$page['title'],

    'L_SUBMIT'=>$lang['submit'],

    'U_COLS'=>$base_url.get_query_string_diff(array('cols')),
    'U_DISPLAY'=>$base_url.get_query_string_diff(array('display')),

    'U_UNIT_MODE'
    =>
    $base_url
    .get_query_string_diff(array('mode','display'))
    .'&amp;mode=unit',

    'F_ACTION'=>$base_url.get_query_string_diff(array()),
   )
 );

// +-----------------------------------------------------------------------+
// |                            caddie options                             |
// +-----------------------------------------------------------------------+

if ('caddie' == $_GET['cat'])
{
  $template->assign_block_vars('in_caddie', array());
}
else
{
  $template->assign_block_vars('not_in_caddie', array());
}

// +-----------------------------------------------------------------------+
// |                           global mode form                            |
// +-----------------------------------------------------------------------+

// Virtualy associate a picture to a category
$blockname = 'associate_option';

$template->assign_block_vars(
  $blockname,
  array('SELECTED' => '',
        'VALUE'=> 0,
        'OPTION' => '------------'
    ));

$query = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
;';
display_select_cat_wrapper($query, array(), $blockname, true);

// Dissociate from a category : categories listed for dissociation can
// only represent virtual links. Links to physical categories can't be
// broken
$blockname = 'dissociate_option';

$template->assign_block_vars(
  $blockname,
  array('SELECTED' => '',
        'VALUE'=> 0,
        'OPTION' => '------------'
    ));

if (count($page['cat_elements_id']) > 0)
{
  $query = '
SELECT DISTINCT(category_id) AS id, c.name, uppercats, global_rank
  FROM '.IMAGE_CATEGORY_TABLE.' AS ic,
       '.CATEGORIES_TABLE.' AS c,
       '.IMAGES_TABLE.' AS i
  WHERE ic.image_id IN ('.implode(',', $page['cat_elements_id']).')
    AND ic.category_id = c.id
    AND ic.image_id = i.id
    AND ic.is_storage = \'false\'
;';
  display_select_cat_wrapper($query, array(), $blockname, true);
}

// add tags
$template->assign_vars(
  array(
    'ADD_TAG_SELECTION' => get_html_tag_selection(get_all_tags(), 'add_tags'),
    )
  );

if (count($page['cat_elements_id']) > 0)
{
  // remove tags
  $query = '
  SELECT tag_id, name, url_name, count(*) counter
    FROM '.IMAGE_TAG_TABLE.'
      INNER JOIN '.TAGS_TABLE.' ON tag_id = id
    WHERE image_id IN ('.implode(',', $page['cat_elements_id']).')
    GROUP BY tag_id
    ORDER BY name ASC
  ;';
  $result = pwg_query($query);

  $tags = array();
  while($row = mysql_fetch_array($result))
  {
    array_push($tags, $row);
  }

  $template->assign_vars(
    array(
      'DEL_TAG_SELECTION' => get_html_tag_selection($tags, 'del_tags'),
      )
    );
}
// creation date
$day =
empty($_POST['date_creation_day']) ? date('j') : $_POST['date_creation_day'];
get_day_list('date_creation_day', $day);

if (!empty($_POST['date_creation_month']))
{
  $month = $_POST['date_creation_month'];
}
else
{
  $month = date('n');
}
get_month_list('date_creation_month', $month);

if (!empty($_POST['date_creation_year']))
{
  $year = $_POST['date_creation_year'];
}
else
{
  $year = date('Y');
}
$template->assign_vars(array('DATE_CREATION_YEAR_VALUE'=>$year));

// +-----------------------------------------------------------------------+
// |                        global mode thumbnails                         |
// +-----------------------------------------------------------------------+

$page['cols'] = !empty($_GET['cols']) ? intval($_GET['cols']) : 5;

// how many items to display on this page
if (!empty($_GET['display']))
{
  if ('all' == $_GET['display'])
  {
    $page['nb_images'] = count($page['cat_elements_id']);
  }
  else
  {
    $page['nb_images'] = intval($_GET['display']);
  }
}
else
{
  $page['nb_images'] = 20;
}

if (count($page['cat_elements_id']) > 0)
{
  $nav_bar = create_navigation_bar(
    $base_url.get_query_string_diff(array('start')),
    count($page['cat_elements_id']),
    $page['start'],
    $page['nb_images']
    );
  $template->assign_vars(array('NAV_BAR' => $nav_bar));

  $query = '
SELECT id,path,tn_ext
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', $page['cat_elements_id']).')
  '.$conf['order_by'].'
  LIMIT '.$page['start'].', '.$page['nb_images'].'
;';
  //echo '<pre>'.$query.'</pre>';
  $result = pwg_query($query);

  // template thumbnail initialization
  if (mysql_num_rows($result) > 0)
  {
    $template->assign_block_vars('thumbnails', array());
    // first line
    $template->assign_block_vars('thumbnails.line', array());
    // current row displayed
    $row_number = 0;
  }

  while ($row = mysql_fetch_array($result))
  {
    $src = get_thumbnail_src($row['path'], @$row['tn_ext']);

    $template->assign_block_vars(
      'thumbnails.line.thumbnail',
      array(
        'ID' => $row['id'],
        'SRC' => $src,
        'ALT' => 'TODO',
        'TITLE' => 'TODO'
        )
      );

    // create a new line ?
    if (++$row_number == $page['cols'])
    {
    $template->assign_block_vars('thumbnails.line', array());
    $row_number = 0;
    }
  }
}

//----------------------------------------------------------- sending html code
$template->assign_var_from_handle('ADMIN_CONTENT', 'element_set_global');
?>
