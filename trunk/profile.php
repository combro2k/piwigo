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

// customize appearance of the site for a user
// +-----------------------------------------------------------------------+
// |                           initialization                              |
// +-----------------------------------------------------------------------+
$userdata = array();
if (defined('IN_ADMIN') and isset($_POST['submituser']))
{
  $userdata = getuserdata($_POST['username']);
}
else if (defined('IN_ADMIN') and IN_ADMIN and isset($_GET['user_id']))
{
  $userdata = getuserdata(intval($_GET['user_id']));
}
elseif (defined('IN_ADMIN') and isset($_POST['submit']))
{
  $userdata = getuserdata(intval($_POST['userid']));
}
elseif (!defined('IN_ADMIN') or !IN_ADMIN)
{
  define('PHPWG_ROOT_PATH','./');
  include_once(PHPWG_ROOT_PATH.'include/common.inc.php');
  check_login_authorization(false);
  $userdata = $user;
}
//------------------------------------------------------ update & customization
$infos = array('nb_image_line', 'nb_line_page', 'language',
               'maxwidth', 'maxheight', 'expand', 'show_nb_comments',
               'recent_period', 'template', 'mail_address');

$errors = array();
if (isset($_POST['submit']))
{
  $int_pattern = '/^\d+$/';
  
  if ($_POST['maxwidth'] != ''
      and (!preg_match($int_pattern, $_POST['maxwidth'])
           or $_POST['maxwidth'] < 50))
  {
    array_push($errors, $lang['maxwidth_error']);
  }
  if ($_POST['maxheight']
       and (!preg_match($int_pattern, $_POST['maxheight'])
             or $_POST['maxheight'] < 50))
  {
    array_push($errors, $lang['maxheight_error']);
  }
  // periods must be integer values, they represents number of days
  if (!preg_match($int_pattern, $_POST['recent_period'])
      or $_POST['recent_period'] <= 0)
  {
    array_push($errors, $lang['periods_error']);
  }

  // if mail_address has changed
  if (!isset($userdata['mail_address']))
  {
    $userdata['mail_address'] = '';
  }
  
  if ($_POST['mail_address'] != @$userdata['mail_address'])
  {
    if ($user['status'] == 'admin')
    {
      $mail_error = validate_mail_address($_POST['mail_address']);
      if (!empty($mail_error))
      {
        array_push($errors, $mail_error);
      }
    }
    else if (!empty($_POST['password']))
    {
      array_push($errors, $lang['reg_err_pass']);
    }
    else
    {
      // retrieving the encrypted password of the login submitted
      $query = '
SELECT password
  FROM '.USERS_TABLE.'
  WHERE id = \''.$userdata['id'].'\'
;';
      $row = mysql_fetch_array(pwg_query($query));
      if ($row['password'] == md5($_POST['password']))
      {
        $mail_error = validate_mail_address($_POST['mail_address']);
        if (!empty($mail_error))
        {
          array_push($errors, $mail_error);
        }
      }
      else
      {
        array_push($errors, $lang['reg_err_pass']);
      }
    }
  }
  
  // password must be the same as its confirmation
  if (!empty($_POST['use_new_pwd'])
      and $_POST['use_new_pwd'] != $_POST['passwordConf'])
  {
    array_push($errors, $lang['reg_err_pass']);
  }
  
  // We check if we are in the admin level
  if (isset($_POST['user_delete']))
  {
    if ($_POST['userid'] > 2) // gallery founder + guest
    {
      delete_user($_POST['userid']);
    }
    else
    {
      array_push($errors, $lang['user_err_modify']);
    }
  }
	
  // We check if we are in the admin level
  if (isset($_POST['status']) and $_POST['status'] <> $userdata['status'])
  {
    if ($_POST['userid'] > 2) // gallery founder + guest
    {
      array_push($infos, 'status');
    }
    else
    {
      array_push($errors, $lang['user_err_modify']);
    }
  }
  
  if (count($errors) == 0)
  {
    $query = '
UPDATE '.USERS_TABLE.'
  SET ';
    $is_first = true;
    foreach ($infos as $i => $info)
    {
      if (!$is_first)
      {
        $query.= '
    , ';
      }
      $is_first = false;
      
      $query.= $info;
      $query.= ' = ';
      if ($_POST[$info] == '')
      {
        $query.= 'NULL';
      }
      else
      {
        $query.= "'".$_POST[$info]."'";
      }
    }
    $query.= '
  WHERE id = '.$_POST['userid'].'
;';
    pwg_query($query);

    if (!empty($_POST['use_new_pwd']))
    {
      $query = '
UPDATE '.USERS_TABLE.'
  SET password = \''.md5($_POST['use_new_pwd']).'\'
  WHERE id = '.$_POST['userid'].'
;';
      pwg_query($query);
    }
    
    // redirection
    if (!defined('IN_ADMIN') or !IN_ADMIN)
    {
      $url = PHPWG_ROOT_PATH.'category.php?'.$_SERVER['QUERY_STRING'];
      redirect(add_session_id($url));
    }
    else
    {
      redirect(add_session_id(PHPWG_ROOT_PATH.'admin.php?page=profile'));
    }
  }
}
else if (defined('IN_ADMIN') and IN_ADMIN and isset($_POST['submit_add']))
{
  $errors = register_user($_POST['login'], $_POST['password'],
                          $_POST['password'], '');
}
// +-----------------------------------------------------------------------+
// |                       page header and options                         |
// +-----------------------------------------------------------------------+
$url_action = PHPWG_ROOT_PATH;
if (!defined('IN_ADMIN'))
{
  $title= $lang['customize_page_title'];
  include(PHPWG_ROOT_PATH.'include/page_header.php');
  $url_action .='profile.php';
}
else
{
  $url_action .='admin.php?page=profile';
}
//----------------------------------------------------- template initialization
$template->set_filenames(array('profile_body'=>'profile.tpl'));

if (defined('IN_ADMIN') and IN_ADMIN and empty($userdata))
{
  $admin_profile = add_session_id(PHPWG_ROOT_PATH.'admin.php?page=profile');
  
  $template->assign_block_vars('add_user', array('F_ACTION'=>$admin_profile));
  $template->assign_block_vars('select_user',array());

  $conf['users_page'] = 20;
  $start = isset($_GET['start']) ? $_GET['start'] : 0;

  $query = '
SELECT COUNT(*) AS counter
  FROM '.USERS_TABLE.'
  WHERE id != 2
;';
  list($counter) = mysql_fetch_row(pwg_query($query));
  $url = PHPWG_ROOT_PATH.'admin.php'.get_query_string_diff(array('start'));
  $navbar = create_navigation_bar($url,
                                  $counter,
                                  $start,
                                  $conf['users_page'],
                                  '');
  
  $template->assign_vars(
    array(
      'L_SELECT_USERNAME'=>$lang['Select_username'],
      'L_LOOKUP_USER'=>$lang['Look_up_user'],
      'L_FIND_USERNAME'=>$lang['Find_username'],
      'L_AUTH_USER'=>$lang['permuser_only_private'],
      'L_GROUP_ADD_USER' => $lang['group_add_user'],
      'L_SUBMIT'=>$lang['submit'],
      'L_STATUS'=>$lang['user_status'],
      'L_USERNAME' => $lang['login'],
      'L_PASSWORD' => $lang['password'],
      'L_EMAIL' => $lang['mail_address'],
      'L_ORDER_BY' => $lang['order_by'],
      'L_ACTIONS' => $lang['actions'],
      'L_PERMISSIONS' => $lang['permissions'],
      'L_USERS_LIST' => $lang['title_liste_users'],

      'NAVBAR'=>$navbar,
      'F_SEARCH_USER_ACTION' => $admin_profile,
      'F_ORDER_ACTION' => $admin_profile,
      'U_SEARCH_USER' => add_session_id(PHPWG_ROOT_PATH.'admin/search.php')
      ));

  $order_by_items = array('id' => $lang['registration_date'],
                          'username' => $lang['login']);
  foreach ($order_by_items as $item => $label)
  {
    $selected = (isset($_GET['order_by']) and $_GET['order_by'] == $item) ?
      'selected="selected"' : '';
    $template->assign_block_vars(
      'select_user.order_by',
      array(
        'VALUE' => $item,
        'CONTENT' => $label,
        'SELECTED' => $selected
        ));
  }

  $direction_items = array('asc' => $lang['ascending'],
                           'desc' => $lang['descending']);
  foreach ($direction_items as $item => $label)
  {
    $selected = (isset($_GET['direction']) and $_GET['direction'] == $item) ?
      'selected="selected"' : '';
    $template->assign_block_vars(
      'select_user.direction',
      array(
        'VALUE' => $item,
        'CONTENT' => $label,
        'SELECTED' => $selected
        ));
  }

  $profile_url = PHPWG_ROOT_PATH.'admin.php?page=profile&amp;user_id=';
  $perm_url = PHPWG_ROOT_PATH.'admin.php?page=user_perm&amp;user_id=';

  $users = array();
  $user_ids = array();
  $groups_content = array();

  $order_by = 'id';
  if (isset($_GET['order_by'])
      and in_array($_GET['order_by'], array_keys($order_by_items)))
  {
    $order_by = $_GET['order_by'];
  }

  $direction = 'ASC';
  if (isset($_GET['direction'])
      and in_array($_GET['direction'], array_keys($direction_items)))
  {
    $direction = strtoupper($_GET['direction']);
  }
  
  $query = '
SELECT id, username, mail_address, status
  FROM '.USERS_TABLE.'
  WHERE id != 2
  ORDER BY '.$order_by.' '.$direction.'
  LIMIT '.$start.', '.$conf['users_page'].'
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    array_push($users, $row);
    array_push($user_ids, $row['id']);
    $user_groups[$row['id']] = array();
  }

  $query = '
SELECT user_id, group_id, name
  FROM '.USER_GROUP_TABLE.' INNER JOIN '.GROUPS_TABLE.' ON group_id = id
  WHERE user_id IN ('.implode(',', $user_ids).')
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    $groups_content[$row['group_id']] = $row['name'];
    array_push($user_groups[$row['user_id']], $row['group_id']);
  }

  foreach ($users as $item)
  {
    $groups = preg_replace('/(\d+)/e',
                           "\$groups_content['$1']",
                           implode(', ', $user_groups[$item['id']]));
    
    $template->assign_block_vars(
      'select_user.user',
      array(
        'U_MOD'=>add_session_id($profile_url.$item['id']),
        'U_PERM'=>add_session_id($perm_url.$item['id']),
        'USERNAME'=>$item['username'],
        'STATUS'=>$lang['user_status_'.$item['status']],
        'EMAIL'=>isset($item['mail_address']) ? $item['mail_address'] : '',
        'GROUPS'=>$groups
        ));
  }
}
else
{
  $expand =
    ($userdata['expand']=='true')?
    'EXPAND_TREE_YES':'EXPAND_TREE_NO';
  
  $nb_comments =
    ($userdata['show_nb_comments']=='true')?
    'NB_COMMENTS_YES':'NB_COMMENTS_NO';
  
  $template->assign_block_vars('modify',array());
  $template->assign_vars(
    array(
      'USERNAME'=>$userdata['username'],
      'USERID'=>$userdata['id'],
      'EMAIL'=>@$userdata['mail_address'],
      'LANG_SELECT'=>language_select($userdata['language'], 'language'),
      'NB_IMAGE_LINE'=>$userdata['nb_image_line'],
      'NB_ROW_PAGE'=>$userdata['nb_line_page'],
      'STYLE_SELECT'=>style_select($userdata['template'], 'template'),
      'RECENT_PERIOD'=>$userdata['recent_period'],
      'MAXWIDTH'=>@$userdata['maxwidth'],
      'MAXHEIGHT'=>@$userdata['maxheight'],
  
      $expand=>'checked="checked"',
      $nb_comments=>'checked="checked"',
      
      'L_TITLE' => $lang['customize_title'],
      'L_REGISTRATION_INFO' => $lang['register_title'],
      'L_PREFERENCES' => $lang['preferences'],
      'L_USERNAME' => $lang['login'],
      'L_EMAIL' => $lang['mail_address'],
      'L_CURRENT_PASSWORD' => $lang['password'],
      'L_CURRENT_PASSWORD_HINT' => $lang['password_hint'],
      'L_NEW_PASSWORD' =>  $lang['new_password'],
      'L_NEW_PASSWORD_HINT' => $lang['new_password_hint'],
      'L_CONFIRM_PASSWORD' =>  $lang['reg_confirm'],
      'L_CONFIRM_PASSWORD_HINT' => $lang['confirm_password_hint'],
      'L_LANG_SELECT'=>$lang['language'],
      'L_NB_IMAGE_LINE'=>$lang['nb_image_per_row'],
      'L_NB_ROW_PAGE'=>$lang['nb_row_per_page'],
      'L_STYLE_SELECT'=>$lang['theme'],
      'L_RECENT_PERIOD'=>$lang['recent_period'],
      'L_EXPAND_TREE'=>$lang['auto_expand'],
      'L_NB_COMMENTS'=>$lang['show_nb_comments'],
      'L_MAXWIDTH'=>$lang['maxwidth'],
      'L_MAXHEIGHT'=>$lang['maxheight'],
      'L_YES'=>$lang['yes'],
      'L_NO'=>$lang['no'],
      'L_SUBMIT'=>$lang['submit'],
      'L_RESET'=>$lang['reset'],
      'L_RETURN' =>  $lang['home'],
      'L_RETURN_HINT' =>  $lang['home_hint'],  
      
      'F_ACTION'=>add_session_id($url_action),
      ));

  if (!defined('IN_ADMIN') or !IN_ADMIN)
  {
    $url_return = PHPWG_ROOT_PATH.'category.php?'.$_SERVER['QUERY_STRING'];
    $template->assign_vars(array('U_RETURN' => add_session_id($url_return)));
  }
//------------------------------------------------------------- user management
  if (defined('IN_ADMIN') and IN_ADMIN)
  {
    $status_select = '<select name="status">';
    $status_select .='<option value = "guest" ';
    if ($userdata['status'] == 'guest')
    {
      $status_select .= 'selected="selected"';
    }
    $status_select .='>'.$lang['user_status_guest'] .'</option>';
    $status_select .='<option value = "admin" ';
    if ($userdata['status'] == 'admin')
    {
      $status_select .= 'selected="selected"';
    }
    $status_select .='>'.$lang['user_status_admin'] .'</option>';
    $status_select .='</select>';
    $template->assign_block_vars(
      'modify.admin',
      array(
        'L_ADMIN_USER'=>$lang['user_management'],
        'L_STATUS'=>$lang['user_status'],
        'L_DELETE'=>$lang['user_delete'],
        'L_DELETE_HINT'=>$lang['user_delete_hint'],
        'STATUS'=>$status_select
        ));
  }
}
// +-----------------------------------------------------------------------+
// |                             errors display                            |
// +-----------------------------------------------------------------------+
if (count($errors) != 0)
{
  $template->assign_block_vars('errors',array());
  foreach ($errors as $error)
  {
    $template->assign_block_vars('errors.error', array('ERROR'=>$error));
  }
}
// +-----------------------------------------------------------------------+
// |                           html code display                           |
// +-----------------------------------------------------------------------+
if (defined('IN_ADMIN') and IN_ADMIN)
{
  $template->assign_var_from_handle('ADMIN_CONTENT', 'profile_body');
}
else
{
  $template->assign_block_vars('modify.profile',array());
  $template->parse('profile_body');
  include(PHPWG_ROOT_PATH.'include/page_tail.php');
}
?>
