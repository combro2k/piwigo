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

//--------------------------------------------------------------------- include
define('PHPWG_ROOT_PATH','./');
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_GUEST);

//------------------------------------------------------------------ form check
$errors = array();
$search = array();
if (isset($_POST['submit']))
{
  if (isset($_POST['search_allwords'])
      and !preg_match('/^\s*$/', $_POST['search_allwords']))
  {
    $drop_char_match = array(
      '-','^','$',';','#','&','(',')','<','>','`','\'','"','|',',','@','_',
      '?','%','~','.','[',']','{','}',':','\\','/','=','\'','!','*');
    $drop_char_replace = array(
      ' ',' ',' ',' ',' ',' ',' ',' ',' ',' ','','',' ',' ',' ',' ','',' ',
      ' ',' ',' ',' ',' ',' ',' ',' ','' ,' ',' ',' ',' ',' ');

    // Split words
    $search['fields']['allwords'] = array(
      'words' => array_unique(
        preg_split(
          '/\s+/',
          str_replace(
            $drop_char_match,
            $drop_char_replace,
            $_POST['search_allwords']
            )
          )
        ),
      'mode' => $_POST['mode'],
      );
  }

  if ($_POST['search_author'])
  {
    $search['fields']['author'] = array(
      'words' => preg_split(
        '/\s+/',
        $_POST['search_author']
        ),
      'mode' => 'OR',
      );
  }

  if (isset($_POST['cat']))
  {
    $search['fields']['cat'] = array(
      'words'   => $_POST['cat'],
      'sub_inc' => ($_POST['subcats-included'] == 1) ? true : false,
      );
  }

  // dates
  $type_date = $_POST['date_type'];

  if (!empty($_POST['start_year']))
  {
    $search['fields'][$type_date.'-after'] = array(
      'date' => join(
        '-',
        array(
          $_POST['start_year'],
          $_POST['start_month'] != 0 ? $_POST['start_month'] : '01',
          $_POST['start_day']   != 0 ? $_POST['start_day']   : '01',
          )
        ),
      'inc' => true,
      );
  }

  if (!empty($_POST['end_year']))
  {
    $search['fields'][$type_date.'-before'] = array(
      'date' => join(
        '-',
        array(
          $_POST['end_year'],
          $_POST['end_month'] != 0 ? $_POST['end_month'] : '12',
          $_POST['end_day']   != 0 ? $_POST['end_day']   : '31',
          )
        ),
      'inc' => true,
      );
  }

  if (!empty($search))
  {
    // default search mode : each clause must be respected
    $search['mode'] = 'AND';

    // register search rules in database, then they will be available on
    // thumbnails page and picture page.
    $query ='
INSERT INTO '.SEARCH_TABLE.'
  (rules)
  VALUES
  (\''.serialize($search).'\')
;';
    pwg_query($query);

    $search_id = mysql_insert_id();
  }
  else
  {
    array_push($errors, $lang['search_one_clause_at_least']);
  }
}
//----------------------------------------------------------------- redirection
if (isset($_POST['submit']) and count($errors) == 0)
{
  redirect(
    make_index_url(
      array(
        'section' => 'search',
        'search'  => $search_id,
        )
      )
    );
}
//----------------------------------------------------- template initialization

// start date
get_day_list('start_day', @$_POST['start_day']);
get_month_list('start_month', @$_POST['start_month']);
// end date
get_day_list('end_day', @$_POST['end_day']);
get_month_list('end_month', @$_POST['end_month']);

//
// Start output of page
//
$title= $lang['search_title'];
$page['body_id'] = 'theSearchPage';
include(PHPWG_ROOT_PATH.'include/page_header.php');

$template->set_filenames( array('search'=>'search.tpl') );
$template->assign_vars(array(
  'L_SEARCH_TITLE' => $lang['search_title'],
  'L_SEARCH_OPTIONS' => $lang['search_options'],
  'L_RETURN' => $lang['home'],
  'L_SUBMIT' => $lang['submit'],
  'L_RESET' => $lang['reset'],
  'L_SEARCH_KEYWORDS'=>$lang['search_keywords'],
  'L_SEARCH_ANY_TERMS'=>$lang['search_mode_or'],
  'L_SEARCH_ALL_TERMS'=>$lang['search_mode_and'],
  'L_SEARCH_AUTHOR'=>$lang['search_author'],
  'L_SEARCH_AUTHOR_HINT'=>$lang['search_explain'],
  'L_SEARCH_CATEGORIES'=>$lang['search_categories'],
  'L_SEARCH_SUBFORUMS'=>$lang['search_subcats_included'],
  'L_YES' => $lang['yes'],
  'L_NO' => $lang['no'],
  'L_SEARCH_DATE' => $lang['search_date'],
  'L_TODAY' => $lang['today'],
  'L_SEARCH_DATE_FROM'=>$lang['search_date_from'],
  'L_SEARCH_DATE_TO'=>$lang['search_date_to'],
  'L_DAYS'=>$lang['days'],
  'L_MONTH'=>$lang['w_month'],
  'L_SEARCH_DATE_TYPE'=>$lang['search_date_type'],
  'L_RESULT_SORT'=>$lang['search_sort'],
  'L_SORT_ASCENDING'=>$lang['search_ascending'],
  'L_SORT_DESCENDING'=>$lang['search_descending'],

  'TODAY_DAY' => date('d', time()),
  'TODAY_MONTH' => date('m', time()),
  'TODAY_YEAR' => date('Y', time()),
  'S_SEARCH_ACTION' => 'search.php',
  'U_HELP' => PHPWG_ROOT_PATH.'/popuphelp.php?page=search',
  'U_HOME' => make_index_url(),
  )
);

//------------------------------------------------------------- categories form
$query = '
SELECT name,id,date_last,nb_images,global_rank,uppercats
  FROM '.CATEGORIES_TABLE;
if ($user['forbidden_categories'] != '')
{
  $query.= '
  WHERE id NOT IN ('.$user['forbidden_categories'].')';
}
$query.= '
;';

$selecteds = array();
display_select_cat_wrapper($query, $selecteds, 'category_option', false);

//-------------------------------------------------------------- errors display
if (sizeof($errors) != 0)
{
  $template->assign_block_vars('errors',array());
  foreach ($errors as $error)
  {
    $template->assign_block_vars('errors.error',array('ERROR'=>$error));
  }
}
//------------------------------------------------------------ log informations
pwg_log( 'search', $title );
$template->parse('search');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>
