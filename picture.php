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
include(PHPWG_ROOT_PATH.'include/section_init.inc.php');
include_once(PHPWG_ROOT_PATH.'include/functions_picture.inc.php');

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
  page_not_found('The requested image does not belong to this image set',
      duplicate_index_url() );
}

// add default event handler for rendering element content
add_event_handler('render_element_content', 'default_picture_content',
  EVENT_HANDLER_PRIORITY_NEUTRAL, 2);
trigger_action('loc_begin_picture');

// this is the default handler that generates the display for the element
function default_picture_content($content, $element_info)
{
  if ( !empty($content) )
  {// someone hooked us - so we skip;
    return $content;
  }
  if (!isset($element_info['image_url']))
  { // nothing to do
    return $content;
  }
  global $user;
  $my_template = new Template(PHPWG_ROOT_PATH.'template/'.$user['template'],
    $user['theme'] );
  $my_template->set_filenames( array('default_content'=>'picture_content.tpl') );

  if (isset($element_info['high_url']))
  {
    $uuid = uniqid(rand());
    $my_template->assign_block_vars(
      'high',
      array(
        'U_HIGH' => $element_info['high_url'],
        'UUID'   => $uuid,
        )
      );
  }
  $my_template->assign_vars( array(
      'SRC_IMG' => $element_info['image_url'],
      'ALT_IMG' => $element_info['file'],
      'WIDTH_IMG' => @$element_info['scaled_width'],
      'HEIGHT_IMG' => @$element_info['scaled_height'],
      )
    );
  return $my_template->parse( 'default_content', true);
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
  // caching first & previous item : readability purpose
  $page['previous_item'] = $page['items'][ $page['current_rank'] - 1 ];
  $page['first_item'] = $page['items'][ $page['first_rank'] ];
}

if ($page['current_rank'] != $page['last_rank'])
{
  // caching next & last item : readability purpose
  $page['next_item'] = $page['items'][ $page['current_rank'] + 1 ];
  $page['last_item'] = $page['items'][ $page['last_rank'] ];
}

$url_up = duplicate_index_url(
  array(
    'start' =>
      floor($page['current_rank'] / $user['nb_image_page'])
      * $user['nb_image_page']
    ),
  array(
    'start',
    )
  );

$url_self = duplicate_picture_url();

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
      if (is_admin() and !is_adviser() and isset($page['category']))
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
      fill_caddie(array($page['image_id']));
      redirect($url_self);
      break;
    }
    case 'rate' :
    {
      include_once(PHPWG_ROOT_PATH.'include/functions_rate.inc.php');
      rate_picture($page['image_id'],
          isset($_POST['rate']) ? $_POST['rate'] : $_GET['rate'] );
      redirect($url_self);
    }
    case 'delete_comment' :
    {
      if (isset($_GET['comment_to_delete'])
          and is_numeric($_GET['comment_to_delete'])
          and is_admin() and !is_adviser() )
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
'.get_sql_condition_FandF
  (
    array
      (
        'forbidden_categories' => 'category_id',
        'visible_categories' => 'category_id'
      ),
    'AND'
  ).'
;';
$result = pwg_query($query);
$related_categories = array();
while ($row = mysql_fetch_array($result))
{
  array_push($related_categories, $row);
}
usort($related_categories, 'global_rank_compare');
//-------------------------first, prev, current, next & last picture management
$picture = array();

$ids = array($page['image_id']);
if (isset($page['previous_item']))
{
  array_push($ids, $page['previous_item']);
  array_push($ids, $page['first_item']);
}
if (isset($page['next_item']))
{
  array_push($ids, $page['next_item']);
  array_push($ids, $page['last_item']);
}

$query = '
SELECT *
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', $ids).')
;';

$result = pwg_query($query);

while ($row = mysql_fetch_assoc($result))
{
  if (isset($page['previous_item']) and $row['id'] == $page['previous_item'])
  {
    $i = 'previous';
  }
  else if (isset($page['next_item']) and $row['id'] == $page['next_item'])
  {
    $i = 'next';
  }
  else if (isset($page['first_item']) and $row['id'] == $page['first_item'])
  {
    $i = 'first';
  }
  else if (isset($page['last_item']) and $row['id'] == $page['last_item'])
  {
    $i = 'last';
  }
  else
  {
    $i = 'current';
  }

  $picture[$i] = $row;

  $picture[$i]['is_picture'] = false;
  if (in_array(get_extension($row['file']), $conf['picture_ext']))
  {
    $picture[$i]['is_picture'] = true;
  }

  // ------ build element_path and element_url
  $picture[$i]['element_path'] = get_element_path($picture[$i]);
  $picture[$i]['element_url'] = get_element_url($picture[$i]);

  // ------ build image_path and image_url
  if ($i=='current' or $i=='next')
  {
    $picture[$i]['image_path'] = get_image_path( $picture[$i] );
    $picture[$i]['image_url'] = get_image_url( $picture[$i] );
  }

  if ($i=='current')
  {
    if ( $picture[$i]['is_picture'] )
    {
      if ( $user['enabled_high']=='true' )
      {
        $hi_url=get_high_url($picture[$i]);
        if ( !empty($hi_url) )
        {
          $picture[$i]['high_url'] = $hi_url;
          $picture[$i]['download_url'] = get_download_url('h',$picture[$i]);
        }
      }
    }
    else
    { // not a pic - need download link
      $picture[$i]['download_url'] = get_download_url('e',$picture[$i]);
    }
  }

  $picture[$i]['thumbnail'] = get_thumbnail_url($row);

  if ( !empty( $row['name'] ) )
  {
    $picture[$i]['name'] = $row['name'];
  }
  else
  {
    $file_wo_ext = get_filename_wo_extension($row['file']);
    $picture[$i]['name'] = str_replace('_', ' ', $file_wo_ext);
  }

  $picture[$i]['url'] = duplicate_picture_url(
    array(
      'image_id' => $row['id'],
      'image_file' => $row['file'],
      ),
    array(
      'start',
      )
    );

  if ('previous'==$i and $page['previous_item']==$page['first_item'])
  {
    $picture['first'] = $picture[$i];
  }
  if ('next'==$i and $page['next_item']==$page['last_item'])
  {
    $picture['last'] = $picture[$i];
  }
}

// calculation of width and height for the current picture
if (empty($picture['current']['width']))
{
  $taille_image = @getimagesize($picture['current']['image_path']);
  if ($taille_image!==false)
  {
    $picture['current']['width'] = $taille_image[0];
    $picture['current']['height']= $taille_image[1];
  }
}

if (!empty($picture['current']['width']))
{
  list($picture['current']['scaled_width'],$picture['current']['scaled_height']) =
    get_picture_size(
      $picture['current']['width'],
      $picture['current']['height'],
      @$user['maxwidth'],
      @$user['maxheight']
    );
}

$url_admin =
  get_root_url().'admin.php?page=picture_modify'
  .'&amp;cat_id='.(isset($page['category']) ? $page['category'] : '')
  .'&amp;image_id='.$page['image_id']
;

$url_slide = add_url_params(
  $picture['current']['url'],
  array( 'slideshow'=>$conf['slideshow_period'] )
  );

$title =  $picture['current']['name'];
$refresh = 0;
if ( isset( $_GET['slideshow'] ) and isset($page['next_item']) )
{
  // $redirect_msg, $refresh, $url_link and $title are required for creating an automated
  // refresh page in header.tpl
  $refresh= $_GET['slideshow'];
  $url_link = add_url_params(
      $picture['next']['url'],
      array('slideshow'=>$refresh)
    );
  $redirect_msg = nl2br(l10n('redirect_msg'));
}

$title_nb = ($page['current_rank'] + 1).'/'.$page['cat_nb_images'];

// metadata
$url_metadata = duplicate_picture_url();

// do we have a plugin that can show metadata for something else than images?
$metadata_showable = trigger_event('get_element_metadata_available',
    (
      ($conf['show_exif'] or $conf['show_iptc'])
      and isset($picture['current']['image_path'])
    ),
    $picture['current']['path'] );
if ($metadata_showable)
{
  if ( !isset($_GET['metadata']) )
  {
    $url_metadata = add_url_params( $url_metadata, array('metadata'=>null) );
  }
}

$page['body_id'] = 'thePicturePage';

// maybe someone wants a special display (call it before page_header so that they
// can add stylesheets)
$element_content = trigger_event('render_element_content',
                      '', $picture['current'] );

if ( isset($picture['next']['image_url'])
      and isset($picture['next']['is_picture']) )
{
  $template->assign_block_vars( 'prefetch',
    array (
      'URL' => $picture['next']['image_url']
    )
  );
}
$template->set_filenames(array('picture'=>'picture.tpl'));

//------------------------------------------------------- navigation management
foreach ( array('first','previous','next','last') as $which_image )
{
  if (isset($picture[$which_image]))
  {
    $template->assign_block_vars(
      $which_image,
      array(
        'TITLE_IMG' => $picture[$which_image]['name'],
        'IMG' => $picture[$which_image]['thumbnail'],
        'U_IMG' => $picture[$which_image]['url'],
        )
      );
  }
}

$template->assign_vars(
  array(
    'SECTION_TITLE' => $page['title'],
    'PICTURE_TITLE' => $picture['current']['name'],
    'PHOTO' => $title_nb,
    'TITLE' => $picture['current']['name'],
    'ELEMENT_CONTENT' => $element_content,

    'LEVEL_SEPARATOR' => $conf['level_separator'],

    'U_HOME' => make_index_url(),
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

// download link
if ( isset($picture['current']['download_url']) )
{
  $template->assign_block_vars(
    'download',
    array(
      'U_DOWNLOAD' => $picture['current']['download_url']
      )
    );
}

// button to set the current picture as representative
if (is_admin() and isset($page['category']))
{
  $template->assign_block_vars(
    'representative',
    array(
      'URL' => add_url_params($url_self,
                  array('action'=>'set_as_representative')
               )
      )
    );
}

// caddie button
if (is_admin())
{
  $template->assign_block_vars(
    'caddie',
    array(
      'URL' => add_url_params($url_self,
                  array('action'=>'add_to_caddie')
               )
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
    $template->assign_block_vars(
      'favorite',
      array(
        'FAVORITE_IMG'  => get_root_url().get_themeconf('icon_dir').'/favorite.png',
        'FAVORITE_HINT' => $lang['add_favorites_hint'],
        'FAVORITE_ALT'  => $lang['add_favorites_alt'],
        'U_FAVORITE'    => add_url_params(
                              $url_self,
                              array('action'=>'add_to_favorites')
                           ),
        )
      );
  }
  else
  {
    $template->assign_block_vars(
      'favorite',
      array(
        'FAVORITE_IMG'  => get_root_url().get_themeconf('icon_dir').'/del_favorite.png',
        'FAVORITE_HINT' => $lang['del_favorites_hint'],
        'FAVORITE_ALT'  => $lang['del_favorites_alt'],
        'U_FAVORITE'    => add_url_params(
                              $url_self,
                              array('action'=>'remove_from_favorites')
                           )
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
$header_infos = array();	//for html header use
// legend
if (isset($picture['current']['comment'])
    and !empty($picture['current']['comment']))
{
  $template->assign_block_vars(
    'legend',
    array(
      'COMMENT_IMG' => nl2br($picture['current']['comment'])
      ));
  $header_infos['COMMENT'] = strip_tags($picture['current']['comment']);
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
  $header_infos['INFO_AUTHOR'] = $picture['current']['author'];
}
else
{
  $infos['INFO_AUTHOR'] = l10n('N/A');
}

// creation date
if (!empty($picture['current']['date_creation']))
{
  $val = format_date($picture['current']['date_creation']);
  $url = make_index_url(
        array(
          'chronology_field'=>'created',
          'chronology_style'=>'monthly',
          'chronology_view'=>'list',
          'chronology_date' => explode('-', $picture['current']['date_creation'])
        )
      );
  $infos['INFO_CREATION_DATE'] = '<a href="'.$url.'" rel="nofollow">'.$val.'</a>';
}
else
{
  $infos['INFO_CREATION_DATE'] = l10n('N/A');
}

// date of availability
$val = format_date($picture['current']['date_available'], 'mysql_datetime');
$url = make_index_url(
      array(
        'chronology_field'=>'posted',
        'chronology_style'=>'monthly',
        'chronology_view'=>'list',
        'chronology_date'=>explode('-', substr($picture['current']['date_available'],0,10))
      )
    );
$infos['INFO_POSTED_DATE'] = '<a href="'.$url.'" rel="nofollow">'.$val.'</a>';

// size in pixels
if ($picture['current']['is_picture'] and isset($picture['current']['width']) )
{
  if ($picture['current']['scaled_width'] !== $picture['current']['width'] )
  {
    $infos['INFO_DIMENSIONS'] =
      '<a href="'.$picture['current']['image_url'].'" title="'.
      l10n('Original dimensions').'">'.
      $picture['current']['width'].'*'.$picture['current']['height'].'</a>';
  }
  else
  {
    $infos['INFO_DIMENSIONS'] =
      $picture['current']['width'].'*'.$picture['current']['height'];
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

// tags
$query = '
SELECT id, name, url_name
  FROM '.IMAGE_TAG_TABLE.'
    INNER JOIN '.TAGS_TABLE.' ON tag_id = id
  WHERE image_id = '.$page['image_id'].'
;';
$result = pwg_query($query);

if (mysql_num_rows($result) > 0)
{
  $tags = array();
  $tag_names = array();

  while ($row = mysql_fetch_array($result))
  {
    array_push(
      $tags,
      '<a href="'
      .make_index_url(
        array(
          'tags' => array(
            array(
              'id' => $row['id'],
              'url_name' => $row['url_name'],
              ),
            )
          )
        )
      .'">'.$row['name'].'</a>'
      );
    array_push( $tag_names, $row['name'] );
  }

  $infos['INFO_TAGS'] = implode(', ', $tags);
  $header_infos['INFO_TAGS'] = implode(', ', $tag_names);
}
else
{
  $infos['INFO_TAGS'] = l10n('N/A');
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
//if ($metadata_showable and isset($_GET['metadata']))
{
  include(PHPWG_ROOT_PATH.'include/picture_metadata.inc.php');
}
//------------------------------------------------------------ log informations
pwg_log('picture', $page['title'], $picture['current']['file']);

include(PHPWG_ROOT_PATH.'include/page_header.php');
$template->parse('picture');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>
