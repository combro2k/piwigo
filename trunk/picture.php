<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2006 PhpWebGallery Team - http://phpwebgallery.net |
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

define('PHPWG_ROOT_PATH','./');
include_once(PHPWG_ROOT_PATH.'include/common.inc.php');
include_once(PHPWG_ROOT_PATH.'include/functions_rate.inc.php');
include(PHPWG_ROOT_PATH.'include/section_init.inc.php');

// Check Access and exit when user status is not ok
check_status(ACCESS_GUEST);

// access authorization check
if (isset($page['category']))
{
  check_restrictions($page['category']);
}

// if this image_id doesn't correspond to this category, an error message is
// displayed, and execution is stopped
if (!in_array($page['image_id'], $page['items']))
{
  die('Fatal: this picture does not belong to this section');
}

// +-----------------------------------------------------------------------+
// |                            initialization                             |
// +-----------------------------------------------------------------------+

$page['rank_of'] = array_flip($page['items']);

// caching first_rank, last_rank, current_rank in the displayed
// section. This should also help in readability.
$page['first_rank']   = 0;
$page['last_rank']    = count($page['items']) - 1;
$page['current_rank'] = $page['rank_of'][ $page['image_id'] ];

// caching current item : readability purpose
$page['current_item'] = $page['image_id'];

if ($page['current_rank'] != $page['first_rank'])
{
  // "go to first picture of this section" link is displayed only if the
  // displayed item is not the first.
  $template->assign_block_vars(
    'first',
    array(
      'U_IMG' => duplicate_picture_URL(
        // redefinitions
        array(
          'image_id' => $page['items'][ $page['first_rank'] ],
          ),
        // removes
        array()
        )
      )
    );

  // caching previous item : readability purpose
  $page['previous_item'] = $page['items'][ $page['current_rank'] - 1 ];
}

if ($page['current_rank'] != $page['last_rank'])
{
  // "go to last picture of this section" link is displayed only if the
  // displayed item is not the last.
  $template->assign_block_vars(
    'last',
    array(
      'U_IMG' => duplicate_picture_URL(
        // redefinitions
        array(
          'image_id' => $page['items'][ $page['last_rank'] ],
          ),
        // removes
        array()
        )
      )
    );

  // caching next item : readability purpose
  $page['next_item'] = $page['items'][ $page['current_rank'] + 1 ];
}

$url_up = duplicate_index_URL(
  array(
    'start' =>
      floor($page['current_rank'] / $user['nb_image_page'])
      * $user['nb_image_page']
    ),
  array(
    'start',
    )
  );

$url_self = duplicate_picture_URL();

// +-----------------------------------------------------------------------+
// |                                actions                                |
// +-----------------------------------------------------------------------+

/**
 * Actions are favorite adding, user comment deletion, setting the picture
 * as representative of the current category...
 *
 * Actions finish by a redirection
 */

if (isset($_GET['action']))
{
  switch ($_GET['action'])
  {
    case 'add_to_favorites' :
    {
      $query = '
INSERT INTO '.FAVORITES_TABLE.'
  (image_id,user_id)
  VALUES
  ('.$page['image_id'].','.$user['id'].')
;';
      pwg_query($query);

      redirect($url_self);
      
      break;
    }
    case 'remove_from_favorites' :
    {
      $query = '
DELETE FROM '.FAVORITES_TABLE.'
  WHERE user_id = '.$user['id'].'
    AND image_id = '.$page['image_id'].'
;';
      pwg_query($query);

      if ('favorites' == $page['section'])
      {
        redirect($url_up);
      }
      else
      {
        redirect($url_self);
      }
      
      break;
    }
    case 'set_as_representative' :
    {
      if (is_admin() and isset($page['category']) and !is_adviser())
      {
        $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET representative_picture_id = '.$page['image_id'].'
  WHERE id = '.$page['category'].'
;';
        pwg_query($query);
      }
      
      redirect($url_self);
  
      break;
    }
    case 'toggle_metadata' :
    {
      break;
    }
    case 'add_to_caddie' :
    {
      if (!is_adviser())
      {
        fill_caddie(array($page['image_id']));
      }
      redirect($url_self);
      break;
    }
    case 'rate' :
    {
      rate_picture($user['id'], $page['image_id'], $_GET['rate']);
      redirect($url_self);
    }
    case 'delete_comment' :
    {
      if (isset($_GET['comment_to_delete'])
          and is_numeric($_GET['comment_to_delete'])
          and is_admin())
      {
        $query = '
DELETE FROM '.COMMENTS_TABLE.'
  WHERE id = '.$_GET['comment_to_delete'].'
;';
        pwg_query( $query );
      }

      redirect($url_self);
    }
  }
}

// incrementation of the number of hits, we do this only if no action
$query = '
UPDATE
  '.IMAGES_TABLE.'
  SET hit = hit+1
  WHERE id = '.$page['image_id'].'
;';
pwg_query($query);

//---------------------------------------------------------- related categories
$query = '
SELECT category_id,uppercats,commentable,global_rank
  FROM '.IMAGE_CATEGORY_TABLE.'
    INNER JOIN '.CATEGORIES_TABLE.' ON category_id = id
  WHERE image_id = '.$page['image_id'].'
    AND category_id NOT IN ('.$user['forbidden_categories'].')
;';
$result = pwg_query($query);
$related_categories = array();
while ($row = mysql_fetch_array($result))
{
  array_push($related_categories, $row);
}
usort($related_categories, 'global_rank_compare');
//------------------------------------- prev, current & next picture management
$picture = array();

$ids = array($page['image_id']);
if (isset($page['previous_item']))
{
  array_push($ids, $page['previous_item']);
}
if (isset($page['next_item']))
{
  array_push($ids, $page['next_item']);
}

$query = '
SELECT *
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', $ids).')
;';

$result = pwg_query($query);

while ($row = mysql_fetch_array($result))
{
  if (isset($page['previous_item']) and $row['id'] == $page['previous_item'])
  {
    $i = 'prev';
  }
  else if (isset($page['next_item']) and $row['id'] == $page['next_item'])
  {
    $i = 'next';
  }
  else
  {
    $i = 'current';
  }

  foreach (array_keys($row) as $key)
  {
    if (!is_numeric($key))
    {
      $picture[$i][$key] = $row[$key];
    }
  }

  $picture[$i]['is_picture'] = false;
  if (in_array(get_extension($row['file']), $conf['picture_ext']))
  {
    $picture[$i]['is_picture'] = true;
  }

  $cat_directory = dirname($row['path']);
  $file_wo_ext = get_filename_wo_extension($row['file']);

  $icon = get_themeconf('mime_icon_dir');
  $icon.= strtolower(get_extension($row['file'])).'.png';

  if (isset($row['representative_ext']) and $row['representative_ext'] != '')
  {
    $picture[$i]['src'] =
      $cat_directory.'/pwg_representative/'
      .$file_wo_ext.'.'.$row['representative_ext'];
  }
  else
  {
    $picture[$i]['src'] = $icon;
  }
  // special case for picture files
  if ($picture[$i]['is_picture'])
  {
    $picture[$i]['src'] = $row['path'];
    // if we are working on the "current" element, we search if there is a
    // high quality picture
    if ($i == 'current')
    {
      if (($row['has_high'] == 'true') and ($user['enabled_high'] == 'true'))
      {
        $url_high=$cat_directory.'/pwg_high/'.$row['file'];
        $picture[$i]['high'] = $url_high;
      }
    }
  }

  // if picture is not a file, we need the download link
  if (!$picture[$i]['is_picture'])
  {
    $picture[$i]['download'] = $row['path'];
  }

  $picture[$i]['thumbnail'] = get_thumbnail_src($row['path'], @$row['tn_ext']);

  if ( !empty( $row['name'] ) )
  {
    $picture[$i]['name'] = $row['name'];
  }
  else
  {
    $picture[$i]['name'] = str_replace('_', ' ', $file_wo_ext);
  }

  $picture[$i]['url'] = duplicate_picture_URL(
    array(
      'image_id' => $row['id'],
      ),
    array(
      'start',
      )
    );
}

$url_admin =
  PHPWG_ROOT_PATH.'admin.php?page=picture_modify'
  .'&amp;cat_id='.(isset($page['category']) ? $page['category'] : '')
  .'&amp;image_id='.$page['image_id']
;

$url_slide =
  $picture['current']['url']
  .'&amp;slideshow='.$conf['slideshow_period']
;

$title =  $picture['current']['name'];
$refresh = 0;
if ( isset( $_GET['slideshow'] ) and isset($page['next_item']) )
{
  $refresh= $_GET['slideshow'];
  $url_link = $picture['next']['url'].'&amp;slideshow='.$refresh;
}

$title_img = $picture['current']['name'];
if ( isset( $page['cat'] ) )
{
  if (is_numeric( $page['cat'] ))
  {
    $title_img = replace_space(get_cat_display_name($page['cat_name']));
  }
  else if ( $page['cat'] == 'search' )
  {
    $title_img = replace_search( $title_img, $_GET['search'] );
  }
}
$title_nb = ($page['current_rank'] + 1).'/'.$page['cat_nb_images'];

// calculation of width and height
if (empty($picture['current']['width']))
{
  $taille_image = @getimagesize($picture['current']['src']);
  $original_width = $taille_image[0];
  $original_height = $taille_image[1];
}
else
{
  $original_width = $picture['current']['width'];
  $original_height = $picture['current']['height'];
}

$picture_size = get_picture_size(
  $original_width,
  $original_height,
  @$user['maxwidth'],
  @$user['maxheight']
  );

// metadata
if ($conf['show_exif'] or $conf['show_iptc'])
{
  $metadata_showable = true;
}
else
{
  $metadata_showable = false;
}

// $url_metadata = PHPWG_ROOT_PATH.'picture.php';
// $url_metadata .=  get_query_string_diff(array('add_fav', 'slideshow', 'show_metadata'));
// if ($metadata_showable and !isset($_GET['show_metadata']))
// {
//   $url_metadata.= '&amp;show_metadata=1';
// }

// TODO: rewrite metadata display to toggle on/off user_infos.show_metadata
$url_metadata = duplicate_picture_URL();

$page['body_id'] = 'thePicturePage';
//------------------------------------------------------- navigation management
if (isset($page['previous_item']))
{
  $template->assign_block_vars(
    'previous',
    array(
      'TITLE_IMG' => $picture['prev']['name'],
      'IMG' => $picture['prev']['thumbnail'],
      'U_IMG' => $picture['prev']['url'],
      'U_IMG_SRC' => $picture['prev']['src']
      )
    );
}

if (isset($page['next_item']))
{
  $template->assign_block_vars(
    'next',
    array(
      'TITLE_IMG' => $picture['next']['name'],
      'IMG' => $picture['next']['thumbnail'],
      'U_IMG' => $picture['next']['url'],
      'U_IMG_SRC' => $picture['next']['src'] // allow navigator to preload
      )
    );
}

include(PHPWG_ROOT_PATH.'include/page_header.php');
$template->set_filenames(array('picture'=>'picture.tpl'));

$template->assign_vars(
  array(
    'CATEGORY' => $title_img,
    'PHOTO' => $title_nb,
    'TITLE' => $picture['current']['name'],
    'SRC_IMG' => $picture['current']['src'],
    'ALT_IMG' => $picture['current']['file'],
    'WIDTH_IMG' => $picture_size[0],
    'HEIGHT_IMG' => $picture_size[1],

    'LEVEL_SEPARATOR' => $conf['level_separator'],

    'L_HOME' => $lang['home'],
    'L_SLIDESHOW' => $lang['slideshow'],
    'L_STOP_SLIDESHOW' => $lang['slideshow_stop'],
    'L_PREV_IMG' =>$lang['previous_page'].' : ',
    'L_NEXT_IMG' =>$lang['next_page'].' : ',
    'L_ADMIN' =>$lang['link_info_image'],
    'L_COMMENT_TITLE' =>$lang['comments_title'],
    'L_ADD_COMMENT' =>$lang['comments_add'],
    'L_DELETE_COMMENT' =>$lang['comments_del'],
    'L_DELETE' =>$lang['delete'],
    'L_SUBMIT' =>$lang['submit'],
    'L_AUTHOR' =>  $lang['upload_author'],
    'L_COMMENT' =>$lang['comment'],
    'L_DOWNLOAD' => $lang['download'],
    'L_DOWNLOAD_HINT' => $lang['download_hint'],
    'L_PICTURE_METADATA' => $lang['picture_show_metadata'],
    'L_PICTURE_HIGH' => $lang['picture_high'],
    'L_UP_HINT' => $lang['home_hint'],
    'L_UP_ALT' => $lang['home'],

    'U_HOME' => make_index_URL(),
    'U_UP' => $url_up,
    'U_METADATA' => $url_metadata,
    'U_ADMIN' => $url_admin,
    'U_SLIDESHOW'=> $url_slide,
    'U_ADD_COMMENT' => $url_self,
    )
  );

if ($conf['show_picture_name_on_title'])
{
  $template->assign_block_vars('title', array());
}

//------------------------------------------------------- upper menu management

// download link if file is not a picture
if (!$picture['current']['is_picture'])
{
  $template->assign_block_vars(
    'download',
    array(
      'U_DOWNLOAD' => $picture['current']['download']
      )
    );
}

// display a high quality link if present
if (isset($picture['current']['high']))
{
  $uuid = uniqid(rand());
  
  $template->assign_block_vars(
    'high',
    array(
      'U_HIGH' => $picture['current']['high'],
      'UUID'   => $uuid,
      )
    );
  
  $template->assign_block_vars(
    'download',
    array(
      'U_DOWNLOAD' => PHPWG_ROOT_PATH.'action.php?dwn='
      .$picture['current']['high']
      )
    );
}

// button to set the current picture as representative
if (is_admin() and isset($page['category']))
{
  $template->assign_block_vars(
    'representative',
    array(
      'URL' => $url_self.'&amp;action=set_as_representative'
      )
    );
}

// caddie button
if (is_admin())
{
  $template->assign_block_vars(
    'caddie',
    array(
      'URL' => $url_self.'&amp;action=add_to_caddie'
      )
    );
}

// favorite manipulation
if (!$user['is_the_guest'])
{
  // verify if the picture is already in the favorite of the user
  $query = '
SELECT COUNT(*) AS nb_fav
  FROM '.FAVORITES_TABLE.'
  WHERE image_id = '.$page['image_id'].'
    AND user_id = '.$user['id'].'
;';
  $result = pwg_query($query);
  $row = mysql_fetch_array($result);
  
  if ($row['nb_fav'] == 0)
  {
    $url = $url_self.'&amp;action=add_to_favorites';

    $template->assign_block_vars(
      'favorite',
      array(
        'FAVORITE_IMG'  => get_themeconf('icon_dir').'/favorite.png',
        'FAVORITE_HINT' => $lang['add_favorites_hint'],
        'FAVORITE_ALT'  => $lang['add_favorites_alt'],
        'U_FAVORITE'    => $url_self.'&amp;action=add_to_favorites',
        )
      );
  }
  else
  {
    $template->assign_block_vars(
      'favorite',
      array(
        'FAVORITE_IMG'  => get_themeconf('icon_dir').'/del_favorite.png',
        'FAVORITE_HINT' => $lang['del_favorites_hint'],
        'FAVORITE_ALT'  => $lang['del_favorites_alt'],
        'U_FAVORITE'    => $url_self.'&amp;action=remove_from_favorites',
        )
      );
  }
}
//------------------------------------ admin link for information modifications
if ( is_admin() )
{
  $template->assign_block_vars('admin', array());
}

//--------------------------------------------------------- picture information
// legend
if (isset($picture['current']['comment'])
    and !empty($picture['current']['comment']))
{
  $template->assign_block_vars(
    'legend',
    array(
      'COMMENT_IMG' => nl2br($picture['current']['comment'])
      ));
}

$infos = array();

// author
if (!empty($picture['current']['author']))
{
  $infos['INFO_AUTHOR'] =
    // FIXME because of search engine partial rewrite, giving the author
    // name threw GET is not supported anymore. This feature should come
    // back later, with a better design
//     '<a href="'.
//       PHPWG_ROOT_PATH.'category.php?cat=search'.
//       '&amp;search=author:'.$picture['current']['author']
//       .'">'.$picture['current']['author'].'</a>';
    $picture['current']['author'];
}
else
{
  $infos['INFO_AUTHOR'] = l10n('N/A');
}

// creation date
if (!empty($picture['current']['date_creation']))
{
  $val = format_date($picture['current']['date_creation']);
  $infos['INFO_CREATION_DATE'] = '<a href="'.
       PHPWG_ROOT_PATH.'category.php?calendar=created-c-'.
       $picture['current']['date_creation'].'">'.$val.'</a>';
}
else
{
  $infos['INFO_CREATION_DATE'] = l10n('N/A');
}

// date of availability
$val = format_date($picture['current']['date_available'], 'mysql_datetime');
$infos['INFO_POSTED_DATE'] = '<a href="'.
   PHPWG_ROOT_PATH.'category.php?calendar=posted-c-'.
   substr($picture['current']['date_available'],0,10).'">'.$val.'</a>';

// size in pixels
if ($picture['current']['is_picture'])
{
  if ($original_width != $picture_size[0]
      or $original_height != $picture_size[1])
  {
    $infos['INFO_DIMENSIONS'] =
      '<a href="'.$picture['current']['src'].'" title="'.
      l10n('Original dimensions').'">'.
      $original_width.'*'.$original_height.'</a>';
  }
  else
  {
    $infos['INFO_DIMENSIONS'] = $original_width.'*'.$original_height;
  }
}
else
{
  $infos['INFO_DIMENSIONS'] = l10n('N/A');
}

// filesize
if (!empty($picture['current']['filesize']))
{
  $infos['INFO_FILESIZE'] =
    sprintf(l10n('%d Kb'), $picture['current']['filesize']);
}
else
{
  $infos['INFO_FILESIZE'] = l10n('N/A');
}

// number of visits
$infos['INFO_VISITS'] = $picture['current']['hit'];

// file
$infos['INFO_FILE'] = $picture['current']['file'];

// keywords
if (!empty($picture['current']['keywords']))
{
  $infos['INFO_KEYWORDS'] =
    // FIXME because of search engine partial rewrite, giving the author
    // name threw GET is not supported anymore. This feature should come
    // back later, with a better design (tag classification).
//     preg_replace(
//       '/([^,]+)/',
//       '<a href="'.
//         PHPWG_ROOT_PATH.'category.php?cat=search&amp;search=keywords:$1'
//         .'">$1</a>',
//       $picture['current']['keywords']
//       );
    $picture['current']['keywords'];
}
else
{
  $infos['INFO_KEYWORDS'] = l10n('N/A');
}

$template->assign_vars($infos);

// related categories
foreach ($related_categories as $category)
{
  $template->assign_block_vars(
    'category',
    array(
      'LINE' => count($related_categories) > 3
        ? get_cat_display_name_cache($category['uppercats'])
        : get_cat_display_name_from_id($category['category_id'])
      )
    );
}

//slideshow end
if (isset($_GET['slideshow']))
{
  if (!is_numeric($_GET['slideshow']))
  {
    $_GET['slideshow'] = $conf['slideshow_period'];
  }

  $template->assign_block_vars(
    'stop_slideshow',
    array(
      'U_SLIDESHOW' => $picture['current']['url'],
      )
    );
}

// +-----------------------------------------------------------------------+
// |                               sub pages                               |
// +-----------------------------------------------------------------------+

include(PHPWG_ROOT_PATH.'include/picture_rate.inc.php');
include(PHPWG_ROOT_PATH.'include/picture_comment.inc.php');
include(PHPWG_ROOT_PATH.'include/picture_metadata.inc.php');

//------------------------------------------------------------ log informations
pwg_log( 'picture', $title_img, $picture['current']['file'] );

$template->parse('picture');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>
