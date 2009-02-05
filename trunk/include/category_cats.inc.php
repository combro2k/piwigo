<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2009 Piwigo Team                  http://piwigo.org |
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

/**
 * This file is included by the main page to show thumbnails for a category
 * that have only subcategories or to show recent categories
 *
 */

if ($page['section']=='recent_cats')
{
  // $user['forbidden_categories'] including with USER_CACHE_CATEGORIES_TABLE
  $query = '
SELECT
  c.*, nb_images, date_last, max_date_last, count_images, count_categories
  FROM '.CATEGORIES_TABLE.' c INNER JOIN '.USER_CACHE_CATEGORIES_TABLE.'
  ON id = cat_id and user_id = '.$user['id'].'
  WHERE date_last >= SUBDATE(
    CURRENT_DATE,INTERVAL '.$user['recent_period'].' DAY
  )
'.get_sql_condition_FandF
  (
    array
      (
        'visible_categories' => 'id',
      ),
    'AND'
  ).'
;';
}
else
{
  // $user['forbidden_categories'] including with USER_CACHE_CATEGORIES_TABLE
  $query = '
SELECT
  c.*, nb_images, date_last, max_date_last, count_images, count_categories
  FROM '.CATEGORIES_TABLE.' c INNER JOIN '.USER_CACHE_CATEGORIES_TABLE.'
  ON id = cat_id and user_id = '.$user['id'].'
  WHERE id_uppercat '.
  (!isset($page['category']) ? 'is NULL' : '= '.$page['category']['id']).'
'.get_sql_condition_FandF
  (
    array
      (
        'visible_categories' => 'id',
      ),
    'AND'
  ).'
  ORDER BY rank
;';
}

$result = pwg_query($query);
$categories = array();
$category_ids = array();
$image_ids = array();

while ($row = mysql_fetch_assoc($result))
{
  $row['is_child_date_last'] = @$row['max_date_last']>@$row['date_last'];

  if (isset($row['representative_picture_id'])
      and is_numeric($row['representative_picture_id']))
  { // if a representative picture is set, it has priority
    $image_id = $row['representative_picture_id'];
  }
  else if ($conf['allow_random_representative'])
  {// searching a random representant among elements in sub-categories
    if ($row['count_images']>0)
    {
      $query = '
SELECT image_id
  FROM '.CATEGORIES_TABLE.' AS c INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic
    ON ic.category_id = c.id';
      $query.= '
  WHERE (c.id='.$row['id'].' OR uppercats LIKE \''.$row['uppercats'].',%\')'
    .get_sql_condition_FandF
    (
      array
        (
          'forbidden_categories' => 'c.id',
          'visible_categories' => 'c.id',
          'visible_images' => 'image_id'
        ),
      "\n  AND"
    ).'
  ORDER BY RAND()
  LIMIT 0,1
;';
      $subresult = pwg_query($query);
      if (mysql_num_rows($subresult) > 0)
      {
        list($image_id) = mysql_fetch_row($subresult);
      }
    }
  }
  else
  { // searching a random representant among representant of sub-categories
    if ($row['count_categories']>0 and $row['count_images']>0)
    {
      $query = '
  SELECT representative_picture_id
    FROM '.CATEGORIES_TABLE.' INNER JOIN '.USER_CACHE_CATEGORIES_TABLE.'
    ON id = cat_id and user_id = '.$user['id'].'
    WHERE uppercats LIKE \''.$row['uppercats'].',%\'
      AND representative_picture_id IS NOT NULL'
    .get_sql_condition_FandF
    (
      array
        (
          'visible_categories' => 'id',
        ),
      "\n  AND"
    ).'
    ORDER BY RAND()
    LIMIT 0,1
  ;';
      $subresult = pwg_query($query);
      if (mysql_num_rows($subresult) > 0)
      {
        list($image_id) = mysql_fetch_row($subresult);
      }
    }
  }

  if (isset($image_id))
  {
    $row['representative_picture_id'] = $image_id;
    array_push($image_ids, $image_id);
    array_push($categories, $row);
    array_push($category_ids, $row['id']);
  }
  unset($image_id);
}

if ($conf['display_fromto'])
{
  $dates_of_category = array();
  if (count($category_ids) > 0)
  {
    $query = '
SELECT
    category_id,
    MIN(date_creation) AS date_creation_min,
    MAX(date_creation) AS date_creation_max
  FROM '.IMAGE_CATEGORY_TABLE.'
    INNER JOIN '.IMAGES_TABLE.' ON image_id = id
  WHERE category_id IN ('.implode(',', $category_ids).')
'.get_sql_condition_FandF
  (
    array
      (
        'visible_categories' => 'category_id',
        'visible_images' => 'id'
      ),
    'AND'
  ).'
  GROUP BY category_id
;';
    $result = pwg_query($query);
    while ($row = mysql_fetch_array($result))
    {
      $dates_of_category[ $row['category_id'] ] = array(
        'from' => $row['date_creation_min'],
        'to'   => $row['date_creation_max'],
        );
    }
  }
}

if ($page['section']=='recent_cats')
{
  usort($categories, 'global_rank_compare');
}
if (count($categories) > 0)
{
  $thumbnail_src_of = array();

  $query = '
SELECT id, path, tn_ext
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', $image_ids).')
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_assoc($result))
  {
    $thumbnail_src_of[$row['id']] = get_thumbnail_url($row);
  }
}

if (count($categories) > 0)
{
  // Update filtered data
  if (function_exists('update_cats_with_filtered_data'))
  {
    update_cats_with_filtered_data($categories);
  }

  $template->set_filename('index_category_thumbnails', 'mainpage_categories.tpl');

  trigger_action('loc_begin_index_category_thumbnails', $categories);

  $tpl_thumbnails_var = array();

  foreach ($categories as $category)
  {
    $category['name'] = trigger_event(
        'render_category_name',
        $category['name'],
        'subcatify_category_name'
        );

    if ($page['section']=='recent_cats')
    {
      $name = get_cat_display_name_cache($category['uppercats'], null, false);
    }
    else
    {
      $name = $category['name'];
    }

    $tpl_var =
        array(
          'ID'    => $category['id'],
          'TN_SRC'   => $thumbnail_src_of[$category['representative_picture_id']],
          'TN_ALT'   => strip_tags($category['name']),
          'ICON_TS'  => get_icon($category['max_date_last'], $category['is_child_date_last']),

          'URL'   => make_index_url(
            array(
              'category' => $category
              )
            ),
          'CAPTION_NB_IMAGES' => get_display_images_count
                                  (
                                    $category['nb_images'],
                                    $category['count_images'],
                                    $category['count_categories'],
                                    true,
                                    '<br />'
                                  ),
          'DESCRIPTION' =>
            trigger_event('render_category_literal_description',
              trigger_event('render_category_description',
                @$category['comment'],
                'subcatify_category_description')),
          'NAME'  => $name,
        );

    if ($conf['display_fromto'])
    {
      if (isset($dates_of_category[ $category['id'] ]))
      {
        $from = $dates_of_category[ $category['id'] ]['from'];
        $to   = $dates_of_category[ $category['id'] ]['to'];

        if (!empty($from))
        {
          $info = '';

          if ($from == $to)
          {
            $info = format_date($from);
          }
          else
          {
            $info = sprintf(
              l10n('from %s to %s'),
              format_date($from),
              format_date($to)
              );
          }
          $tpl_var['INFO_DATES'] = $info;
        }
      }
    }//fromto

    $tpl_thumbnails_var[] = $tpl_var;
  }

  $tpl_thumbnails_var = trigger_event('loc_end_index_category_thumbnails', $tpl_thumbnails_var, $categories);
  $template->assign( 'category_thumbnails', $tpl_thumbnails_var);

  $template->assign_var_from_handle('CATEGORIES', 'index_category_thumbnails');
}
pwg_debug('end include/category_cats.inc.php');
?>