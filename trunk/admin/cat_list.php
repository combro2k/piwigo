<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2008 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | file          : $Id$
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
// |                               functions                               |
// +-----------------------------------------------------------------------+

/**
 * save the rank depending on given categories order
 *
 * The list of ordered categories id is supposed to be in the same parent
 * category
 *
 * @param array categories
 * @return void
 */
function save_categories_order($categories)
{
  $current_rank = 0;
  $datas = array();
  foreach ($categories as $id)
  {
    array_push($datas, array('id' => $id, 'rank' => ++$current_rank));
  }
  $fields = array('primary' => array('id'), 'update' => array('rank'));
  mass_updates(CATEGORIES_TABLE, $fields, $datas);

  update_global_rank(@$_GET['parent_id']);
}

// +-----------------------------------------------------------------------+
// |                            initialization                             |
// +-----------------------------------------------------------------------+

$categories = array();

$base_url = PHPWG_ROOT_PATH.'admin.php?page=cat_list';
$navigation = '<a class="" href="'.$base_url.'">';
$navigation.= l10n('home');
$navigation.= '</a>';

// +-----------------------------------------------------------------------+
// |                    virtual categories management                      |
// +-----------------------------------------------------------------------+
// request to delete a virtual category / not for an adviser
if (isset($_GET['delete']) and is_numeric($_GET['delete']) and !is_adviser())
{
  delete_categories(array($_GET['delete']));
  array_push($page['infos'], l10n('cat_virtual_deleted'));
  ordering();
  update_global_rank();
}
// request to add a virtual category
else if (isset($_POST['submitAdd']))
{
  $output_create = create_virtual_category(
    $_POST['virtual_name'],
    @$_GET['parent_id']
    );

  if (isset($output_create['error']))
  {
    array_push($page['errors'], $output_create['error']);
  }
  else
  {
    array_push($page['infos'], $output_create['info']);
  }
}
// save manual category ordering
else if (isset($_POST['submitOrder']))
{
  asort($_POST['catOrd'], SORT_NUMERIC);
  save_categories_order(array_keys($_POST['catOrd']));

  array_push(
    $page['infos'],
    l10n('Categories manual order was saved')
    );
}
// sort categories alpha-numerically
else if (isset($_POST['submitOrderAlphaNum']))
{
  $query = '
SELECT id, name
  FROM '.CATEGORIES_TABLE.'
  WHERE id_uppercat '.
    (!isset($_GET['parent_id']) ? 'IS NULL' : '= '.$_GET['parent_id']).'
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_assoc($result))
  {
    $categories[ $row['id'] ] = strtolower($row['name']);
  }

  asort($categories, SORT_REGULAR);
  save_categories_order(array_keys($categories));

  array_push(
    $page['infos'],
    l10n('Categories ordered alphanumerically')
    );
}

// +-----------------------------------------------------------------------+
// |                            Navigation path                            |
// +-----------------------------------------------------------------------+

if (isset($_GET['parent_id']))
{
  $navigation.= $conf['level_separator'];

  $navigation.= get_cat_display_name_from_id(
    $_GET['parent_id'],
    $base_url.'&amp;parent_id=',
    false
    );
}
// +-----------------------------------------------------------------------+
// |                       template initialization                         |
// +-----------------------------------------------------------------------+
$template->set_filename('categories', 'admin/cat_list.tpl');

$form_action = PHPWG_ROOT_PATH.'admin.php?page=cat_list';
if (isset($_GET['parent_id']))
{
  $form_action.= '&amp;parent_id='.$_GET['parent_id'];
}

$template->assign(array(
  'CATEGORIES_NAV'=>$navigation,
  'F_ACTION'=>$form_action,
 ));

// +-----------------------------------------------------------------------+
// |                          Categories display                           |
// +-----------------------------------------------------------------------+

$categories = array();

$query = '
SELECT id, name, permalink, dir, rank, nb_images, status
  FROM '.CATEGORIES_TABLE;
if (!isset($_GET['parent_id']))
{
  $query.= '
  WHERE id_uppercat IS NULL';
}
else
{
  $query.= '
  WHERE id_uppercat = '.$_GET['parent_id'];
}
$query.= '
  ORDER BY rank ASC
;';
$result = pwg_query($query);
while ($row = mysql_fetch_array($result))
{
  $categories[$row['id']] = $row;
  // by default, let's consider there is no sub-categories. This will be
  // calculated after.
  $categories[$row['id']]['nb_subcats'] = 0;
}

if (count($categories) > 0)
{
  $query = '
SELECT id_uppercat, COUNT(*) AS nb_subcats
  FROM '. CATEGORIES_TABLE.'
  WHERE id_uppercat IN ('.implode(',', array_keys($categories)).')
  GROUP BY id_uppercat
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    $categories[$row['id_uppercat']]['nb_subcats'] = $row['nb_subcats'];
  }
}

$template->assign('categories', array());
foreach ($categories as $category)
{
  $base_url = PHPWG_ROOT_PATH.'admin.php?page=';
  $cat_list_url = $base_url.'cat_list';

  $self_url = $cat_list_url;
  if (isset($_GET['parent_id']))
  {
    $self_url.= '&amp;parent_id='.$_GET['parent_id'];
  }

  $tpl_cat =
    array(
      'NAME'       => $category['name'],
      'ID'         => $category['id'],
      'RANK'       => $category['rank']*10,

      'U_JUMPTO'   => make_index_url(
        array(
          'category' => $category
          )
        ),

      'U_CHILDREN' => $cat_list_url.'&amp;parent_id='.$category['id'],
      'U_EDIT'     => $base_url.'cat_modify&amp;cat_id='.$category['id'],
      
      'IS_VIRTUAL' => empty($category['dir'])
    );

  if (empty($category['dir']))
  {
    $tpl_cat['U_DELETE'] = $self_url.'&amp;delete='.$category['id'];
  }

  if ($category['nb_images'] > 0)
  {
    $tpl_cat['U_MANAGE_ELEMENTS']=
      $base_url.'element_set&amp;cat='.$category['id'];
  }

  if ('private' == $category['status'])
  {
    $tpl_cat['U_MANAGE_PERMISSIONS']=
      $base_url.'cat_perm&amp;cat='.$category['id'];
  }
  $template->append('categories', $tpl_cat);
}
// +-----------------------------------------------------------------------+
// |                          sending html code                            |
// +-----------------------------------------------------------------------+
$template->assign_var_from_handle('ADMIN_CONTENT', 'categories');
?>
