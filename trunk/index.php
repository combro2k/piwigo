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

//--------------------------------------------------------------------- include
define('PHPWG_ROOT_PATH','./');
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );
include(PHPWG_ROOT_PATH.'include/section_init.inc.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_GUEST);

//---------------------------------------------------------------------- logout
if ( isset( $_GET['act'] )
     and $_GET['act'] == 'logout'
     and isset( $_COOKIE[session_name()] ) )
{
  // cookie deletion if exists
  $_SESSION = array();
  session_unset();
  session_destroy();
  setcookie(session_name(),'',0, ini_get('session.cookie_path') );
  redirect( make_index_url() );
}

//---------------------------------------------- change of image display order
if (isset($_GET['image_order']))
{
  setcookie(
    'pwg_image_order',
    $_GET['image_order'] > 0 ? $_GET['image_order'] : '',
    0, cookie_path()
    );

  redirect(
    duplicate_index_URL(
      array(),        // nothing to redefine
      array('start')  // changing display order goes back to section first page
      )
    );
}
//-------------------------------------------------------------- initialization
// detection of the start picture to display
if (!isset($page['start']))
{
  $page['start'] = 0;
}

// access authorization check
if (isset($page['category']))
{
  check_restrictions($page['category']);
}

if (isset($page['cat_nb_images'])
    and $page['cat_nb_images'] > $user['nb_image_page'])
{
  $page['navigation_bar'] = create_navigation_bar(
    duplicate_index_URL(array(), array('start')),
    $page['cat_nb_images'],
    $page['start'],
    $user['nb_image_page'],
    true
    );
}
else
{
  $page['navigation_bar'] = '';
}

// caddie filling :-)
if (isset($_GET['caddie']))
{
  fill_caddie($page['items']);
  // redirect();
}

//----------------------------------------------------- template initialization
//
// Start output of page
//
$title = $page['title'];
$page['body_id'] = 'theCategoryPage';
include(PHPWG_ROOT_PATH.'include/page_header.php');

$template->set_filenames( array('index'=>'index.tpl') );
//-------------------------------------------------------------- category title
$template_title = $page['title'];
if (isset($page['cat_nb_images']) and $page['cat_nb_images'] > 0)
{
  $template_title.= ' ['.$page['cat_nb_images'].']';
}

$icon_recent = get_icon(date('Y-m-d'));

if (!isset($page['chronology_field']))
{
  $chronology_params =
      array(
          'chronology_field' => 'created',
          'chronology_style' => 'monthly',
          'chronology_view' => 'list',
      );
  $template->assign_block_vars(
    'mode_created',
    array(
      'URL' => duplicate_index_URL( $chronology_params, array('start') )
      )
    );

  $chronology_params['chronology_field'] = 'posted';
  $template->assign_block_vars(
    'mode_posted',
    array(
      'URL' => duplicate_index_URL( $chronology_params, array('start') )
      )
    );
}
else
{
  $template->assign_block_vars(
    'mode_normal',
    array(
      'URL' => duplicate_index_URL( array(), array('chronology_field','start') )
      )
    );

  if ($page['chronology_field'] == 'created')
  {
    $chronology_field = 'posted';
  }
  else
  {
    $chronology_field = 'created';
  }
  $url = duplicate_index_URL(
            array('chronology_field'=>$chronology_field ),
            array('chronology_date', 'start')
          );
  $template->assign_block_vars(
    'mode_'.$chronology_field,
    array('URL' => $url )
    );
}

$template->assign_vars(
  array(
    'NB_PICTURE' => $user['nb_total_images'],
    'TITLE' => $template_title,
    'USERNAME' => $user['username'],
    'TOP_NUMBER' => $conf['top_number'],
    'MENU_CATEGORIES_CONTENT' => get_categories_menu(),

    'F_IDENTIFY' => get_root_url().'identification.php',
    'T_RECENT' => $icon_recent,

    'U_HOME' => make_index_URL(),
    'U_REGISTER' => get_root_url().'register.php',
    'U_LOST_PASSWORD' => get_root_url().'password.php',
    'U_LOGOUT' => add_url_params(make_index_URL(), array('act'=>'logout') ),
    'U_ADMIN'=> get_root_url().'admin.php',
    'U_PROFILE'=> get_root_url().'profile.php',
    )
  );

if ('search' == $page['section'])
{
  $template->assign_block_vars(
    'search_rules',
    array(
      'URL' => get_root_url().'search_rules.php?search_id='.$page['search'],
      )
    );
}
//-------------------------------------------------------------- external links
if (count($conf['links']) > 0)
{
  $template->assign_block_vars('links', array());

  foreach ($conf['links'] as $url => $label)
  {
    $template->assign_block_vars(
      'links.link',
      array(
        'URL' => $url,
        'LABEL' => $label
        )
      );
  }
}
//------------------------------------------------------------------------ tags
if ('tags' == $page['section'])
{
  $template->assign_block_vars('tags', array());

  // display tags associated to currently tagged items, less current tags
  $tags = array();

  if ( !empty($page['items']) )
  {
    $query = '
SELECT tag_id, name, url_name, count(*) counter
  FROM '.IMAGE_TAG_TABLE.'
    INNER JOIN '.TAGS_TABLE.' ON tag_id = id
  WHERE image_id IN ('.implode(',', $items).')
    AND tag_id NOT IN ('.implode(',', $page['tag_ids']).')
  GROUP BY tag_id
  ORDER BY name ASC
;';
    $result = pwg_query($query);
    while($row = mysql_fetch_array($result))
    {
      array_push($tags, $row);
    }
  }

  $tags = add_level_to_tags($tags);

  foreach ($tags as $tag)
  {
    $template->assign_block_vars(
      'tags.tag',
      array(
        'URL_ADD' => make_index_URL(
          array(
            'tags' => array_merge(
              $page['tags'],
              array(
                array(
                  'id' => $tag['tag_id'],
                  'url_name' => $tag['url_name'],
                  ),
                )
              )
            )
          ),

        'URL' => make_index_URL(
          array(
            'tags' => array(
              array(
                'id' => $tag['tag_id'],
                'url_name' => $tag['url_name'],
                ),
              )
            )
          ),

        'NAME' => $tag['name'],

        'TITLE' => l10n('See pictures linked to this tag only'),

        'TITLE_ADD' => sprintf(
          l10n('%d pictures are also linked to current tags'),
          $tag['counter']
          ),

        'CLASS' => 'tagLevel'.$tag['level']
        )
      );
  }
}
//---------------------------------------------------------- special categories
// favorites categories
if ( !$user['is_the_guest'] )
{
  $template->assign_block_vars('username', array());

  $template->assign_block_vars(
    'special_cat',
    array(
      'URL' => make_index_URL(array('section' => 'favorites')),
      'TITLE' => $lang['favorite_cat_hint'],
      'NAME' => $lang['favorite_cat']
      ));
}
// most visited
$template->assign_block_vars(
  'special_cat',
  array(
    'URL' => make_index_URL(array('section' => 'most_visited')),
    'TITLE' => $lang['most_visited_cat_hint'],
    'NAME' => $lang['most_visited_cat']
    ));
// best rated
if ($conf['rate'])
{
  $template->assign_block_vars(
    'special_cat',
    array(
      'URL' => make_index_URL(array('section' => 'best_rated')),
      'TITLE' => $lang['best_rated_cat_hint'],
      'NAME' => $lang['best_rated_cat']
      )
    );
}
// random
$template->assign_block_vars(
  'special_cat',
  array(
    'URL' => get_root_url().'random.php',
    'TITLE' => $lang['random_cat_hint'],
    'NAME' => $lang['random_cat']
    ));
// recent pics
$template->assign_block_vars(
  'special_cat',
  array(
    'URL' => make_index_URL(array('section' => 'recent_pics')),
    'TITLE' => $lang['recent_pics_cat_hint'],
    'NAME' => $lang['recent_pics_cat']
    ));
// recent cats
$template->assign_block_vars(
  'special_cat',
  array(
    'URL' => make_index_URL(array('section' => 'recent_cats')),
    'TITLE' => $lang['recent_cats_cat_hint'],
    'NAME' => $lang['recent_cats_cat']
    ));

// calendar
$template->assign_block_vars(
  'special_cat',
  array(
    'URL' =>
      make_index_URL(
        array(
          'chronology_field' => ($conf['calendar_datefield']=='date_available'
                                  ? 'posted' : 'created'),
           'chronology_style'=> 'monthly',
           'chronology_view' => 'calendar'
        )
      ),
    'TITLE' => $lang['calendar_hint'],
    'NAME' => $lang['calendar']
    )
  );
//--------------------------------------------------------------------- summary

if ($user['is_the_guest'])
{
  $template->assign_block_vars('register', array());
  $template->assign_block_vars('login', array());

  $template->assign_block_vars('quickconnect', array());
  if ($conf['authorize_remembering'])
  {
    $template->assign_block_vars('quickconnect.remember_me', array());
  }
}
else
{
  $template->assign_block_vars('hello', array());

  if (is_autorize_status(ACCESS_CLASSIC))
  {
    $template->assign_block_vars('profile', array());
  }

  // the logout link has no meaning with Apache authentication : it is not
  // possible to logout with this kind of authentication.
  if (!$conf['apache_authentication'])
  {
    $template->assign_block_vars('logout', array());
  }

  if (is_admin())
  {
    $template->assign_block_vars('admin', array());
  }
}

// tags link
$template->assign_block_vars(
  'summary',
  array(
    'TITLE' => l10n('See available tags'),
    'NAME' => l10n('Tags'),
    'U_SUMMARY'=> get_root_url().'tags.php',
    )
  );

// search link
$template->assign_block_vars(
  'summary',
  array(
    'TITLE'=>$lang['hint_search'],
    'NAME'=>$lang['search'],
    'U_SUMMARY'=> get_root_url().'search.php',
    'REL'=> 'rel="search"'
    )
  );

// comments link
$template->assign_block_vars(
  'summary',
  array(
    'TITLE'=>$lang['hint_comments'],
    'NAME'=>$lang['comments'],
    'U_SUMMARY'=> get_root_url().'comments.php',
    )
  );

// about link
$template->assign_block_vars(
  'summary',
  array(
    'TITLE'     => $lang['about_page_title'],
    'NAME'      => $lang['About'],
    'U_SUMMARY' => 'about.php?'.str_replace(
      '&',
      '&amp;',
      $_SERVER['QUERY_STRING']
      )
    )
  );

// notification
$template->assign_block_vars(
  'summary',
  array(
    'TITLE'=>l10n('notification'),
    'NAME'=>l10n('Notification'),
    'U_SUMMARY'=> get_root_url().'notification.php',
    'REL'=> 'rel="nofollow"'
    )
  );

if (isset($page['category']) and is_admin())
{
  $template->assign_block_vars(
    'edit',
    array(
      'URL' =>
        get_root_url().'admin.php?page=cat_modify'
        .'&amp;cat_id='.$page['category']
      )
    );
}

if (is_admin() and !empty($page['items']) )
{
    $template->assign_block_vars(
      'caddie',
      array(
        'URL' =>
          add_url_params(duplicate_index_url(), array('caddie'=>1) )
        )
      );
  }

//------------------------------------------------------ main part : thumbnails
if (isset($page['thumbnails_include']))
{
  include(PHPWG_ROOT_PATH.$page['thumbnails_include']);
}
//------------------------------------------------------- category informations
if (
  $page['navigation_bar'] != ''
  or (isset($page['comment']) and $page['comment'] != '')
  )
{
  $template->assign_block_vars('cat_infos',array());
}
// navigation bar
if ($page['navigation_bar'] != '')
{
  $template->assign_block_vars(
    'cat_infos.navigation',
    array(
      'NAV_BAR' => $page['navigation_bar'],
      )
    );
}

if (isset($page['cat_nb_images']) and $page['cat_nb_images'] > 0
    and $page['section'] != 'most_visited'
    and $page['section'] != 'best_rated')
{
  // image order
  $template->assign_block_vars( 'preferred_image_order', array() );

  $order_idx = isset($_COOKIE['pwg_image_order'])
    ? $_COOKIE['pwg_image_order']
    : 0
    ;

  $orders = get_category_preferred_image_orders();
  for ($i = 0; $i < count($orders); $i++)
  {
    if ($orders[$i][2])
    {
      $template->assign_block_vars(
        'preferred_image_order.order',
        array(
          'DISPLAY' => $orders[$i][0],
          'URL' => add_url_params( duplicate_index_URL(), array('image_order'=>$i) ),
          'SELECTED_OPTION' => ($order_idx==$i ? 'SELECTED' : ''),
          )
        );
    }
  }
}

if (isset($page['category']))
{
  // upload a picture in the category
  if ($page['cat_uploadable'])
  {
    $url = get_root_url().'upload.php?cat='.$page['category'];
    $template->assign_block_vars(
      'upload',
      array(
        'U_UPLOAD'=> $url
        )
      );
  }

  // category comment
  if (isset($page['comment']) and $page['comment'] != '')
  {
    $template->assign_block_vars(
      'cat_infos.comment',
      array(
        'COMMENTS' => $page['comment']
        )
      );
  }
}
//------------------------------------------------------------ log informations
pwg_log('category', $page['title']);

$template->parse('index');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>
