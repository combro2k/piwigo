<?php
// +-----------------------------------------------------------------------+
// |                       category_calendar.inc.php                       |
// +-----------------------------------------------------------------------+
// | application   : PhpWebGallery <http://phpwebgallery.net>              |
// | branch        : BSF (Best So Far)                                     |
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

/**
 * This file is included by category.php to show thumbnails for the category
 * calendar
 * 
 */

// years of image availability
$query = '
SELECT YEAR('.$conf['calendar_datefield'].') AS year, COUNT(id) AS count
  FROM '.IMAGES_TABLE.', '.IMAGE_CATEGORY_TABLE.'
  '.$page['where'].'
    AND id = image_id
  GROUP BY year
;';
$result = mysql_query($query);
$calendar_years = array();
while ($row = mysql_fetch_array($result))
{
  $calendar_years[$row['year']] = $row['count'];
}

// if the year requested is not among the available years, we unset the
// variable
if (isset($page['calendar_year'])
    and !isset($calendar_years[$page['calendar_year']]))
{
  unset($page['calendar_year']);
}

// years navigation bar creation
$years_nav_bar = '';
foreach ($calendar_years as $calendar_year => $nb_picture_year)
{
  if (isset($page['calendar_year'])
      and $calendar_year == $page['calendar_year'])
  {
    $years_nav_bar.= ' <span class="dateSelected">'.$calendar_year.'</span>';
  }
  else
  {
    $url = PHPWG_ROOT_PATH.'category.php?cat=calendar';
    $url.= '&amp;year='.$calendar_year;
    $url = add_session_id($url);
    $years_nav_bar.= ' <a href="'.$url.'">'.$calendar_year.'</a>';
  }
}

$template->assign_block_vars(
  'calendar',
  array('YEARS_NAV_BAR' => $years_nav_bar)
  );

// months are calculated (to know which months are available, and how many
// pictures per month we can find) only if a year is requested.
if (isset($page['calendar_year']))
{
  // creation of hash associating the number of the month in the year with
  // the number of picture for this month : $calendar_months
  $query = '
SELECT DISTINCT(MONTH('.$conf['calendar_datefield'].')) AS month
     , COUNT(id) AS count
  FROM '.IMAGES_TABLE.', '.IMAGE_CATEGORY_TABLE.'
  '.$page['where'].'
    AND id = image_id
    AND YEAR('.$conf['calendar_datefield'].') = '.$page['calendar_year'].'
  GROUP BY MONTH('.$conf['calendar_datefield'].')
;';
  $result = mysql_query($query);
  $calendar_months = array();
  while ($row = mysql_fetch_array($result))
  {
    $calendar_months[$row['month']] = $row['count'];
  }

  // if a month is requested and is not among the available months, we unset
  // the requested month
  if (isset($page['calendar_month'])
      and !isset($calendar_months[$page['calendar_month']]))
  {
    unset($page['calendar_month']);
  }

  // months navigation bar creation
  $months_nav_bar = '';
  foreach ($calendar_months as $calendar_month => $nb_picture_month)
  {
    if (isset($page['calendar_month'])
        and $calendar_month == $page['calendar_month'])
    {
      $months_nav_bar.= ' <span class="dateSelected">';
      $months_nav_bar.= $lang['month'][(int)$calendar_month];
      $months_nav_bar.= '</span>';
    }
    else
    {
      $url = PHPWG_ROOT_PATH.'category.php?cat=calendar&amp;month=';
      $url.= $page['calendar_year'].'.'.sprintf('%02s', $calendar_month);
      $months_nav_bar.= ' ';
      $months_nav_bar.= '<a href="'.add_session_id($url).'">';
      $months_nav_bar.= $lang['month'][(int)$calendar_month];
      $months_nav_bar.= '</a>';
    }
  }
  $template->assign_block_vars(
    'calendar',
    array('MONTHS_NAV_BAR' => $months_nav_bar));
}

/**
 * 4 sub-cases are possibles for the calendar category :
 *
 *  1. show years if no year is requested
 *  2. show months of the requested year if no month is requested
 *  3. show days of the {year,month} requested if no day requested
 *  4. show categories of the requested day (+ a special category gathering
 *     all categories)
 */

if (!isset($page['calendar_year']))
{
  $nb_pics = count($calendar_years);
}
elseif (!isset($page['calendar_month']))
{
  $nb_pics = count($calendar_months);
}
elseif (!isset($page['calendar_day']))
{
  // creation of hash associating the number of the day in the month with
  // the number of picture for this day : $calendar_days
  $query = '
SELECT DISTINCT('.$conf['calendar_datefield'].') AS day, COUNT(id) AS count
  FROM '.IMAGES_TABLE.', '.IMAGE_CATEGORY_TABLE.'
  '.$page['where'].'
    AND id = image_id
    AND YEAR('.$conf['calendar_datefield'].') = '.$page['calendar_year'].'
    AND MONTH('.$conf['calendar_datefield'].') = '.$page['calendar_month'].'
  GROUP BY day
;';
  $result = mysql_query($query);
  $calendar_days = array();
  while ($row = mysql_fetch_array($result))
  {
    $calendar_days[$row['day']] = $row['count'];
  }
  $nb_pics = count($calendar_days);
}
elseif (isset($page['calendar_day']))
{
  // $page['calendar_date'] is the concatenation of year-month-day. simplier
  // to use in SQ queries
  $page['calendar_date'] = $page['calendar_year'];
  $page['calendar_date'].= '-'.$page['calendar_month'];
  $page['calendar_date'].= '-'.$page['calendar_day'];
  
  $query = '
SELECT category_id AS category, COUNT(id) AS count
  FROM '.IMAGES_TABLE.', '.IMAGE_CATEGORY_TABLE.'
  '.$page['where'].'
    AND '.$conf['calendar_datefield'].' = \''.$page['calendar_date'].'\'
    AND id = image_id
  GROUP BY category_id
;';
  $result = mysql_query($query);
  $calendar_categories = array();
  // special category 0 : gathering all available categories (0 cannot be a
  // oregular category identifier)
  $calendar_categories[0] = 0;
  while ($row = mysql_fetch_array($result))
  {
    $calendar_categories[$row['category']] = $row['count'];
  }
  // update the total number of pictures for this day
  $calendar_categories[0] = array_sum($calendar_categories);
  
  $nb_pics = count($calendar_categories);
}

// template thumbnail initialization
if ($nb_pics > 0)
{
  $template->assign_block_vars('thumbnails', array());
  // first line
  $template->assign_block_vars('thumbnails.line', array());
  // current row displayed
  $row_number = 0;
}

if (!isset($page['calendar_year']))
{
  // for each month of this year, display a random picture
  foreach ($calendar_years as $calendar_year => $nb_pics)
  {
    $query = '
SELECT file,tn_ext,'.$conf['calendar_datefield'].',storage_category_id
  FROM '.IMAGES_TABLE.', '.IMAGE_CATEGORY_TABLE.'
  '.$page['where'].'
    AND YEAR('.$conf['calendar_datefield'].') = '.$calendar_year.'
    AND id = image_id
  ORDER BY RAND()
  LIMIT 0,1
;';
    $row = mysql_fetch_array(mysql_query($query));
    
    $file = get_filename_wo_extension($row['file']);
    
    // creating links for thumbnail and associated category
    if (isset($row['tn_ext']) and $row['tn_ext'] != '')
    {
      $thumbnail_link = get_complete_dir($row['storage_category_id']);
      $thumbnail_link.= 'thumbnail/'.$conf['prefix_thumbnail'];
      $thumbnail_link.= $file.'.'.$row['tn_ext'];
    }
    else
    {
      $thumbnail_link = './template/'.$user['template'].'/mimetypes/';
      $thumbnail_link.= strtolower(get_extension($row['file'])).'.png';
    }
    
    $name = $calendar_year.' ('.$nb_pics.')';

    $thumbnail_title = $lang['calendar_picture_hint'].$name;
      
    $url_link = PHPWG_ROOT_PATH.'category.php?cat=calendar';
    $url_link.= '&amp;year='.$calendar_year;
    
    $template->assign_block_vars(
      'thumbnails.line.thumbnail',
      array(
        'IMAGE'=>$thumbnail_link,
        'IMAGE_ALT'=>$row['file'],
        'IMAGE_TITLE'=>$thumbnail_title,
        'IMAGE_NAME'=>$name,
          
        'U_IMG_LINK'=>add_session_id($url_link)
       )
     );

    // create a new line ?
    if (++$row_number == $user['nb_image_line'])
    {
      $template->assign_block_vars('thumbnails.line', array());
      $row_number = 0;
    }
  }
}
elseif (!isset($page['calendar_month']))
{
  // for each month of this year, display a random picture
  foreach ($calendar_months as $calendar_month => $nb_pics)
  {
    $query = '
SELECT file,tn_ext,'.$conf['calendar_datefield'].',storage_category_id
  FROM '.IMAGES_TABLE.', '.IMAGE_CATEGORY_TABLE.'
  '.$page['where'].'
    AND YEAR('.$conf['calendar_datefield'].') = '.$page['calendar_year'].'
    AND MONTH('.$conf['calendar_datefield'].') = '.$calendar_month.'
    AND id = image_id
  ORDER BY RAND()
  LIMIT 0,1
;';
    $row = mysql_fetch_array(mysql_query($query));
    
    $file = get_filename_wo_extension($row['file']);

    // creating links for thumbnail and associated category
    if (isset($row['tn_ext']) and $row['tn_ext'] != '')
    {
      $thumbnail_link = get_complete_dir($row['storage_category_id']);
      $thumbnail_link.= 'thumbnail/'.$conf['prefix_thumbnail'];
      $thumbnail_link.= $file.'.'.$row['tn_ext'];
    }
    else
    {
      $thumbnail_link = './template/'.$user['template'].'/mimetypes/';
      $thumbnail_link.= strtolower(get_extension($row['file'])).'.png';
    }
      
    $name = $lang['month'][$calendar_month];
    $name.= ' '.$page['calendar_year'];
    $name.= ' ('.$nb_pics.')';

    $thumbnail_title = $lang['calendar_picture_hint'].$name;
      
    $url_link = PHPWG_ROOT_PATH.'category.php?cat=calendar';
    $url_link.= '&amp;month='.$page['calendar_year'].'.';
    if ($calendar_month < 10)
    {
      // adding leading zero
      $url_link.= '0';
    }
    $url_link.= $calendar_month;
    
    $template->assign_block_vars(
      'thumbnails.line.thumbnail',
      array(
        'IMAGE'=>$thumbnail_link,
        'IMAGE_ALT'=>$row['file'],
        'IMAGE_TITLE'=>$thumbnail_title,
        'IMAGE_NAME'=>$name,
          
        'U_IMG_LINK'=>add_session_id($url_link)
       )
     );

    // create a new line ?
    if (++$row_number == $user['nb_image_line'])
    {
      $template->assign_block_vars('thumbnails.line', array());
      $row_number = 0;
    }
  }
}
elseif (!isset($page['calendar_day']))
{
  // for each day of the requested month, display a random picture
  foreach ($calendar_days as $calendar_day => $nb_pics)
  {
    $query = '
SELECT file,tn_ext,'.$conf['calendar_datefield'].',storage_category_id
  FROM '.IMAGES_TABLE.', '.IMAGE_CATEGORY_TABLE.'
  '.$page['where'].'
    AND '.$conf['calendar_datefield'].' = \''.$calendar_day.'\'
    AND id = image_id
  ORDER BY RAND()
  LIMIT 0,1
;';
    $row = mysql_fetch_array(mysql_query($query));
    
    $file = get_filename_wo_extension($row['file']);
    
    // creating links for thumbnail and associated category
    if (isset($row['tn_ext']) and $row['tn_ext'] != '')
    {
      $thumbnail_link = get_complete_dir($row['storage_category_id']);
      $thumbnail_link.= 'thumbnail/'.$conf['prefix_thumbnail'];
      $thumbnail_link.= $file.'.'.$row['tn_ext'];
    }
    else
    {
      $thumbnail_link = './template/'.$user['template'].'/mimetypes/';
      $thumbnail_link.= strtolower(get_extension($row['file'])).'.png';
    }

    list($year,$month,$day) = explode('-', $calendar_day);
    $unixdate = mktime(0,0,0,$month,$day,$year);
    $name = $lang['day'][date("w", $unixdate)];
    $name.= ' '.$day;
    $name.= ' ('.$nb_pics.')';
      
    $thumbnail_title = $lang['calendar_picture_hint'].$name;

    $url_link = PHPWG_ROOT_PATH.'category.php';
    $url_link.= '?cat=calendar&amp;day='.str_replace('-', '.', $calendar_day);
    
    $template->assign_block_vars(
      'thumbnails.line.thumbnail',
      array(
        'IMAGE'=>$thumbnail_link,
        'IMAGE_ALT'=>$row['file'],
        'IMAGE_TITLE'=>$thumbnail_title,
        'IMAGE_NAME'=>$name,
          
        'U_IMG_LINK'=>add_session_id($url_link)
         )
       );

    // create a new line ?
    if (++$row_number == $user['nb_image_line'])
    {
      $template->assign_block_vars('thumbnails.line', array());
      $row_number = 0;
    }
  }
}
elseif (isset($page['calendar_day']))
{
  // for each category of this day, display a random picture
  foreach ($calendar_categories as $calendar_category => $nb_pics)
  {
    if ($calendar_category == 0)
    {
      $name = '[all]';
    }
    else
    {
      $cat_infos = get_cat_info( $calendar_category );
      $name = get_cat_display_name($cat_infos['name'],'<br />','',false);
      $name = '['.$name.']';
    }
    $name.= ' ('.$nb_pics.')';
    
    $query = '
SELECT file,tn_ext,'.$conf['calendar_datefield'].',storage_category_id
  FROM '.IMAGES_TABLE.', '.IMAGE_CATEGORY_TABLE.'
  '.$page['where'].'
    AND '.$conf['calendar_datefield'].' = \''.$page['calendar_date'].'\'';
    if ($calendar_category != 0)
    {
      $query.= '
    AND category_id = '.$calendar_category;
    }
    $query.= '
    AND id = image_id
  ORDER BY RAND()
  LIMIT 0,1
;';
    $row = mysql_fetch_array(mysql_query($query));
    
    $file = get_filename_wo_extension($row['file']);
    
    // creating links for thumbnail and associated category
    if (isset($row['tn_ext']) and $row['tn_ext'] != '')
    {
      $thumbnail_link = get_complete_dir($row['storage_category_id']);
      $thumbnail_link.= 'thumbnail/'.$conf['prefix_thumbnail'];
      $thumbnail_link.= $file.'.'.$row['tn_ext'];
    }
    else
    {
      $thumbnail_link = './template/'.$user['template'].'/mimetypes/';
      $thumbnail_link.= strtolower(get_extension($row['file'])).'.png';
    }
    
    $thumbnail_title = $lang['calendar_picture_hint'].$name;

    $url_link = PHPWG_ROOT_PATH.'category.php?cat=search';
    $url_link.= '&amp;search='.$conf['calendar_datefield'].':'.$_GET['day'];
    if ($calendar_category != 0)
    {
      $url_link.= ';cat:'.$calendar_category.'|AND';
    }
    
    $template->assign_block_vars(
      'thumbnails.line.thumbnail',
      array(
        'IMAGE'=>$thumbnail_link,
        'IMAGE_ALT'=>$row['file'],
        'IMAGE_TITLE'=>$thumbnail_title,
        'IMAGE_NAME'=>$name,
          
        'U_IMG_LINK'=>add_session_id($url_link)
         )
       );
    $template->assign_block_vars('thumbnails.line.thumbnail.bullet',array());
    
    // create a new line ?
    if (++$row_number == $user['nb_image_line'])
    {
      $template->assign_block_vars('thumbnails.line', array());
      $row_number = 0;
    }
  }
}
?>