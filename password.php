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

// +-----------------------------------------------------------------------+
// |                           initialization                              |
// +-----------------------------------------------------------------------+

define('PHPWG_ROOT_PATH','./');
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );
include_once(PHPWG_ROOT_PATH.'include/functions_mail.inc.php');

// +-----------------------------------------------------------------------+
// |                          send a new password                          |
// +-----------------------------------------------------------------------+

$page['errors'] = array();
$page['infos'] = array();

if (isset($_POST['submit']))
{
  // in case of error, creation of mailto link
  $query = '
SELECT '.$conf['user_fields']['email'].'
  FROM '.USERS_TABLE.'
  WHERE '.$conf['user_fields']['id'].' = '.$conf['webmaster_id'].'
;';
  list($mail_webmaster) = mysql_fetch_array(pwg_query($query));

  $mailto =
    '<a href="mailto:'.$mail_webmaster.'">'
    .l10n('Contact webmaster')
    .'</a>'
    ;

  if (isset($_POST['no_mail_address']) and $_POST['no_mail_address'] == 1)
  {
    array_push($page['infos'], l10n('Email address is missing'));
    array_push($page['infos'], $mailto);
  }
  else if (isset($_POST['mail_address']) and !empty($_POST['mail_address']))
  {
    $mail_address = mysql_escape_string($_POST['mail_address']);
    
    $query = '
SELECT '.$conf['user_fields']['id'].' AS id
     , '.$conf['user_fields']['username'].' AS username
     , '.$conf['user_fields']['email'].' AS email
FROM '.USERS_TABLE.' as u
  INNER JOIN '.USER_INFOS_TABLE.' AS ui
      ON u.'.$conf['user_fields']['id'].' = ui.user_id
WHERE '
  .$conf['user_fields']['email'].' = \''.$mail_address.'\' AND
  ui.status not in (\'guest\', \'generic\', \'webmaster\')
;';
    $result = pwg_query($query);

    if (mysql_num_rows($result) > 0)
    {
      $error_on_mail = false;
      $datas = array();
      
      while ($row = mysql_fetch_array($result))
      {
        $new_password = generate_key(6);

        $infos =
          l10n('Username').': '.$row['username']
          ."\n".l10n('Password').': '.$new_password
          ;

        if (pwg_mail($row['email'], $mail_webmaster, l10n('password updated'), $infos))
        {
          $data =
            array(
              $conf['user_fields']['id']
              => $row['id'],
              
              $conf['user_fields']['password']
              => $conf['pass_convert']($new_password)
              );

          array_push($datas, $data);
        }
        else
        {
          $error_on_mail = true;
        }
      }
      
      if ($error_on_mail)
      {
        array_push($page['errors'], l10n('Error sending email'));
        array_push($page['errors'], $mailto);
      }
      else
      {
        include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
        mass_updates(
          USERS_TABLE,
          array(
            'primary' => array($conf['user_fields']['id']),
            'update' => array($conf['user_fields']['password'])
          ),
          $datas
          );

        array_push($page['infos'], l10n('New password sent by email'));
      }
    }
    else
    {
      array_push($page['errors'], l10n('No user matches this email address'));
      array_push($page['errors'], $mailto);
    }
  }
}

// +-----------------------------------------------------------------------+
// |                        template initialization                        |
// +-----------------------------------------------------------------------+

$title = l10n('Forgot your password?');
$page['body_id'] = 'thePasswordPage';
include(PHPWG_ROOT_PATH.'include/page_header.php');
$template->set_filenames(array('password'=>'password.tpl'));

$template->assign_vars(
  array(
    'U_HOME' => make_index_url(),
    )
  );

// +-----------------------------------------------------------------------+
// |                        infos & errors display                         |
// +-----------------------------------------------------------------------+

if (count($page['errors']) != 0)
{
  $template->assign_block_vars('errors', array());
  
  foreach ($page['errors'] as $error)
  {
    $template->assign_block_vars(
      'errors.error',
      array(
        'ERROR' => $error
        )
      );
  }
}

if (count($page['infos']) != 0)
{
  $template->assign_block_vars('infos', array());
  
  foreach ($page['infos'] as $info)
  {
    $template->assign_block_vars(
      'infos.info',
      array(
        'INFO' => $info
        )
      );
  }
}

// +-----------------------------------------------------------------------+
// |                           html code display                           |
// +-----------------------------------------------------------------------+

$template->parse('password');
include(PHPWG_ROOT_PATH.'include/page_tail.php');

?>