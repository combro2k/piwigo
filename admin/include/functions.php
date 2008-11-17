<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008      Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 PhpWebGallery Team    http://phpwebgallery.net |
// | Copyright(C) 2002-2003 Pierrick LE GALL   http://le-gall.net/pierrick |
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

include(PHPWG_ROOT_PATH.'admin/include/functions_metadata.php');


// The function delete_site deletes a site and call the function
// delete_categories for each primary category of the site
function delete_site( $id )
{
  // destruction of the categories of the site
  $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
  WHERE site_id = '.$id.'
;';
  $result = pwg_query($query);
  $category_ids = array();
  while ($row = mysql_fetch_array($result))
  {
    array_push($category_ids, $row['id']);
  }
  delete_categories($category_ids);

  // destruction of the site
  $query = '
DELETE FROM '.SITES_TABLE.'
  WHERE id = '.$id.'
;';
  pwg_query($query);
}


// The function delete_categories deletes the categories identified by the
// (numeric) key of the array $ids. It also deletes (in the database) :
//    - all the elements of the category (delete_elements, see further)
//    - all the links between elements and this category
//    - all the restrictions linked to the category
// The function works recursively.
function delete_categories($ids)
{
  if (count($ids) == 0)
  {
    return;
  }

  // add sub-category ids to the given ids : if a category is deleted, all
  // sub-categories must be so
  $ids = get_subcat_ids($ids);

  // destruction of all the related elements
  $query = '
SELECT id
  FROM '.IMAGES_TABLE.'
  WHERE storage_category_id IN (
'.wordwrap(implode(', ', $ids), 80, "\n").')
;';
  $result = pwg_query($query);
  $element_ids = array();
  while ($row = mysql_fetch_array($result))
  {
    array_push($element_ids, $row['id']);
  }
  delete_elements($element_ids);

  // destruction of the links between images and this category
  $query = '
DELETE FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE category_id IN (
'.wordwrap(implode(', ', $ids), 80, "\n").')
;';
  pwg_query($query);

  // destruction of the access linked to the category
  $query = '
DELETE FROM '.USER_ACCESS_TABLE.'
  WHERE cat_id IN (
'.wordwrap(implode(', ', $ids), 80, "\n").')
;';
  pwg_query($query);

  $query = '
DELETE FROM '.GROUP_ACCESS_TABLE.'
  WHERE cat_id IN (
'.wordwrap(implode(', ', $ids), 80, "\n").')
;';
  pwg_query($query);

  // destruction of the category
  $query = '
DELETE FROM '.CATEGORIES_TABLE.'
  WHERE id IN (
'.wordwrap(implode(', ', $ids), 80, "\n").')
;';
  pwg_query($query);

  $query='
DELETE FROM '.OLD_PERMALINKS_TABLE.'
  WHERE cat_id IN ('.implode(',',$ids).')';
  pwg_query($query);

  $query='
DELETE FROM '.USER_CACHE_CATEGORIES_TABLE.'
  WHERE cat_id IN ('.implode(',',$ids).')';
  pwg_query($query);

  trigger_action('delete_categories', $ids);
}

// The function delete_elements deletes the elements identified by the
// (numeric) values of the array $ids. It also deletes (in the database) :
//    - all the comments related to elements
//    - all the links between categories and elements
//    - all the favorites associated to elements
function delete_elements($ids, $physical_deletion=false)
{
  if (count($ids) == 0)
  {
    return;
  }
  trigger_action('begin_delete_elements', $ids);

  if ($physical_deletion)
  {
    include_once(PHPWG_ROOT_PATH.'include/functions_picture.inc.php');
    
    // we can currently delete physically only photo with no
    // storage_category_id (added via pLoader)
    //
    // we assume that the element is a photo, with no representative
    $query = '
SELECT
    id,
    path,
    tn_ext,
    has_high
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', $ids).')
    AND storage_category_id IS NULL
;';
    $result = pwg_query($query);
    while ($row = mysql_fetch_assoc($result))
    {
      $file_path = $row['path'];
      $thumbnail_path = get_thumbnail_path($row);
      $high_path = null;
      if (isset($row['has_high']) and get_boolean($row['has_high']))
      {
        $high_path = get_high_path($row);
      }

      foreach (array($file_path, $thumbnail_path, $high_path) as $path)
      {
        if (isset($path) and !unlink($path))
        {
          die('"'.$path.'" cannot be removed');
        }
      }
    }
  }

  // destruction of the comments on the image
  $query = '
DELETE FROM '.COMMENTS_TABLE.'
  WHERE image_id IN (
'.wordwrap(implode(', ', $ids), 80, "\n").')
;';
  pwg_query($query);

  // destruction of the links between images and this category
  $query = '
DELETE FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE image_id IN (
'.wordwrap(implode(', ', $ids), 80, "\n").')
;';
  pwg_query($query);

  // destruction of the links between images and tags
  $query = '
DELETE FROM '.IMAGE_TAG_TABLE.'
  WHERE image_id IN (
'.wordwrap(implode(', ', $ids), 80, "\n").')
;';
  pwg_query($query);

  // destruction of the favorites associated with the picture
  $query = '
DELETE FROM '.FAVORITES_TABLE.'
  WHERE image_id IN (
'.wordwrap(implode(', ', $ids), 80, "\n").')
;';
  pwg_query($query);

  // destruction of the rates associated to this element
  $query = '
DELETE FROM '.RATE_TABLE.'
  WHERE element_id IN (
'.wordwrap(implode(', ', $ids), 80, "\n").')
;';
  pwg_query($query);

  // destruction of the rates associated to this element
  $query = '
DELETE FROM '.CADDIE_TABLE.'
  WHERE element_id IN (
'.wordwrap(implode(', ', $ids), 80, "\n").')
;';
  pwg_query($query);

  // destruction of the image
  $query = '
DELETE FROM '.IMAGES_TABLE.'
  WHERE id IN (
'.wordwrap(implode(', ', $ids), 80, "\n").')
;';
  pwg_query($query);

  trigger_action('delete_elements', $ids);
}

// The delete_user function delete a user identified by the $user_id
// It also deletes :
//     - all the access linked to this user
//     - all the links to any group
//     - all the favorites linked to this user
//     - calculated permissions linked to the user
//     - all datas about notifications for the user
function delete_user($user_id)
{
  global $conf;
  $tables = array(
    // destruction of the access linked to the user
    USER_ACCESS_TABLE,
    // destruction of data notification by mail for this user
    USER_MAIL_NOTIFICATION_TABLE,
    // destruction of data RSS notification for this user
    USER_FEED_TABLE,
    // deletion of calculated permissions linked to the user
    USER_CACHE_TABLE,
    // deletion of computed cache data linked to the user
    USER_CACHE_CATEGORIES_TABLE,
    // destruction of the group links for this user
    USER_GROUP_TABLE,
    // destruction of the favorites associated with the user
    FAVORITES_TABLE,
    // destruction of the caddie associated with the user
    CADDIE_TABLE,
    // deletion of piwigo specific informations
    USER_INFOS_TABLE,
    );

  foreach ($tables as $table)
  {
    $query = '
DELETE FROM '.$table.'
  WHERE user_id = '.$user_id.'
;';
    pwg_query($query);
  }

  // destruction of the user
  $query = '
DELETE FROM '.SESSIONS_TABLE.'
  WHERE data LIKE "pwg_uid|i:'.(int)$user_id.';%"
;';
  pwg_query($query);

  // destruction of the user
  $query = '
DELETE FROM '.USERS_TABLE.'
  WHERE '.$conf['user_fields']['id'].' = '.$user_id.'
;';
  pwg_query($query);

  trigger_action('delete_user', $user_id);
}

/**
 * Verifies that the representative picture really exists in the db and
 * picks up a random represantive if possible and based on config.
 *
 * @param mixed category id
 * @returns void
 */
function update_category($ids = 'all')
{
  global $conf;

  if ($ids=='all')
  {
    $where_cats = '1=1';
  }
  elseif ( !is_array($ids) )
  {
    $where_cats = '%s='.$ids;
  }
  else
  {
    if (count($ids) == 0)
    {
      return false;
    }
    $where_cats = '%s IN('.wordwrap(implode(', ', $ids), 120, "\n").')';
  }

  // find all categories where the setted representative is not possible :
  // the picture does not exist
  $query = '
SELECT DISTINCT c.id
  FROM '.CATEGORIES_TABLE.' AS c LEFT JOIN '.IMAGES_TABLE.' AS i
    ON c.representative_picture_id = i.id
  WHERE representative_picture_id IS NOT NULL
    AND '.sprintf($where_cats, 'c.id').'
    AND i.id IS NULL
;';
  $wrong_representant = array_from_query($query, 'id');

  if (count($wrong_representant) > 0)
  {
    $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET representative_picture_id = NULL
  WHERE id IN ('.wordwrap(implode(', ', $wrong_representant), 120, "\n").')
;';
    pwg_query($query);
  }

  if (!$conf['allow_random_representative'])
  {
    // If the random representant is not allowed, we need to find
    // categories with elements and with no representant. Those categories
    // must be added to the list of categories to set to a random
    // representant.
    $query = '
SELECT DISTINCT id
  FROM '.CATEGORIES_TABLE.' INNER JOIN '.IMAGE_CATEGORY_TABLE.'
    ON id = category_id
  WHERE representative_picture_id IS NULL
    AND '.sprintf($where_cats, 'category_id').'
;';
    $to_rand = array_from_query($query, 'id');
    if (count($to_rand) > 0)
    {
      set_random_representant($to_rand);
    }
  }
}

/**
 * returns an array containing sub-directories which can be a category,
 * recursive by default
 *
 * directories nammed "thumbnail", "pwg_high" or "pwg_representative" are
 * omitted
 *
 * @param string $basedir
 * @return array
 */
function get_fs_directories($path, $recursive = true)
{
  $dirs = array();

  if (is_dir($path))
  {
    if ($contents = opendir($path))
    {
      while (($node = readdir($contents)) !== false)
      {
        if (is_dir($path.'/'.$node)
            and $node != '.'
            and $node != '..'
            and $node != '.svn'
            and $node != 'thumbnail'
            and $node != 'pwg_high'
            and $node != 'pwg_representative')
        {
          array_push($dirs, $path.'/'.$node);
          if ($recursive)
          {
            $dirs = array_merge($dirs, get_fs_directories($path.'/'.$node));
          }
        }
      }
      closedir($contents);
    }
  }

  return $dirs;
}

/**
 * inserts multiple lines in a table
 *
 * @param string table_name
 * @param array dbfields
 * @param array inserts
 * @return void
 */
function mass_inserts($table_name, $dbfields, $datas)
{
  if (count($datas) != 0)
  {
    $first = true;

    $query = 'SHOW VARIABLES LIKE \'max_allowed_packet\'';
    list(, $packet_size) = mysql_fetch_row(pwg_query($query));
    $packet_size = $packet_size - 2000; // The last list of values MUST not exceed 2000 character*/
    $query = '';

    foreach ($datas as $insert)
    {
      if (strlen($query) >= $packet_size)
      {
        pwg_query($query);
        $first = true;
      }

      if ($first)
      {
        $query = '
INSERT INTO '.$table_name.'
  ('.implode(',', $dbfields).')
  VALUES';
        $first = false;
      }
      else
      {
        $query .= '
  , ';
      }

      $query .= '(';
      foreach ($dbfields as $field_id => $dbfield)
      {
        if ($field_id > 0)
        {
          $query .= ',';
        }

        if (!isset($insert[$dbfield]) or $insert[$dbfield] === '')
        {
          $query .= 'NULL';
        }
        else
        {
          $query .= "'".$insert[$dbfield]."'";
        }
      }
      $query .= ')';
    }
    pwg_query($query);
  }
}

define('MASS_UPDATES_SKIP_EMPTY', 1);
/**
 * updates multiple lines in a table
 *
 * @param string table_name
 * @param array dbfields
 * @param array datas
 * @param int flags - if MASS_UPDATES_SKIP_EMPTY - empty values do not overwrite existing ones
 * @return void
 */
function mass_updates($tablename, $dbfields, $datas, $flags=0)
{
  if (count($datas) == 0)
    return;
  // depending on the MySQL version, we use the multi table update or N update queries
  if (count($datas) < 10 or version_compare(mysql_get_server_info(), '4.0.4') < 0)
  { // MySQL is prior to version 4.0.4, multi table update feature is not available
    foreach ($datas as $data)
    {
      $query = '
UPDATE '.$tablename.'
  SET ';
      $is_first = true;
      foreach ($dbfields['update'] as $key)
      {
        $separator = $is_first ? '' : ",\n    ";

        if (isset($data[$key]) and $data[$key] != '')
        {
          $query.= $separator.$key.' = \''.$data[$key].'\'';
        }
        else
        {
          if ($flags & MASS_UPDATES_SKIP_EMPTY )
            continue; // next field
          $query.= "$separator$key = NULL";
        }
        $is_first = false;
      }
      if (!$is_first)
      {// only if one field at least updated
        $query.= '
  WHERE ';
        $is_first = true;
        foreach ($dbfields['primary'] as $key)
        {
          if (!$is_first)
          {
            $query.= ' AND ';
          }
          if ( isset($data[$key]) )
          {
            $query.= $key.' = \''.$data[$key].'\'';
          }
          else
          {
            $query.= $key.' IS NULL';
          }
          $is_first = false;
        }
        pwg_query($query);
      }
    } // foreach update
  } // if mysql_ver or count<X
  else
  {
    // creation of the temporary table
    $query = '
SHOW FULL COLUMNS FROM '.$tablename;
    $result = pwg_query($query);
    $columns = array();
    $all_fields = array_merge($dbfields['primary'], $dbfields['update']);
    while ($row = mysql_fetch_array($result))
    {
      if (in_array($row['Field'], $all_fields))
      {
        $column = $row['Field'];
        $column.= ' '.$row['Type'];

        $nullable = true;
        if (!isset($row['Null']) or $row['Null'] == '' or $row['Null']=='NO')
        {
          $column.= ' NOT NULL';
          $nullable = false;
        }
        if (isset($row['Default']))
        {
          $column.= " default '".$row['Default']."'";
        }
        elseif ($nullable)
        {
          $column.= " default NULL";
        }
        if (isset($row['Collation']) and $row['Collation'] != 'NULL')
        {
          $column.= " collate '".$row['Collation']."'";
        }
        array_push($columns, $column);
      }
    }

    $temporary_tablename = $tablename.'_'.micro_seconds();

    $query = '
CREATE TABLE '.$temporary_tablename.'
(
  '.implode(",\n  ", $columns).',
  UNIQUE KEY the_key ('.implode(',', $dbfields['primary']).')
)';

    pwg_query($query);
    mass_inserts($temporary_tablename, $all_fields, $datas);
    if ( $flags & MASS_UPDATES_SKIP_EMPTY )
      $func_set = create_function('$s', 'return "t1.$s = IFNULL(t2.$s, t1.$s)";');
    else
      $func_set = create_function('$s', 'return "t1.$s = t2.$s";');

    // update of images table by joining with temporary table
    $query = '
UPDATE '.$tablename.' AS t1, '.$temporary_tablename.' AS t2
  SET '.
      implode(
        "\n    , ",
        array_map($func_set,$dbfields['update'])
        ).'
  WHERE '.
      implode(
        "\n    AND ",
        array_map(
          create_function('$s', 'return "t1.$s = t2.$s";'),
          $dbfields['primary']
          )
        );
    pwg_query($query);
    $query = '
DROP TABLE '.$temporary_tablename;
    pwg_query($query);
  }
}

/**
 * order categories (update categories.rank and global_rank database fields)
 * so that rank field are consecutive integers starting at 1 for each child
 * @return void
 */
function update_global_rank()
{
  $query = '
SELECT id, if(id_uppercat is null,\'\',id_uppercat) AS id_uppercat, uppercats, rank, global_rank
  FROM '.CATEGORIES_TABLE.'
  ORDER BY id_uppercat,rank,name';

  $cat_map = array();

  $current_rank = 0;
  $current_uppercat = '';

  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    if ($row['id_uppercat'] != $current_uppercat)
    {
      $current_rank = 0;
      $current_uppercat = $row['id_uppercat'];
    }
    ++$current_rank;
    $cat =
      array(
        'rank' =>        $current_rank,
        'rank_changed' =>$current_rank!=$row['rank'],
        'global_rank' => $row['global_rank'],
        'uppercats' =>   $row['uppercats'],
        );
    $cat_map[ $row['id'] ] = $cat;
  }

  $datas = array();

  foreach( $cat_map as $id=>$cat )
  {
    $new_global_rank = preg_replace(
          '/(\d+)/e',
          "\$cat_map['$1']['rank']",
          str_replace(',', '.', $cat['uppercats'] )
          );
    if ( $cat['rank_changed']
      or $new_global_rank!=$cat['global_rank']
      )
    {
      $datas[] = array(
          'id' => $id,
          'rank' => $cat['rank'],
          'global_rank' => $new_global_rank,
        );
    }
  }

  mass_updates(
    CATEGORIES_TABLE,
    array(
      'primary' => array('id'),
      'update'  => array('rank', 'global_rank')
      ),
    $datas
    );
  return count($datas);
}

/**
 * change the visible property on a set of categories
 *
 * @param array categories
 * @param string value
 * @return void
 */
function set_cat_visible($categories, $value)
{
  if (!in_array($value, array('true', 'false')))
  {
    trigger_error("set_cat_visible invalid param $value", E_USER_WARNING);
    return false;
  }

  // unlocking a category => all its parent categories become unlocked
  if ($value == 'true')
  {
    $uppercats = get_uppercat_ids($categories);
    $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET visible = \'true\'
  WHERE id IN ('.implode(',', $uppercats).')';
    pwg_query($query);
  }
  // locking a category   => all its child categories become locked
  if ($value == 'false')
  {
    $subcats = get_subcat_ids($categories);
    $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET visible = \'false\'
  WHERE id IN ('.implode(',', $subcats).')';
    pwg_query($query);
  }
}

/**
 * change the status property on a set of categories : private or public
 *
 * @param array categories
 * @param string value
 * @return void
 */
function set_cat_status($categories, $value)
{
  if (!in_array($value, array('public', 'private')))
  {
    trigger_error("set_cat_status invalid param $value", E_USER_WARNING);
    return false;
  }

  // make public a category => all its parent categories become public
  if ($value == 'public')
  {
    $uppercats = get_uppercat_ids($categories);
    $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET status = \'public\'
  WHERE id IN ('.implode(',', $uppercats).')
;';
    pwg_query($query);
  }
  // make a category private => all its child categories become private
  if ($value == 'private')
  {
    $subcats = get_subcat_ids($categories);
    $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET status = \'private\'
  WHERE id IN ('.implode(',', $subcats).')';
    pwg_query($query);
  }
}

/**
 * returns all uppercats category ids of the given category ids
 *
 * @param array cat_ids
 * @return array
 */
function get_uppercat_ids($cat_ids)
{
  if (!is_array($cat_ids) or count($cat_ids) < 1)
  {
    return array();
  }

  $uppercats = array();

  $query = '
SELECT uppercats
  FROM '.CATEGORIES_TABLE.'
  WHERE id IN ('.implode(',', $cat_ids).')
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    $uppercats = array_merge($uppercats,
                             explode(',', $row['uppercats']));
  }
  $uppercats = array_unique($uppercats);

  return $uppercats;
}

/**
 * set a new random representant to the categories
 *
 * @param array categories
 */
function set_random_representant($categories)
{
  $datas = array();
  foreach ($categories as $category_id)
  {
    $query = '
SELECT image_id
  FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE category_id = '.$category_id.'
  ORDER BY RAND()
  LIMIT 0,1
;';
    list($representative) = mysql_fetch_array(pwg_query($query));

    array_push(
      $datas,
      array(
        'id' => $category_id,
        'representative_picture_id' => $representative,
        )
      );
  }

  mass_updates(
    CATEGORIES_TABLE,
    array(
      'primary' => array('id'),
      'update' => array('representative_picture_id')
      ),
    $datas
    );
}

/**
 * returns the fulldir for each given category id
 *
 * @param array cat_ids
 * @return array
 */
function get_fulldirs($cat_ids)
{
  if (count($cat_ids) == 0)
  {
    return array();
  }

  // caching directories of existing categories
  $query = '
SELECT id, dir
  FROM '.CATEGORIES_TABLE.'
  WHERE dir IS NOT NULL
;';
  $cat_dirs = simple_hash_from_query($query, 'id', 'dir');

  // caching galleries_url
  $query = '
SELECT id, galleries_url
  FROM '.SITES_TABLE.'
;';
  $galleries_url = simple_hash_from_query($query, 'id', 'galleries_url');

  // categories : id, site_id, uppercats
  $categories = array();

  $query = '
SELECT id, uppercats, site_id
  FROM '.CATEGORIES_TABLE.'
  WHERE dir IS NOT NULL
    AND id IN (
'.wordwrap(implode(', ', $cat_ids), 80, "\n").')
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    array_push($categories, $row);
  }

  // filling $cat_fulldirs
  $cat_fulldirs = array();
  foreach ($categories as $category)
  {
    $uppercats = str_replace(',', '/', $category['uppercats']);
    $cat_fulldirs[$category['id']] = $galleries_url[$category['site_id']];
    $cat_fulldirs[$category['id']].= preg_replace('/(\d+)/e',
                                                  "\$cat_dirs['$1']",
                                                  $uppercats);
  }

  return $cat_fulldirs;
}

/**
 * returns an array with all file system files according to
 * $conf['file_ext']
 *
 * @param string $path
 * @param bool recursive
 * @return array
 */
function get_fs($path, $recursive = true)
{
  global $conf;

  // because isset is faster than in_array...
  if (!isset($conf['flip_picture_ext']))
  {
    $conf['flip_picture_ext'] = array_flip($conf['picture_ext']);
  }
  if (!isset($conf['flip_file_ext']))
  {
    $conf['flip_file_ext'] = array_flip($conf['file_ext']);
  }

  $fs['elements'] = array();
  $fs['thumbnails'] = array();
  $fs['representatives'] = array();
  $subdirs = array();

  if (is_dir($path))
  {
    if ($contents = opendir($path))
    {
      while (($node = readdir($contents)) !== false)
      {
        if (is_file($path.'/'.$node))
        {
          $extension = get_extension($node);

//          if (in_array($extension, $conf['picture_ext']))
          if (isset($conf['flip_picture_ext'][$extension]))
          {
            if (basename($path) == 'thumbnail')
            {
              array_push($fs['thumbnails'], $path.'/'.$node);
            }
            else if (basename($path) == 'pwg_representative')
            {
              array_push($fs['representatives'], $path.'/'.$node);
            }
            else
            {
              array_push($fs['elements'], $path.'/'.$node);
            }
          }
//          else if (in_array($extension, $conf['file_ext']))
          else if (isset($conf['flip_file_ext'][$extension]))
          {
            array_push($fs['elements'], $path.'/'.$node);
          }
        }
        else if (is_dir($path.'/'.$node)
                 and $node != '.'
                 and $node != '..'
                 and $node != 'pwg_high'
                 and $recursive)
        {
          array_push($subdirs, $node);
        }
      }
    }
    closedir($contents);

    foreach ($subdirs as $subdir)
    {
      $tmp_fs = get_fs($path.'/'.$subdir);

      $fs['elements']        = array_merge($fs['elements'],
                                           $tmp_fs['elements']);

      $fs['thumbnails']      = array_merge($fs['thumbnails'],
                                           $tmp_fs['thumbnails']);

      $fs['representatives'] = array_merge($fs['representatives'],
                                           $tmp_fs['representatives']);
    }
  }
  return $fs;
}

/**
 * stupidly returns the current microsecond since Unix epoch
 */
function micro_seconds()
{
  $t1 = explode(' ', microtime());
  $t2 = explode('.', $t1[0]);
  $t2 = $t1[1].substr($t2[1], 0, 6);
  return $t2;
}

/**
 * synchronize base users list and related users list
 *
 * compares and synchronizes base users table (USERS_TABLE) with its child
 * tables (USER_INFOS_TABLE, USER_ACCESS, USER_CACHE, USER_GROUP) : each
 * base user must be present in child tables, users in child tables not
 * present in base table must be deleted.
 *
 * @return void
 */
function sync_users()
{
  global $conf;

  $query = '
SELECT '.$conf['user_fields']['id'].' AS id
  FROM '.USERS_TABLE.'
;';
  $base_users = array_from_query($query, 'id');

  $query = '
SELECT user_id
  FROM '.USER_INFOS_TABLE.'
;';
  $infos_users = array_from_query($query, 'user_id');

  // users present in $base_users and not in $infos_users must be added
  $to_create = array_diff($base_users, $infos_users);

  if (count($to_create) > 0)
  {
    create_user_infos($to_create);
  }

  // users present in user related tables must be present in the base user
  // table
  $tables = array(
    USER_MAIL_NOTIFICATION_TABLE,
    USER_FEED_TABLE,
    USER_INFOS_TABLE,
    USER_ACCESS_TABLE,
    USER_CACHE_TABLE,
    USER_CACHE_CATEGORIES_TABLE,
    USER_GROUP_TABLE
    );

  foreach ($tables as $table)
  {
    $query = '
SELECT DISTINCT user_id
  FROM '.$table.'
;';
    $to_delete = array_diff(
      array_from_query($query, 'user_id'),
      $base_users
      );

    if (count($to_delete) > 0)
    {
      $query = '
DELETE
  FROM '.$table.'
  WHERE user_id in ('.implode(',', $to_delete).')
;';
      pwg_query($query);
    }
  }
}

/**
 * updates categories.uppercats field based on categories.id +
 * categories.id_uppercat
 *
 * @return void
 */
function update_uppercats()
{
  $query = '
SELECT id, id_uppercat, uppercats
  FROM '.CATEGORIES_TABLE.'
;';
  $cat_map = hash_from_query($query, 'id');

  $datas = array();
  foreach ($cat_map as $id => $cat)
  {
    $upper_list = array();

    $uppercat = $id;
    while ($uppercat)
    {
      array_push($upper_list, $uppercat);
      $uppercat = $cat_map[$uppercat]['id_uppercat'];
    }

    $new_uppercats = implode(',', array_reverse($upper_list));
    if ($new_uppercats != $cat['uppercats'])
    {
      array_push(
        $datas,
        array(
          'id' => $id,
          'uppercats' => $new_uppercats
          )
        );
    }
  }
  $fields = array('primary' => array('id'), 'update' => array('uppercats'));
  mass_updates(CATEGORIES_TABLE, $fields, $datas);
}

/**
 * update images.path field
 *
 * @return void
 */
function update_path()
{
  $query = '
SELECT DISTINCT(storage_category_id)
  FROM '.IMAGES_TABLE.'
  WHERE storage_category_id IS NOT NULL
;';
  $cat_ids = array_from_query($query, 'storage_category_id');
  $fulldirs = get_fulldirs($cat_ids);

  foreach ($cat_ids as $cat_id)
  {
    $query = '
UPDATE '.IMAGES_TABLE.'
  SET path = CONCAT(\''.$fulldirs[$cat_id].'\',\'/\',file)
  WHERE storage_category_id = '.$cat_id.'
;';
    pwg_query($query);
  }
}

/**
 * update images.average_rate field
 * param int $element_id optional, otherwise applies to all
 * @return void
 */
function update_average_rate( $element_id=-1 )
{
  $query = '
SELECT element_id,
       ROUND(AVG(rate),2) AS average_rate
  FROM '.RATE_TABLE;
  if ( $element_id != -1 )
  {
    $query .= ' WHERE element_id=' . $element_id;
  }
  $query .= ' GROUP BY element_id;';

  $result = pwg_query($query);

  $datas = array();

  while ($row = mysql_fetch_array($result))
  {
    array_push(
      $datas,
      array(
        'id' => $row['element_id'],
        'average_rate' => $row['average_rate']
        )
      );
  }

  mass_updates(
    IMAGES_TABLE,
    array(
      'primary' => array('id'),
      'update' => array('average_rate')
      ),
    $datas
    );

  $query='
SELECT id FROM '.IMAGES_TABLE .'
  LEFT JOIN '.RATE_TABLE.' ON id=element_id
  WHERE element_id IS NULL AND average_rate IS NOT NULL';
  if ( $element_id != -1 )
  {
    $query .= ' AND id=' . $element_id;
  }
  $to_update = array_from_query( $query, 'id');

  if ( !empty($to_update) )
  {
    $query='
UPDATE '.IMAGES_TABLE .'
  SET average_rate=NULL
  WHERE id IN (' . implode(',',$to_update) . ')';
    pwg_query($query);
  }
}

/**
 * change the parent category of the given categories. The categories are
 * supposed virtual.
 *
 * @param array category identifiers
 * @param int parent category identifier
 * @return void
 */
function move_categories($category_ids, $new_parent = -1)
{
  global $page;

  if (count($category_ids) == 0)
  {
    return;
  }

  $new_parent = $new_parent < 1 ? 'NULL' : $new_parent;

  $categories = array();

  $query = '
SELECT id, id_uppercat, status, uppercats
  FROM '.CATEGORIES_TABLE.'
  WHERE id IN ('.implode(',', $category_ids).')
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    $categories[$row['id']] =
      array(
        'parent' => empty($row['id_uppercat']) ? 'NULL' : $row['id_uppercat'],
        'status' => $row['status'],
        'uppercats' => $row['uppercats']
        );
  }

  // is the movement possible? The movement is impossible if you try to move
  // a category in a sub-category or itself
  if ('NULL' != $new_parent)
  {
    $query = '
SELECT uppercats
  FROM '.CATEGORIES_TABLE.'
  WHERE id = '.$new_parent.'
;';
    list($new_parent_uppercats) = mysql_fetch_row(pwg_query($query));

    foreach ($categories as $category)
    {
      // technically, you can't move a category with uppercats 12,125,13,14
      // into a new parent category with uppercats 12,125,13,14,24
      if (preg_match('/^'.$category['uppercats'].'/', $new_parent_uppercats))
      {
        array_push(
          $page['errors'],
          l10n('You cannot move a category in its own sub category')
          );
        return;
      }
    }
  }

  $tables =
    array(
      USER_ACCESS_TABLE => 'user_id',
      GROUP_ACCESS_TABLE => 'group_id'
      );

  $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET id_uppercat = '.$new_parent.'
  WHERE id IN ('.implode(',', $category_ids).')
;';
  pwg_query($query);

  update_uppercats();
  update_global_rank();

  // status and related permissions management
  if ('NULL' == $new_parent)
  {
    $parent_status = 'public';
  }
  else
  {
    $query = '
SELECT status
  FROM '.CATEGORIES_TABLE.'
  WHERE id = '.$new_parent.'
;';
    list($parent_status) = mysql_fetch_row(pwg_query($query));
  }

  if ('private' == $parent_status)
  {
    foreach ($categories as $cat_id => $category)
    {
      switch ($category['status'])
      {
        case 'public' :
        {
          set_cat_status(array($cat_id), 'private');
          break;
        }
        case 'private' :
        {
          $subcats = get_subcat_ids(array($cat_id));

          foreach ($tables as $table => $field)
          {
            $query = '
SELECT '.$field.'
  FROM '.$table.'
  WHERE cat_id = '.$cat_id.'
;';
            $category_access = array_from_query($query, $field);

            $query = '
SELECT '.$field.'
  FROM '.$table.'
  WHERE cat_id = '.$new_parent.'
;';
            $parent_access = array_from_query($query, $field);

            $to_delete = array_diff($parent_access, $category_access);

            if (count($to_delete) > 0)
            {
              $query = '
DELETE FROM '.$table.'
  WHERE '.$field.' IN ('.implode(',', $to_delete).')
    AND cat_id IN ('.implode(',', $subcats).')
;';
              pwg_query($query);
            }
          }
          break;
        }
      }
    }
  }

  array_push(
    $page['infos'],
    l10n_dec(
      '%d category moved', '%d categories moved',
      count($categories)
      )
    );
}

/**
 * create a virtual category
 *
 * @param string category name
 * @param int parent category id
 * @return array with ('info' and 'id') or ('error') key
 */
function create_virtual_category($category_name, $parent_id=null)
{
  global $conf;

  // is the given category name only containing blank spaces ?
  if (preg_match('/^\s*$/', $category_name))
  {
    return array('error' => l10n('cat_error_name'));
  }

  $parent_id = !empty($parent_id) ? $parent_id : 'NULL';

  $query = '
SELECT MAX(rank)
  FROM '.CATEGORIES_TABLE.'
  WHERE id_uppercat '.(is_numeric($parent_id) ? '= '.$parent_id : 'IS NULL').'
;';
  list($current_rank) = mysql_fetch_array(pwg_query($query));

  $insert = array(
    'name' => $category_name,
    'rank' => ++$current_rank,
    'commentable' => boolean_to_string($conf['newcat_default_commentable']),
    'uploadable' => 'false',
    );

  if ($parent_id != 'NULL')
  {
    $query = '
SELECT id, uppercats, global_rank, visible, status
  FROM '.CATEGORIES_TABLE.'
  WHERE id = '.$parent_id.'
;';
    $parent = mysql_fetch_array(pwg_query($query));

    $insert{'id_uppercat'} = $parent{'id'};
    $insert{'global_rank'} = $parent{'global_rank'}.'.'.$insert{'rank'};

    // at creation, must a category be visible or not ? Warning : if the
    // parent category is invisible, the category is automatically create
    // invisible. (invisible = locked)
    if ('false' == $parent['visible'])
    {
      $insert{'visible'} = 'false';
    }
    else
    {
      $insert{'visible'} = boolean_to_string($conf['newcat_default_visible']);
    }

    // at creation, must a category be public or private ? Warning : if the
    // parent category is private, the category is automatically create
    // private.
    if ('private' == $parent['status'])
    {
      $insert{'status'} = 'private';
    }
    else
    {
      $insert{'status'} = $conf['newcat_default_status'];
    }
  }
  else
  {
    $insert{'visible'} = boolean_to_string($conf['newcat_default_visible']);
    $insert{'status'} = $conf['newcat_default_status'];
    $insert{'global_rank'} = $insert{'rank'};
  }

  // we have then to add the virtual category
  mass_inserts(
    CATEGORIES_TABLE,
    array(
      'site_id', 'name', 'id_uppercat', 'rank', 'commentable',
      'uploadable', 'visible', 'status', 'global_rank',
      ),
    array($insert)
    );

  $inserted_id = mysql_insert_id();

  $query = '
UPDATE
  '.CATEGORIES_TABLE.'
  SET uppercats = \''.
    (isset($parent) ? $parent{'uppercats'}.',' : '').
    $inserted_id.
    '\'
  WHERE id = '.$inserted_id.'
;';
  pwg_query($query);

  return array(
    'info' => l10n('cat_virtual_added'),
    'id'   => $inserted_id,
    );
}

/**
 * Set tags to an image. Warning: given tags are all tags associated to the
 * image, not additionnal tags.
 *
 * @param array tag ids
 * @param int image id
 * @return void
 */
function set_tags($tags, $image_id)
{
  $query = '
DELETE
  FROM '.IMAGE_TAG_TABLE.'
  WHERE image_id = '.$image_id.'
;';
  pwg_query($query);

  if (count($tags) > 0)
  {
    $inserts = array();
    foreach ($tags as $tag_id)
    {
      array_push(
        $inserts,
        array(
          'tag_id' => $tag_id,
          'image_id' => $image_id
          )
        );
    }
    mass_inserts(
      IMAGE_TAG_TABLE,
      array_keys($inserts[0]),
      $inserts
      );
  }
}

/**
 * Add new tags to a set of images.
 *
 * @param array tag ids
 * @param array image ids
 * @return void
 */
function add_tags($tags, $images)
{
  if (count($tags) == 0 or count($tags) == 0)
  {
    return;
  }

  // we can't insert twice the same {image_id,tag_id} so we must first
  // delete lines we'll insert later
  $query = '
DELETE
  FROM '.IMAGE_TAG_TABLE.'
  WHERE image_id IN ('.implode(',', $images).')
    AND tag_id IN ('.implode(',', $tags).')
;';
  pwg_query($query);

  $inserts = array();
  foreach ($images as $image_id)
  {
    foreach ($tags as $tag_id)
    {
      array_push(
        $inserts,
        array(
          'image_id' => $image_id,
          'tag_id' => $tag_id,
          )
        );
    }
  }
  mass_inserts(
    IMAGE_TAG_TABLE,
    array_keys($inserts[0]),
    $inserts
    );
}

function tag_id_from_tag_name($tag_name)
{
  global $page;

  $tag_name = trim($tag_name);
  if (isset($page['tag_id_from_tag_name_cache'][$tag_name]))
  {
    return $page['tag_id_from_tag_name_cache'][$tag_name];
  }

  // does the tag already exists?
  $query = '
SELECT id
  FROM '.TAGS_TABLE.'
  WHERE name = \''.$tag_name.'\'
;';
  $existing_tags = array_from_query($query, 'id');

  if (count($existing_tags) == 0)
  {
    mass_inserts(
      TAGS_TABLE,
      array('name', 'url_name'),
      array(
        array(
          'name' => $tag_name,
          'url_name' => str2url($tag_name),
          )
        )
      );

    $page['tag_id_from_tag_name_cache'][$tag_name] = mysql_insert_id();
  }
  else
  {
    $page['tag_id_from_tag_name_cache'][$tag_name] = $existing_tags[0];
  }

  return $page['tag_id_from_tag_name_cache'][$tag_name];
}

function set_tags_of($tags_of)
{
  if (count($tags_of) > 0)
  {
    $query = '
DELETE
  FROM '.IMAGE_TAG_TABLE.'
  WHERE image_id IN ('.implode(',', array_keys($tags_of)).')
;';
    pwg_query($query);

    $inserts = array();

    foreach ($tags_of as $image_id => $tag_ids)
    {
      foreach ($tag_ids as $tag_id)
      {
        array_push(
          $inserts,
          array(
            'image_id' => $image_id,
            'tag_id' => $tag_id,
            )
          );
      }
    }

    mass_inserts(
      IMAGE_TAG_TABLE,
      array_keys($inserts[0]),
      $inserts
      );
  }
}

/**
 * Do maintenance on all PWG tables
 *
 * @return nono
 */
function do_maintenance_all_tables()
{
  global $prefixeTable, $page;

  $all_tables = array();

  // List all tables
  $query = 'SHOW TABLES LIKE \''.$prefixeTable.'%\'';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    array_push($all_tables, $row[0]);
  }

  // Repair all tables
  $query = 'REPAIR TABLE '.implode(', ', $all_tables);
  $mysql_rc = pwg_query($query);

  // Re-Order all tables
  foreach ($all_tables as $table_name)
  {
    $all_primary_key = array();

    $query = 'DESC '.$table_name.';';
    $result = pwg_query($query);
    while ($row = mysql_fetch_array($result))
    {
      if ($row['Key'] == 'PRI')
      {
        array_push($all_primary_key, $row['Field']);
      }
    }

    if (count($all_primary_key) != 0)
    {
      $query = 'ALTER TABLE '.$table_name.' ORDER BY '.implode(', ', $all_primary_key).';';
      $mysql_rc = $mysql_rc && pwg_query($query);
    }
  }

  // Optimize all tables
  $query = 'OPTIMIZE TABLE '.implode(', ', $all_tables);
  $mysql_rc = $mysql_rc && pwg_query($query);
  if ($mysql_rc)
  {
    array_push(
          $page['infos'],
          l10n('Optimization completed')
          );
  }
  else
  {
    array_push(
          $page['errors'],
          l10n('Optimizations errors')
          );
  }
}

/**
 * Associate a list of images to a list of categories.
 *
 * The function will not duplicate links
 *
 * @param array images
 * @param array categories
 * @return void
 */
function associate_images_to_categories($images, $categories)
{
  if (count($images) == 0
      or count($categories) == 0)
  {
    return false;
  }

  $query = '
DELETE
  FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE image_id IN ('.implode(',', $images).')
    AND category_id IN ('.implode(',', $categories).')
;';
  pwg_query($query);

  $inserts = array();
  foreach ($categories as $category_id)
  {
    foreach ($images as $image_id)
    {
      array_push(
        $inserts,
        array(
          'image_id' => $image_id,
          'category_id' => $category_id,
          )
        );
    }
  }

  mass_inserts(
    IMAGE_CATEGORY_TABLE,
    array_keys($inserts[0]),
    $inserts
    );

  update_category($categories);
}

/**
 * Associate images associated to a list of source categories to a list of
 * destination categories.
 *
 * @param array sources
 * @param array destinations
 * @return void
 */
function associate_categories_to_categories($sources, $destinations)
{
  if (count($sources) == 0)
  {
    return false;
  }

  $query = '
SELECT image_id
  FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE category_id IN ('.implode(',', $sources).')
;';
  $images = array_from_query($query, 'image_id');

  associate_images_to_categories($images, $destinations);
}

/**
 * Refer main Piwigo URLs (currently PHPWG_DOMAIN domain)
 *
 * @param void
 * @return array like $conf['links']
 */
function pwg_URL()
{
  global $lang_info;
  $urls = array(
    'WIKI'       => 'http://'.PHPWG_DOMAIN.'/doc/',
    'HOME'       => 'http://'.PHPWG_DOMAIN.'/',
    'DEMO'       => 'http://demo.'.PHPWG_DOMAIN.'/',
    'FORUM'      => 'http://forum.'.PHPWG_DOMAIN.'/',
    'BUGS'       => 'http://bugs.'.PHPWG_DOMAIN.'/',
    'EXTENSIONS' => 'http://'.PHPWG_DOMAIN.'/ext',
    );
  if ( isset($lang_info['code']) and
       in_array($lang_info['code'], array('fr','en')) )
  { /* current wiki languages are French or English */
    $urls['WIKI'] .= 'doku.php?id='.$lang_info['code'].':'.$lang_info['code'];
    $urls['HOME'] .= '?lang='.$lang_info['code'];
  }
  return $urls;
}

/**
 * Invalidates cahed data (permissions and category counts) for all users.
 */
function invalidate_user_cache()
{
  $query = '
UPDATE '.USER_CACHE_TABLE.'
  SET need_update = \'true\'';
  pwg_query($query);
  trigger_action('invalidate_user_cache');
}

/**
 * adds the caracter set to a create table sql query.
 * all CREATE TABLE queries must call this function
 * @param string query - the sql query
 */
function create_table_add_character_set($query)
{
  defined('DB_CHARSET') or fatal_error('create_table_add_character_set DB_CHARSET undefined');
  if ('DB_CHARSET'!='')
  {
    if ( version_compare(mysql_get_server_info(), '4.1.0', '<') )
    {
      return $query;
    }
    $charset_collate = " DEFAULT CHARACTER SET ".DB_CHARSET;
    if (DB_COLLATE!='')
    {
      $charset_collate .= " COLLATE ".DB_COLLATE;
    }
    if ( is_array($query) )
    {
      foreach( $query as $id=>$q)
      {
        $q=trim($q);
        $q=trim($q, ';');
        if (preg_match('/^CREATE\s+TABLE/i',$q))
        {
          $q.=$charset_collate;
        }
        $q .= ';';
        $query[$id] = $q;
      }
    }
    else
    {
      $query=trim($query);
      $query=trim($query, ';');
      if (preg_match('/^CREATE\s+TABLE/i',$query))
      {
        $query.=$charset_collate;
      }
      $query .= ';';
    }
  }
  return $query;
}

/**
 * Returns array use on template with html_options method
 * @param Min and Max access to use
 * @return array of user access level
 */
function get_user_access_level_html_options($MinLevelAccess = ACCESS_FREE, $MaxLevelAccess = ACCESS_CLOSED)
{
  $tpl_options = array();
  for ($level = $MinLevelAccess; $level <= $MaxLevelAccess; $level++)
  {
    $tpl_options[$level] = l10n(sprintf('ACCESS_%d', $level));
  }
  return $tpl_options;
}

/**
 * returns a list of templates currently available in template-extension
 * Each .tpl file is extracted from template-extension.
 * @return array
 */
function get_extents($start='')
{
  if ($start == '') { $start = './template-extension'; }
  $dir = opendir($start);
  $extents = array();

  while (($file = readdir($dir)) !== false)
  {
    if ( $file == '.' or $file == '..' or $file == '.svn') continue;
    $path = $start . '/' . $file;
    if (is_dir($path))
    {
      $extents = array_merge($extents, get_extents($path));
    }
    elseif ( !is_link($path) and file_exists($path) 
            and get_extension($path) == 'tpl' )
    {
      $extents[] = substr($path, 21);
    }
  }
  return $extents;
}

function create_tag($tag_name)
{
  // does the tag already exists?
  $query = '
SELECT id
  FROM '.TAGS_TABLE.'
  WHERE name = \''.$tag_name.'\'
;';
  $existing_tags = array_from_query($query, 'id');

  if (count($existing_tags) == 0)
  {
    mass_inserts(
      TAGS_TABLE,
      array('name', 'url_name'),
      array(
        array(
          'name' => $tag_name,
          'url_name' => str2url($tag_name),
          )
        )
      );

    $inserted_id = mysql_insert_id();

    return array(
      'info' => sprintf(
        l10n('Tag "%s" was added'),
        stripslashes($tag_name)
        ),
      'id' => $inserted_id,
      );
  }
  else
  {
    return array(
      'error' => sprintf(
        l10n('Tag "%s" already exists'),
        stripslashes($tag_name)
        )
      );
  }
}

/**
 * Is the category accessible to the (Admin) user ?
 *
 * Note : if the user is not authorized to see this category, category jump
 * will be replaced by admin cat_modify page
 *
 * @param int category id to verify
 * @return bool
 */
function cat_admin_access($category_id)
{
  global $user;

  // $filter['visible_categories'] and $filter['visible_images']
  // are not used because it's not necessary (filter <> restriction)
  if (in_array($category_id, explode(',', $user['forbidden_categories'])))
  {
    return false;
  }
  return true;
}

/**
 * Retrieve data from external URL
 *
 * @param string $src: URL
 * @param global $dest: can be a file ressource or string
 * @return bool
 */
function fetchRemote($src, &$dest, $user_agent='Piwigo', $step=0)
{
  is_resource($dest) or $dest = '';

  // Try curl to read remote file
  if (function_exists('curl_init'))
  {
    $ch = @curl_init();
    @curl_setopt($ch, CURLOPT_URL, $src);
    @curl_setopt($ch, CURLOPT_HEADER, 0);
    @curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
    is_resource($dest) ?
      @curl_setopt($ch, CURLOPT_FILE, $dest):
      @curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $content = @curl_exec($ch);
    @curl_close($ch);
    if ($content !== false)
    {
      is_resource($dest) or $dest = $content;
      return true;
    }
  }

  // Try file_get_contents to read remote file
  if (ini_get('allow_url_fopen'))
  {
    $content = @file_get_contents($src);
    if ($content !== false)
    {
      is_resource($dest) ? @fwrite($dest, $content) : $dest = $content;
      return true;
    }
  }

  // Try fsockopen to read remote file
  if ($step > 3)
  {
    return false;
  }

  $src = parse_url($src);
  $host = $src['host'];
  $path = isset($src['path']) ? $src['path'] : '/';
  $path .= isset($src['query']) ? '?'.$src['query'] : '';
  
  if (($s = @fsockopen($host,80,$errno,$errstr,5)) === false)
  {
    return false;
  }

  fwrite($s,
    "GET ".$path." HTTP/1.0\r\n"
    ."Host: ".$host."\r\n"
    ."User-Agent: ".$user_agent."\r\n"
    ."Accept: */*\r\n"
    ."\r\n"
  );

  $i = 0;
  $in_content = false;
  while (!feof($s))
  {
    $line = fgets($s);

    if (rtrim($line,"\r\n") == '' && !$in_content)
    {
      $in_content = true;
      $i++;
      continue;
    }
    if ($i == 0)
    {
      if (!preg_match('/HTTP\/(\\d\\.\\d)\\s*(\\d+)\\s*(.*)/',rtrim($line,"\r\n"), $m))
      {
        fclose($s);
        return false;
      }
      $status = (integer) $m[2];
      if ($status < 200 || $status >= 400)
      {
        fclose($s);
        return false;
      }
    }
    if (!$in_content)
    {
      if (preg_match('/Location:\s+?(.+)$/',rtrim($line,"\r\n"),$m))
      {
        fclose($s);
        return fetchRemote(trim($m[1]),$dest,$user_agent,$step+1);
      }
      $i++;
      continue;
    }
    is_resource($dest) ? @fwrite($dest, $line) : $dest .= $line;
    $i++;
  }
  fclose($s);
  return true;
}

?>