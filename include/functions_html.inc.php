<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2005 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
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

function get_icon($date)
{
  global $page, $user, $conf, $lang;

  if (empty($date))
  {
    $date = 'NULL';
  }

  if (isset($page['get_icon_cache'][$date]))
  {
    return $page['get_icon_cache'][$date];
  }

  if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})/', $date, $matches))
  {
    // date can be empty, no icon to display
    $page['get_icon_cache'][$date] = '';
    return $page['get_icon_cache'][$date];
  }

  list($devnull, $year, $month, $day) = $matches;
  $unixtime = mktime( 0, 0, 0, $month, $day, $year );

  if ($unixtime === false  // PHP 5.1.0 and above
      or $unixtime === -1) // PHP prior to 5.1.0
  {
    $page['get_icon_cache'][$date] = '';
    return $page['get_icon_cache'][$date];
  }

  $diff = time() - $unixtime;
  $day_in_seconds = 24*60*60;
  $output = '';
  $title = $lang['recent_image'].'&nbsp;';
  if ( $diff < $user['recent_period'] * $day_in_seconds )
  {
    $icon_url = get_themeconf('icon_dir').'/recent.png';
    $title .= $user['recent_period'];
    $title .=  '&nbsp;'.$lang['days'];
    $size = getimagesize( $icon_url );
    $icon_url = get_root_url().$icon_url;
    $output = '<img title="'.$title.'" src="'.$icon_url.'" class="icon" style="border:0;';
    $output.= 'height:'.$size[1].'px;width:'.$size[0].'px" alt="(!)" />';
  }

  $page['get_icon_cache'][$date] = $output;

  return $page['get_icon_cache'][$date];
}

function create_navigation_bar(
  $url, $nb_element, $start, $nb_element_page, $clean_url = false
  )
{
  global $lang, $conf;

  $pages_around = $conf['paginate_pages_around'];
  $start_str = $clean_url ? '/start-' : '&amp;start=';

  $navbar = '';

  // current page detection
  if (!isset($start)
      or !is_numeric($start)
      or (is_numeric($start) and $start < 0))
  {
    $start = 0;
  }

  // navigation bar useful only if more than one page to display !
  if ($nb_element > $nb_element_page)
  {
    // current page and last page
    $cur_page = ceil($start / $nb_element_page) + 1;
    $maximum = ceil($nb_element / $nb_element_page);

    // link to first page ?
    if ($cur_page != 1)
    {
      $navbar.=
        '<a href="'.$url.'" rel="start">'
        .$lang['first_page']
        .'</a>';
    }
    else
    {
      $navbar.= $lang['first_page'];
    }
    $navbar.= ' | ';
    // link on previous page ?
    if ($start != 0)
    {
      $previous = $start - $nb_element_page;

      $navbar.=
        '<a href="'
        .$url.($previous > 0 ? $start_str.$previous : '')
        .'" rel="prev">'
        .$lang['previous_page']
        .'</a>';
    }
    else
    {
      $navbar.= $lang['previous_page'];
    }
    $navbar.= ' |';

    if ($cur_page > $pages_around + 1)
    {
      $navbar.= '&nbsp;<a href="'.$url.'">1</a>';

      if ($cur_page > $pages_around + 2)
      {
        $navbar.= ' ...';
      }
    }

    // inspired from punbb source code
    for ($i = $cur_page - $pages_around, $stop = $cur_page + $pages_around + 1;
         $i < $stop;
         $i++)
    {
      if ($i < 1 or $i > $maximum)
      {
        continue;
      }
      else if ($i != $cur_page)
      {
        $temp_start = ($i - 1) * $nb_element_page;

        $navbar.=
          '&nbsp;'
          .'<a href="'.$url
          .($temp_start > 0 ? $start_str.$temp_start : '')
          .'">'
          .$i
          .'</a>';
      }
      else
      {
        $navbar.=
          '&nbsp;'
          .'<span class="pageNumberSelected">'
          .$i
          .'</span>';
      }
    }

    if ($cur_page < ($maximum - $pages_around))
    {
      $temp_start = ($maximum - 1) * $nb_element_page;

      if ($cur_page < ($maximum - $pages_around - 1))
      {
        $navbar.= ' ...';
      }

      $navbar.= ' <a href="'.$url.$start_str.$temp_start.'">'.$maximum.'</a>';
    }

    $navbar.= ' | ';
    // link on next page ?
    if ($nb_element > $nb_element_page
        and $start + $nb_element_page < $nb_element)
    {
      $next = $start + $nb_element_page;

      $navbar.=
        '<a href="'.$url.$start_str.$next.'" rel="next">'
        .$lang['next_page']
        .'</a>';
    }
    else
    {
      $navbar.= $lang['next_page'];
    }

    $navbar.= ' | ';
    // link to last page ?
    if ($cur_page != $maximum)
    {
      $temp_start = ($maximum - 1) * $nb_element_page;

      $navbar.=
        '<a href="'.$url.$start_str.$temp_start.'" rel="last">'
        .$lang['last_page']
        .'</a>';
    }
    else
    {
      $navbar.= $lang['last_page'];
    }
  }
  return $navbar;
}

//
// Pick a language, any language ...
//
function language_select($default, $select_name = "language")
{
  $available_lang = get_languages();

  $lang_select = '<select name="' . $select_name . '">';
  foreach ($available_lang as $code => $displayname)
  {
    $selected = ( strtolower($default) == strtolower($code) ) ? ' selected="selected"' : '';
    $lang_select .= '<option value="' . $code . '"' . $selected . '>' . ucwords($displayname) . '</option>';
  }
  $lang_select .= '</select>';

  return $lang_select;
}

/**
 * returns the list of categories as a HTML string
 *
 * categories string returned contains categories as given in the input
 * array $cat_informations. $cat_informations array must be an association
 * of {category_id => category_name}. If url input parameter is null,
 * returns only the categories name without links.
 *
 * @param array cat_informations
 * @param string url
 * @param boolean replace_space
 * @return string
 */
function get_cat_display_name($cat_informations,
                              $url = '',
                              $replace_space = true)
{
  global $conf;

  $output = '';
  $is_first = true;
  foreach ($cat_informations as $id => $name)
  {
    if ($is_first)
    {
      $is_first = false;
    }
    else
    {
      $output.= $conf['level_separator'];
    }

    if ( !isset($url) )
    {
      $output.= $name;
    }
    elseif ($url == '')
    {
      $output.= '<a class=""';
      $output.= ' href="'.make_index_url( array('category'=>$id) ).'">';
      $output.= $name.'</a>';
    }
    else
    {
      $output.= '<a class=""';
      $output.= ' href="'.PHPWG_ROOT_PATH.$url.$id.'">';
      $output.= $name.'</a>';
    }
  }
  if ($replace_space)
  {
    return replace_space($output);
  }
  else
  {
    return $output;
  }
}

/**
 * returns the list of categories as a HTML string, with cache of names
 *
 * categories string returned contains categories as given in the input
 * array $cat_informations. $uppercats is the list of category ids to
 * display in the right order. If url input parameter is empty, returns only
 * the categories name without links.
 *
 * @param string uppercats
 * @param string url
 * @param boolean replace_space
 * @return string
 */
function get_cat_display_name_cache($uppercats,
                                    $url = '',
                                    $replace_space = true)
{
  global $cat_names, $conf;

  if (!isset($cat_names))
  {
    $query = '
SELECT id,name
  FROM '.CATEGORIES_TABLE.'
;';
    $result = pwg_query($query);
    while ($row = mysql_fetch_array($result))
    {
      $cat_names[$row['id']] = $row['name'];
    }
  }

  $output = '';
  $is_first = true;
  foreach (explode(',', $uppercats) as $category_id)
  {
    $name = $cat_names[$category_id];

    if ($is_first)
    {
      $is_first = false;
    }
    else
    {
      $output.= $conf['level_separator'];
    }

    if ( !isset($url) )
    {
      $output.= $name;
    }
    elseif ($url == '')
    {
      $output.= '
<a class=""
   href="'.make_index_url( array('category'=>$category_id) ).'">'.$name.'</a>';
    }
    else
    {
      $output.= '
<a class=""
   href="'.PHPWG_ROOT_PATH.$url.$category_id.'">'.$name.'</a>';
    }
  }
  if ($replace_space)
  {
    return replace_space($output);
  }
  else
  {
    return $output;
  }
}

/**
 * returns the HTML code for a category item in the menu (for the main page)
 *
 * HTML code generated uses logical list tags ul and each category is an
 * item li. The paramter given is the category informations as an array,
 * used keys are : id, name, nb_images, date_last
 *
 * @param array categories
 * @return string
 */
function get_html_menu_category($categories)
{
  global $page, $lang;

  $ref_level = 0;
  $level = 0;
  $menu = '';

  // $page_cat value remains 0 for special sections
  $page_cat = 0;
  if (isset($page['category']))
  {
    $page_cat = $page['category'];
  }

  foreach ($categories as $category)
  {
    $level = substr_count($category['global_rank'], '.') + 1;
    if ($level > $ref_level)
    {
      $menu.= "\n<ul>";
    }
    else if ($level == $ref_level)
    {
      $menu.= "\n</li>";
    }
    else if ($level < $ref_level)
    {
      // we may have to close more than one level at the same time...
      $menu.= "\n</li>";
      $menu.= str_repeat("\n</ul></li>",($ref_level-$level));
    }
    $ref_level = $level;

    $menu.= "\n\n".'<li';
    if ($category['id'] == $page_cat)
    {
      $menu.= ' class="selected"';
    }
    $menu.= '>';

    $url = make_index_url(array('category' => $category['id']));

    $menu.= "\n".'<a href="'.$url.'"';
    if ($page_cat != 0
        and $category['id'] == $page['cat_id_uppercat'])
    {
      $menu.= ' rel="up"';
    }
    $menu.= '>'.$category['name'].'</a>';

    if ($category['nb_images'] > 0)
    {
      $menu.= "\n".'<span class="menuInfoCat"';
      $menu.= ' title="'.$category['nb_images'];
      $menu.= ' '.$lang['images_available'].'">';
      $menu.= '['.$category['nb_images'].']';
      $menu.= '</span>';
      $menu.= get_icon($category['date_last']);
    }
  }

  $menu.= str_repeat("\n</li></ul>",($level));

  return $menu;
}

/**
 * returns HTMLized comment contents retrieved from database
 *
 * newlines becomes br tags, _word_ becomes underline, /word/ becomes
 * italic, *word* becomes bolded
 *
 * @param string content
 * @return string
 */
function parse_comment_content($content)
{
  $pattern = '/(https?:\/\/\S*)/';
  $replacement = '<a href="$1" rel="nofollow">$1</a>';
  $content = preg_replace($pattern, $replacement, $content);

  $content = nl2br($content);

  // replace _word_ by an underlined word
  $pattern = '/\b_(\S*)_\b/';
  $replacement = '<span style="text-decoration:underline;">$1</span>';
  $content = preg_replace($pattern, $replacement, $content);

  // replace *word* by a bolded word
  $pattern = '/\b\*(\S*)\*\b/';
  $replacement = '<span style="font-weight:bold;">$1</span>';
  $content = preg_replace($pattern, $replacement, $content);

  // replace /word/ by an italic word
  $pattern = "/\/(\S*)\/(\s)/";
  $replacement = '<span style="font-style:italic;">$1$2</span>';
  $content = preg_replace($pattern, $replacement, $content);

  $content = '<div>'.$content.'</div>';
  return $content;
}

function get_cat_display_name_from_id($cat_id,
                                      $url = '',
                                      $replace_space = true)
{
  $cat_info = get_cat_info($cat_id);
  return get_cat_display_name($cat_info['name'], $url, $replace_space);
}

/**
 * exits the current script (either exit or redirect)
 */
function access_denied()
{
  global $user, $lang;

  $login_url =
      get_root_url().'identification.php?redirect='
      .urlencode(urlencode($_SERVER['REQUEST_URI']));

  if ( isset($user['is_the_guest']) and !$user['is_the_guest'] )
  {
    echo '<div style="text-align:center;">'.$lang['access_forbiden'].'<br />';
    echo '<a href="'.get_root_url().'identification.php">'.$lang['identification'].'</a>&nbsp;';
    echo '<a href="'.make_index_url().'">'.$lang['home'].'</a></div>';
    exit();
  }
  else
  {
    header('HTTP/1.1 401 Authorization required');
    header('Status: 401 Authorization required');
    redirect($login_url);
  }
}
?>