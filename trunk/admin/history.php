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

/**
 * Display filtered history lines
 */

// echo '<pre>$_POST:
// '; print_r($_POST); echo '</pre>';
// echo '<pre>$_GET:
// '; print_r($_GET); echo '</pre>';

// +-----------------------------------------------------------------------+
// |                              functions                                |
// +-----------------------------------------------------------------------+

// +-----------------------------------------------------------------------+
// |                           initialization                              |
// +-----------------------------------------------------------------------+

if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions_history.inc.php');

if (isset($_GET['start']) and is_numeric($_GET['start']))
{
  $page['start'] = $_GET['start'];
}
else
{
  $page['start'] = 0;
}

$types = array('none', 'picture', 'high', 'other');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+

check_status(ACCESS_ADMINISTRATOR);

// +-----------------------------------------------------------------------+
// | Build search criteria and redirect to results                         |
// +-----------------------------------------------------------------------+

$errors = array();
$search = array();

if (isset($_POST['submit']))
{
  // dates
  if (!empty($_POST['start_year']))
  {
    $search['fields']['date-after'] = sprintf(
      '%d-%02d-%02d',
      $_POST['start_year'],
      $_POST['start_month'],
      $_POST['start_day']
      );
  }

  if (!empty($_POST['end_year']))
  {
    $search['fields']['date-before'] = sprintf(
      '%d-%02d-%02d',
      $_POST['end_year'],
      $_POST['end_month'],
      $_POST['end_day']
      );
  }

  $search['fields']['types'] = $_POST['types'];

  $search['fields']['user'] = $_POST['user'];
  
  // echo '<pre>'; print_r($search); echo '</pre>';
  
  if (!empty($search))
  {
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
    
    redirect(
      PHPWG_ROOT_PATH.'admin.php?page=history&search_id='.$search_id
      );
  }
  else
  {
    array_push($errors, $lang['search_one_clause_at_least']);
  }
}

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

$template->set_filename('history', 'admin/history.tpl');

// TabSheet initialization
history_tabsheet();

$base_url = PHPWG_ROOT_PATH.'admin.php?page=history';

$template->assign_vars(
  array(
    'U_HELP' => PHPWG_ROOT_PATH.'popuphelp.php?page=history',

    'F_ACTION' => PHPWG_ROOT_PATH.'admin.php?page=history'
    )
  );

$template->assign_vars(
  array(
    'TODAY_DAY'   => date('d', time()),
    'TODAY_MONTH' => date('m', time()),
    'TODAY_YEAR'  => date('Y', time()),
    )
  );

// +-----------------------------------------------------------------------+
// |                             history lines                             |
// +-----------------------------------------------------------------------+

if (isset($_GET['search_id'])
    and $page['search_id'] = (int)$_GET['search_id'])
{
  // what are the lines to display in reality ?
  $query = '
SELECT rules
  FROM '.SEARCH_TABLE.'
  WHERE id = '.$page['search_id'].'
;';
  list($serialized_rules) = mysql_fetch_row(pwg_query($query));

  $page['search'] = unserialize($serialized_rules);

  if (isset($_GET['user_id']))
  {
    if (!is_numeric($_GET['user_id']))
    {
      die('user_id GET parameter must be an integer value');
    }

    $page['search']['fields']['user'] = $_GET['user_id'];
    
    $query ='
INSERT INTO '.SEARCH_TABLE.'
  (rules)
  VALUES
  (\''.serialize($page['search']).'\')
;';
    pwg_query($query);

    $search_id = mysql_insert_id();
    
    redirect(
      PHPWG_ROOT_PATH.'admin.php?page=history&search_id='.$search_id
      );
  }

  // echo '<pre>'; print_r($page['search']); echo '</pre>';
  
  $clauses = array();

  if (isset($page['search']['fields']['date-after']))
  {
    array_push(
      $clauses,
      "date >= '".$page['search']['fields']['date-after']."'"
      );
  }

  if (isset($page['search']['fields']['date-before']))
  {
    array_push(
      $clauses,
      "date <= '".$page['search']['fields']['date-before']."'"
      );
  }

  if (isset($page['search']['fields']['types']))
  {
    $local_clauses = array();
    
    foreach ($types as $type) {
      if (in_array($type, $page['search']['fields']['types'])) {
        $clause = 'image_type ';
        if ($type == 'none')
        {
          $clause.= 'IS NULL';
        }
        else
        {
          $clause.= "= '".$type."'";
        }
        
        array_push($local_clauses, $clause);
      }
    }
    
    if (count($local_clauses) > 0)
    {
      array_push(
        $clauses,
        implode(' OR ', $local_clauses)
        );
    }
  }

  if (isset($page['search']['fields']['user'])
      and $page['search']['fields']['user'] != -1)
  {
    array_push(
      $clauses,
      'user_id = '.$page['search']['fields']['user']
      );
  }
  
  $clauses = prepend_append_array_items($clauses, '(', ')');

  $where_separator =
    implode(
      "\n    AND ",
      $clauses
      );
  
  $query = '
SELECT
    date,
    time,
    user_id,
    IP,
    section,
    category_id,
    tag_ids,
    image_id,
    image_type
  FROM '.HISTORY_TABLE.'
  WHERE '.$where_separator.'
;';

  // LIMIT '.$page['start'].', '.$conf['nb_logs_page'].'

  $result = pwg_query($query);

  $page['nb_lines'] = mysql_num_rows($result);
  
  $history_lines = array();
  $user_ids = array();
  $category_ids = array();
  $image_ids = array();
  $tag_ids = array();
  
  while ($row = mysql_fetch_assoc($result))
  {
    $user_ids[$row['user_id']] = 1;

    if (isset($row['category_id']))
    {
      $category_ids[$row['category_id']] = 1;
    }

    if (isset($row['image_id']))
    {
      $image_ids[$row['image_id']] = 1;
    }

    array_push(
      $history_lines,
      $row
      );
  }

  // prepare reference data (users, tags, categories...)
  if (count($user_ids) > 0)
  {
    $query = '
SELECT '.$conf['user_fields']['id'].' AS id
     , '.$conf['user_fields']['username'].' AS username
  FROM '.USERS_TABLE.'
  WHERE id IN ('.implode(',', array_keys($user_ids)).')
;';
    $result = pwg_query($query);

    $username_of = array();
    while ($row = mysql_fetch_array($result))
    {
      $username_of[$row['id']] = $row['username'];
    }
  }

  if (count($category_ids) > 0)
  {
    $query = '
SELECT id, uppercats
  FROM '.CATEGORIES_TABLE.'
  WHERE id IN ('.implode(',', array_keys($category_ids)).')
;';
    $uppercats_of = simple_hash_from_query($query, 'id', 'uppercats');

    $name_of_category = array();
    
    foreach ($uppercats_of as $category_id => $uppercats)
    {
      $name_of_category[$category_id] = get_cat_display_name_cache(
        $uppercats
        );
    }
  }

  if (count($image_ids) > 0)
  {
    $query = '
SELECT
    id,
    IF(name IS NULL, file, name) AS label,
    filesize,
    high_filesize
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', array_keys($image_ids)).')
;';
    // $label_of_image = simple_hash_from_query($query, 'id', 'label');
    $label_of_image = array();
    $filesize_of_image = array();
    $high_filesize_of_image = array();
    
    $result = pwg_query($query);
    while ($row = mysql_fetch_array($result))
    {
      $label_of_image[ $row['id'] ] = $row['label'];

      if (isset($row['filesize']))
      {
        $filesize_of_image[ $row['id'] ] = $row['filesize'];
      }

      if (isset($row['high_filesize']))
      {
        $high_filesize_of_image[ $row['id'] ] = $row['high_filesize'];
      }
    }

    // echo '<pre>'; print_r($high_filesize_of_image); echo '</pre>';
  }
  
  $i = 0;
  $first_line = $page['start'] + 1;
  $last_line = $page['start'] + $conf['nb_logs_page'];

  $total_filesize = 0;

  foreach ($history_lines as $line)
  {
    if (isset($line['image_type']))
    {
      if ($line['image_type'] == 'high')
      {
        if (isset($high_filesize_of_image[$line['image_id']]))
        {
          $total_filesize+= $high_filesize_of_image[$line['image_id']];
        }
      }
      else
      {
        if (isset($filesize_of_image[$line['image_id']]))
        {
          $total_filesize+= $filesize_of_image[$line['image_id']];
        }
      }
    }
    
    $i++;
    
    if ($i < $first_line or $i > $last_line)
    {
      continue;
    }

    $user_string = '';
    if (isset($username_of[$line['user_id']]))
    {
      $user_string.= $username_of[$line['user_id']];
    }
    else
    {
      $user_string.= $line['user_id'];
    }
    $user_string.= '&nbsp;<a href="';
    $user_string.= PHPWG_ROOT_PATH.'admin.php?page=history';
    $user_string.= '&amp;search_id='.$page['search_id'];
    $user_string.= '&amp;user_id='.$line['user_id'];
    $user_string.= '">+</a>';
    
    $template->assign_block_vars(
      'detail',
      array(
        'DATE'      => $line['date'],
        'TIME'      => $line['time'],
        'USER'      => $user_string,
        'IP'        => $line['IP'],
        'IMAGE'     => isset($line['image_id'])
          ? ( isset($label_of_image[$line['image_id']])
                ? sprintf(
                    '(%u) %s',
                    $line['image_id'],
                    $label_of_image[$line['image_id']]
                  )
                : sprintf(
                    '(%u) deleted ',
                    $line['image_id']
                  )
            )
          : '',
        'TYPE'      => $line['image_type'],
        'SECTION'   => $line['section'],
        'CATEGORY'  => isset($line['category_id'])
          ? ( isset($name_of_category[$line['category_id']])
                ? $name_of_category[$line['category_id']]
                : 'deleted '.$line['category_id'] )
          : '',
        'TAGS'       => $line['tag_ids'],
        'T_CLASS'   => ($i % 2) ? 'row1' : 'row2',
        )
      );
  }

  $template->assign_block_vars(
    'summary',
    array(
      'FILESIZE' => $total_filesize.' KB',
      )
    );
}

// $groups_string = preg_replace(
//     '/(\d+)/e',
//     "\$groups['$1']",
//     implode(
//       ', ',
//       $local_user['groups']
//       )
//     );

// +-----------------------------------------------------------------------+
// |                            navigation bar                             |
// +-----------------------------------------------------------------------+

if (isset($page['search_id']))
{
  $navbar = create_navigation_bar(
    PHPWG_ROOT_PATH.'admin.php'.get_query_string_diff(array('start')),
    $page['nb_lines'],
    $page['start'],
    $conf['nb_logs_page']
    );

  $template->assign_block_vars(
    'navigation',
    array(
      'NAVBAR' => $navbar
      )
    );
}

// +-----------------------------------------------------------------------+
// |                             filter form                               |
// +-----------------------------------------------------------------------+

$form = array();

if (isset($page['search']))
{
  if (isset($page['search']['fields']['date-after']))
  {
    $tokens = explode('-', $page['search']['fields']['date-after']);
    
    $form['start_year']  = (int)$tokens[0];
    $form['start_month'] = (int)$tokens[1];
    $form['start_day']   = (int)$tokens[2];
  }

  if (isset($page['search']['fields']['date-before']))
  {
    $tokens = explode('-', $page['search']['fields']['date-before']);

    $form['end_year']  = (int)$tokens[0];
    $form['end_month'] = (int)$tokens[1];
    $form['end_day']   = (int)$tokens[2];
  }

  $form['types'] = $page['search']['fields']['types'];

  if (isset($page['search']['fields']['user']))
  {
    $form['user'] = $page['search']['fields']['user'];
  }
  else
  {
    $form['user'] = null;
  }
}
else
{
  // by default, at page load, we want the selected date to be the current
  // date
  $form['start_year']  = $form['end_year']  = date('Y');
  $form['start_month'] = $form['end_month'] = date('n');
  $form['start_day']   = $form['end_day']   = date('j');
  $form['types'] = $types;
}

// start date
get_day_list('start_day', @$form['start_day']);
get_month_list('start_month', @$form['start_month']);
// end date
get_day_list('end_day', @$form['end_day']);
get_month_list('end_month', @$form['end_month']);

$template->assign_vars(
  array(
    'START_YEAR' => @$form['start_year'],
    'END_YEAR'   => @$form['end_year'],
    )
  );

foreach ($types as $option)
{
  $selected = '';
  
  if (in_array($option, $form['types']))
  {
    $selected = 'selected="selected"';
  }
  
  $template->assign_block_vars(
    'types_option',
    array(
      'VALUE' => $option,
      'CONTENT' => l10n($option),
      'SELECTED' => $selected,
      )
    );
}

$template->assign_block_vars(
  'user_option',
  array(
    'VALUE'=> -1,
    'CONTENT' => '------------',
    'SELECTED' => ''
    )
  );

$query = '
SELECT
    '.$conf['user_fields']['id'].' AS id,
    '.$conf['user_fields']['username'].' AS username
  FROM '.USERS_TABLE.'
  ORDER BY username ASC
;';
$result = pwg_query($query);

while ($row = mysql_fetch_array($result))
{
  $selected = '';

  if ($row['id'] == $form['user'])
  {
    $selected = 'selected="selected"';
  }
  
  $template->assign_block_vars(
    'user_option',
    array(
      'VALUE' => $row['id'],
      'CONTENT' => $row['username'],
      'SELECTED' => $selected,
      )
    );
}
  
// +-----------------------------------------------------------------------+
// |                           html code display                           |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'history');
?>
