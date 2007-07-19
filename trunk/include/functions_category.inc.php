<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
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

/**
 * Provides functions to handle categories.
 *
 *
 */

/**
 * Is the category accessible to the connected user ?
 *
 * Note : if the user is not authorized to see this category, page creation
 * ends (exit command in this function)
 *
 * @param int category id to verify
 * @return void
 */
function check_restrictions($category_id)
{
  global $user;

  // $filter['visible_categories'] and $filter['visible_images']
  // are not used because it's not necessary (filter <> restriction)
  if (in_array($category_id, explode(',', $user['forbidden_categories'])))
  {
    access_denied();
  }
}

function get_categories_menu()
{
  global $page, $user, $filter;

  $query = '
SELECT ';
  // From CATEGORIES_TABLE
  $query.= '
  id, name, permalink, nb_images, global_rank,';
  // From USER_CACHE_CATEGORIES_TABLE
  $query.= '
  date_last, max_date_last, count_images, count_categories';

  // $user['forbidden_categories'] including with USER_CACHE_CATEGORIES_TABLE
  $query.= '
FROM '.CATEGORIES_TABLE.' INNER JOIN '.USER_CACHE_CATEGORIES_TABLE.'
  ON id = cat_id and user_id = '.$user['id'];

  // Always expand when filter is activated
  if (!$user['expand'] and !$filter['enabled'])
  {
    $where = '
(id_uppercat is NULL';
    if (isset($page['category']))
    {
      $where .= ' OR id_uppercat IN ('.$page['category']['uppercats'].')';
    }
    $where .= ')';
  }
  else
  {
    $where = '
  '.get_sql_condition_FandF
    (
      array
        (
          'visible_categories' => 'id',
        ),
      null,
      true
    );
  }

  $where = trigger_event('get_categories_menu_sql_where',
    $where, $user['expand'], $filter['enabled'] );

  $query.= '
WHERE '.$where.'
;';

  $result = pwg_query($query);
  $cats = array();
  while ($row = mysql_fetch_assoc($result))
  {
    array_push($cats, $row);
  }
  usort($cats, 'global_rank_compare');

  // Update filtered data
  if (function_exists('update_cats_with_filtered_data'))
  {
    update_cats_with_filtered_data($cats);
  }

  return get_html_menu_category($cats, @$page['category'] );
}


/**
 * Retrieve informations about a category in the database
 *
 * Returns an array with following keys :
 *
 *  - comment
 *  - dir : directory, might be empty for virtual categories
 *  - name : an array with indexes from 0 (lowest cat name) to n (most
 *           uppercat name findable)
 *  - nb_images
 *  - id_uppercat
 *  - site_id
 *  -
 *
 * @param int category id
 * @return array
 */
function get_cat_info( $id )
{
  $query = '
SELECT *
  FROM '.CATEGORIES_TABLE.'
  WHERE id = '.$id.'
;';
  $cat = mysql_fetch_assoc(pwg_query($query));
  if (empty($cat))
    return null;

  foreach ($cat as $k => $v)
  {
    // If the field is true or false, the variable is transformed into a
    // boolean value.
    if ($cat[$k] == 'true' or $cat[$k] == 'false')
    {
      $cat[$k] = get_boolean( $cat[$k] );
    }
  }
  global $conf;
  if ( !( $conf['allow_html_descriptions'] and
          preg_match('/<(div|br|img|script).*>/i', $cat['comment']) ) )
  {
    $cat['comment'] = nl2br(@$cat['comment']);
  }

  $upper_ids = explode(',', $cat['uppercats']);
  if ( count($upper_ids)==1 )
  {// no need to make a query for level 1
    $cat['upper_names'] = array(
        array(
          'id' => $cat['id'],
          'name' => $cat['name'],
          'permalink' => $cat['permalink'],
          )
      );
  }
  else
  {
    $names = array();
    $query = '
  SELECT id, name, permalink
    FROM '.CATEGORIES_TABLE.'
    WHERE id IN ('.$cat['uppercats'].')
  ;';
    $names = hash_from_query($query, 'id');

    // category names must be in the same order than uppercats list
    $cat['upper_names'] = array();
    foreach ($upper_ids as $cat_id)
    {
      array_push( $cat['upper_names'], $names[$cat_id]);
    }
  }
  return $cat;
}

// get_complete_dir returns the concatenation of get_site_url and
// get_local_dir
// Example : "pets > rex > 1_year_old" is on the the same site as the
// PhpWebGallery files and this category has 22 for identifier
// get_complete_dir(22) returns "./galleries/pets/rex/1_year_old/"
function get_complete_dir( $category_id )
{
  return get_site_url($category_id).get_local_dir($category_id);
}

// get_local_dir returns an array with complete path without the site url
// Example : "pets > rex > 1_year_old" is on the the same site as the
// PhpWebGallery files and this category has 22 for identifier
// get_local_dir(22) returns "pets/rex/1_year_old/"
function get_local_dir( $category_id )
{
  global $page;

  $uppercats = '';
  $local_dir = '';

  if ( isset( $page['plain_structure'][$category_id]['uppercats'] ) )
  {
    $uppercats = $page['plain_structure'][$category_id]['uppercats'];
  }
  else
  {
    $query = 'SELECT uppercats';
    $query.= ' FROM '.CATEGORIES_TABLE.' WHERE id = '.$category_id;
    $query.= ';';
    $row = mysql_fetch_array( pwg_query( $query ) );
    $uppercats = $row['uppercats'];
  }

  $upper_array = explode( ',', $uppercats );

  $database_dirs = array();
  $query = 'SELECT id,dir';
  $query.= ' FROM '.CATEGORIES_TABLE.' WHERE id IN ('.$uppercats.')';
  $query.= ';';
  $result = pwg_query( $query );
  while( $row = mysql_fetch_array( $result ) )
  {
    $database_dirs[$row['id']] = $row['dir'];
  }
  foreach ($upper_array as $id)
  {
    $local_dir.= $database_dirs[$id].'/';
  }

  return $local_dir;
}

// retrieving the site url : "http://domain.com/gallery/" or
// simply "./galleries/"
function get_site_url($category_id)
{
  global $page;

  $query = '
SELECT galleries_url
  FROM '.SITES_TABLE.' AS s,'.CATEGORIES_TABLE.' AS c
  WHERE s.id = c.site_id
    AND c.id = '.$category_id.'
;';
  $row = mysql_fetch_array(pwg_query($query));
  return $row['galleries_url'];
}

// returns an array of image orders available for users/visitors
function get_category_preferred_image_orders()
{
  global $conf;
  return array(
    array(l10n('default_sort'), '', true),
    array(l10n('Average rate'), 'average_rate DESC', $conf['rate']),
    array(l10n('most_visited_cat'), 'hit DESC', true),
    array(l10n('Creation date'), 'date_creation DESC', true),
    array(l10n('Post date'), 'date_available DESC', true),
    array(l10n('File name'), 'file ASC', true)
  );
}

function display_select_categories($categories,
                                   $selecteds,
                                   $blockname,
                                   $fullname = true)
{
  global $template;

  foreach ($categories as $category)
  {
    $selected = '';
    if (in_array($category['id'], $selecteds))
    {
      $selected = ' selected="selected"';
    }

    if ($fullname)
    {
      $option = get_cat_display_name_cache($category['uppercats'],
                                           null,
                                           false);
    }
    else
    {
      $option = str_repeat('&nbsp;',
                           (3 * substr_count($category['global_rank'], '.')));
      $option.= '- '.$category['name'];
    }

    $template->assign_block_vars(
      $blockname,
      array('SELECTED'=>$selected,
            'VALUE'=>$category['id'],
            'OPTION'=>$option
        ));
  }
}

function display_select_cat_wrapper($query, $selecteds, $blockname,
                                    $fullname = true)
{
  $result = pwg_query($query);
  $categories = array();
  if (!empty($result))
  {
    while ($row = mysql_fetch_assoc($result))
    {
      array_push($categories, $row);
    }
  }
  usort($categories, 'global_rank_compare');
  display_select_categories($categories, $selecteds, $blockname, $fullname);
}

/**
 * returns all subcategory identifiers of given category ids
 *
 * @param array ids
 * @return array
 */
function get_subcat_ids($ids)
{
  $query = '
SELECT DISTINCT(id)
  FROM '.CATEGORIES_TABLE.'
  WHERE ';
  foreach ($ids as $num => $category_id)
  {
    is_numeric($category_id)
      or trigger_error(
        'get_subcat_ids expecting numeric, not '.gettype($category_id),
        E_USER_WARNING
      );
    if ($num > 0)
    {
      $query.= '
    OR ';
    }
    $query.= 'uppercats REGEXP \'(^|,)'.$category_id.'(,|$)\'';
  }
  $query.= '
;';
  $result = pwg_query($query);

  $subcats = array();
  while ($row = mysql_fetch_array($result))
  {
    array_push($subcats, $row['id']);
  }
  return $subcats;
}

/** finds a matching category id from a potential list of permalinks
 * @param array permalinks example: holiday holiday/france holiday/france/paris
 * @param int idx - output of the index in $permalinks that matches
 * return category id or null if no match
 */
function get_cat_id_from_permalinks( $permalinks, &$idx )
{
  $in = '';
  foreach($permalinks as $permalink)
  {
    if ( !empty($in) ) $in.=', ';
    $in .= '"'.$permalink.'"';
  }
  $query ='
SELECT c.id, op.permalink, 1 AS is_old
  FROM '.OLD_PERMALINKS_TABLE.' op INNER JOIN '.CATEGORIES_TABLE.' c
    ON op.cat_id=c.id
  WHERE op.permalink IN ('.$in.')
UNION
SELECT id, permalink, 0 AS is_old
  FROM '.CATEGORIES_TABLE.'
  WHERE permalink IN ('.$in.')
;';
  $perma_hash = hash_from_query($query, 'permalink');

  if ( empty($perma_hash) )
    return null;
  for ($i=count($permalinks)-1; $i>=0; $i--)
  {
    if ( isset( $perma_hash[ $permalinks[$i] ] ) )
    {
      $idx = $i;
      $cat_id = $perma_hash[ $permalinks[$i] ]['id'];
      if ($perma_hash[ $permalinks[$i] ]['is_old'])
      {
        $query='
UPDATE '.OLD_PERMALINKS_TABLE.' SET last_hit=NOW(), hit=hit+1
  WHERE permalink="'.$permalinks[$i].'" AND cat_id='.$cat_id.'
  LIMIT 1';
        pwg_query($query);
      }
      return $cat_id;
    }
  }
  return null;
}

function global_rank_compare($a, $b)
{
  return strnatcasecmp($a['global_rank'], $b['global_rank']);
}

function rank_compare($a, $b)
{
  if ($a['rank'] == $b['rank'])
  {
    return 0;
  }

  return ($a['rank'] < $b['rank']) ? -1 : 1;
}

/**
 * returns display text for information images of category
 *
 * @param array categories
 * @return string
 */
function get_display_images_count($cat_nb_images, $cat_count_images, $cat_count_categories, $short_message = true, $Separator = '\n')
{
  $display_text = '';

  if ($cat_count_images > 0)
  {
    if ($cat_nb_images > 0 and $cat_nb_images < $cat_count_images)
    {
      $display_text.= get_display_images_count($cat_nb_images, $cat_nb_images, 0, $short_message, $Separator).$Separator;
      $cat_count_images-= $cat_nb_images;
      $cat_nb_images = 0;
    }
    
    //at least one image direct or indirect
    $display_text.= l10n_dec('image_available', 'images_available', $cat_count_images);

    if ($cat_count_categories == 0 or $cat_nb_images == $cat_count_images)
    {
      //no descendant categories or descendants do not contain images
      if (! $short_message)
      {
        $display_text.= ' '.l10n('images_available_cpl');
      }
    }
    else
    {
      $display_text.= ' '.l10n_dec('images_available_cat', 'images_available_cats', $cat_count_categories);
    }
  }

  return $display_text;
}

?>
