<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
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

// +-----------------------------------------------------------------------+
// |                               functions                               |
// +-----------------------------------------------------------------------+

/*
 * Returns an array of mail configuration parameters :
 *
 * - mail_options: see $conf['mail_options']
 * - send_bcc_mail_webmaster: see $conf['send_bcc_mail_webmaster']
 * - email_webmaster: mail corresponding to $conf['webmaster_id']
 * - formated_email_webmaster: the name of webmaster is $conf['gallery_title']
 * - text_footer: PhpWebGallery and version
 *
 * @return array
 */
function get_mail_configuration()
{
  global $conf;

  $conf_mail = array(
    'mail_options' => $conf['mail_options'],
    'send_bcc_mail_webmaster' => $conf['send_bcc_mail_webmaster'],
    'default_email_format' => $conf['default_email_format'],
    'use_smtp' => !empty($conf['smtp_host']),
    'smtp_host' => $conf['smtp_host'],
    'smtp_user' => $conf['smtp_user'],
    'smtp_password' => $conf['smtp_password']
    );

  // we have webmaster id among user list, what's his email address ?
  $conf_mail['email_webmaster'] = get_webmaster_mail_address();

  // name of the webmaster is the title of the gallery
  $conf_mail['formated_email_webmaster'] =
    format_email($conf['gallery_title'], $conf_mail['email_webmaster']);

  $conf_mail['boundary_key'] = generate_key(32);

  return $conf_mail;
}

/**
 * Returns an email address with an associated real name
 *
 * @param string name
 * @param string email
 */
function format_email($name, $email)
{
  global $conf;

  if ($conf['enabled_format_email'])
  {
    // Spring cleaning
    $cvt_name = trim(preg_replace('#[\n\r]+#s', '', $name));
    $cvt_email = trim(preg_replace('#[\n\r]+#s', '', $email));
    // Ascii convertion
    $cvt_name = '"'.addslashes(str_translate_to_ascii7bits($cvt_name)).'"';

    if (strpos($cvt_email, '<') === false)
    {
      return $cvt_name.' <'.$cvt_email.'>';
    }
    else
    {
      return $cvt_name.$cvt_email;
    }
  }
  else
  {
    return $cvt_email;
  }
}

/**
 * Returns an completed array template/theme
 * completed with get_default_template()
 *
 * @params:
 *   - args: incompleted array of template/theme
 *       o template: template to use [default get_default_template()]
 *       o theme: template to use [default get_default_template()]
 */
function get_array_template_theme($args = array())
{
  global $conf;

  $res = array();
  
  if (empty($args['template']) or empty($args['theme']))
  {
    list($res['template'], $res['theme']) = explode('/', get_default_template());
  }

  if (!empty($args['template']))
  {
    $res['template'] = $args['template'];
  }

  if (!empty($args['theme']))
  {
    $res['theme'] = $args['theme'];
  }

  return $res;
}

/**
 * Return an new mail template
 *
 * @params:
 *   - email_format: mail format
 *   - args: function params of mail function:
 *       o template: template to use [default get_default_template()]
 *       o theme: template to use [default get_default_template()]
 */
function get_mail_template($email_format, $args = array())
{
  $args = get_array_template_theme($args);

  $mail_template = new Template(PHPWG_ROOT_PATH.'template/'.$args['template'], $args['theme']);
  $mail_template->set_rootdir(PHPWG_ROOT_PATH.'template/'.$args['template'].'/mail/'.$email_format);

  return $mail_template;
}

/**
 * Return string email format (html or not) 
 *
 * @param string format
 */
function get_str_email_format($is_html)
{
  return ($is_html ? 'text/html' : 'text/plain');
}

/* 
 * Switch language to param language
 * All entries are push on language stack
 *
 * @param string language
 */
function switch_lang_to($language)
{
  global $switch_lang, $user, $lang, $lang_info;

  if (count($switch_lang['stack']) == 0)
  {
    $prev_language = $user['language'];
  }
  else
  {
    $prev_language = end($switch_lang['stack']);
  }

  $switch_lang['stack'][] = $language;

  if ($prev_language != $language)
  {
    if (!isset($switch_lang['language'][$prev_language]))
    {
      $switch_lang[$prev_language]['lang_info'] = $lang_info;
      $switch_lang[$prev_language]['lang'] = $lang;
    }

    if (!isset($switch_lang['language'][$language]))
    {
      // Re-Init language arrays
      $lang_info = array();
      $lang  = array();

      // language files
      include(get_language_filepath('common.lang.php', '', $language));
      // No test admin because script is checked admin (user selected no)
      // Translations are in admin file too
      include(get_language_filepath('admin.lang.php', '', $language));
      trigger_action('loading_lang');
      @include(get_language_filepath('local.lang.php', '', $language));

      $switch_lang[$language]['lang_info'] = $lang_info;
      $switch_lang[$language]['lang'] = $lang;
    }
    else
    {
      $lang_info = $switch_lang[$language]['lang_info'];
      $lang = $switch_lang[$language]['lang'];
    }
  }
}

/* 
 * Switch back language pushed with switch_lang_to function
 *
 * @param: none
 */
function switch_lang_back()
{
  global $switch_lang, $user, $lang, $lang_info;

  $last_language = array_pop($switch_lang['stack']);

  if (count($switch_lang['stack']) > 0)
  {
    $language = end($switch_lang['stack']);
  }
  else
  {
    $language = $user['language'];
  }

  if ($last_language != $language)
  {
    if (!isset($switch_lang['language'][$language]))
    {
      $lang_info = $switch_lang[$language]['lang_info'];
      $lang = $switch_lang[$language]['lang'];
    }
  }
}

/**
 * Returns email of all administrator
 *
 * @return string
 */
/*
 * send en notification email to all administrators
 * if a administrator is doing action, 
 * he's be removed to email list
 *
 * @param:
 *   - keyargs_subject: mail subject on l10n_args format
 *   - keyargs_content: mail content on l10n_args format
 *
 * @return boolean (Ok or not)
 */
function pwg_mail_notification_admins($keyargs_subject, $keyargs_content)
{
  global $conf, $user;
  $return = true;

  $admins = array();

  $query = '
select
  U.'.$conf['user_fields']['username'].' as username,
  U.'.$conf['user_fields']['email'].' as mail_address
from
  '.USERS_TABLE.' as U,
  '.USER_INFOS_TABLE.' as I
where
  I.user_id =  U.'.$conf['user_fields']['id'].' and
  I.status in (\'webmaster\',  \'admin\') and
  I.adviser = \'false\' and
  '.$conf['user_fields']['email'].' is not null and
  I.user_id <> '.$user['id'].'
order by
  username
';

  $datas = pwg_query($query);
  if (!empty($datas))
  {
    while ($admin = mysql_fetch_array($datas))
    {
      if (!empty($admin['mail_address']))
      {
        array_push($admins, format_email($admin['username'], $admin['mail_address']));
      }
    }
  }

  if (count($admins) > 0)
  {
    $keyargs_content_admin_info = array
    (
      get_l10n_args('Connected user: %s', $user['username']),
      get_l10n_args('IP: %s', $_SERVER['REMOTE_ADDR']),
      get_l10n_args('Browser: %s', $_SERVER['HTTP_USER_AGENT'])
    );

    switch_lang_to(get_default_language());

    $return = pwg_mail
    (
      '',
      array
      (
        'Bcc' => $admins,
        'subject' => '['.$conf['gallery_title'].'] '.l10n_args($keyargs_subject),
        'content' => 
           l10n_args($keyargs_content)."\n\n"
          .l10n_args($keyargs_content_admin_info)."\n",
        'content_format' => 'text/plain'
      )
    ) and $return;

    switch_lang_back();
  }

  return $return;
}

/*
 * send en email to user's group
 *
 * @param:
 *   - group_id: mail are sent to group with this Id
 *   - email_format: mail format
 *   - keyargs_subject: mail subject on l10n_args format
 *   - dirname: short name of directory including template
 *   - tpl_shortname: short template name without extension
 *   - assign_vars: array used to assign_vars to mail template
 *   - language_selected: send mail only to user with this selected language
 *
 * @return boolean (Ok or not)
 */
function pwg_mail_group(
  $group_id, $email_format, $keyargs_subject, 
  $dirname, $tpl_shortname,
  $assign_vars = array(), $language_selected = '')
{
  global $conf;
  $return = true;

  $query = '
SELECT
  distinct language, template
FROM 
  '.USER_GROUP_TABLE.' as ug 
  INNER JOIN '.USERS_TABLE.' as u  ON '.$conf['user_fields']['id'].' = ug.user_id
  INNER JOIN '.USER_INFOS_TABLE.' as ui  ON ui.user_id = ug.user_id
WHERE 
        '.$conf['user_fields']['email'].' IS NOT NULL
    AND group_id = '.$group_id;

  if (!empty($language_selected))
  {
    $query .= '
    AND language = \''.$language_selected.'\'';
  }

    $query .= '
;';

  $result = pwg_query($query);

  if (mysql_num_rows($result) > 0)
  {
    $list = array();
    while ($row = mysql_fetch_array($result))
    {
      $row['template_theme'] = $row['template'];
      list($row['template'], $row['theme']) = explode('/', $row['template_theme']);
      $list[] = $row;
    }
  }

  foreach ($list as $elem)
  {
    $query = '
SELECT
  u.'.$conf['user_fields']['username'].' as username,
  u.'.$conf['user_fields']['email'].' as mail_address
FROM 
  '.USER_GROUP_TABLE.' as ug 
  INNER JOIN '.USERS_TABLE.' as u  ON '.$conf['user_fields']['id'].' = ug.user_id
  INNER JOIN '.USER_INFOS_TABLE.' as ui  ON ui.user_id = ug.user_id
WHERE 
        '.$conf['user_fields']['email'].' IS NOT NULL
    AND group_id = '.$group_id.'
    AND language = \''.$elem['language'].'\'
    AND template = \''.$elem['template_theme'].'\'
;';

    $result = pwg_query($query);

    if (mysql_num_rows($result) > 0)
    {
      $Bcc = array();
      while ($row = mysql_fetch_array($result))
      {
        if (!empty($row['mail_address']))
        {
          array_push($Bcc, format_email($row['username'], $row['mail_address']));
        }
      }

      if (count($Bcc) > 0)
      {
        switch_lang_to($elem['language']);

        $mail_template = get_mail_template($email_format, $elem);
        $mail_template->set_filename($tpl_shortname, 
          (empty($dirname) ? '' : $dirname.'/').$tpl_shortname.'.tpl');
        $mail_template->assign_vars($assign_vars);

        $return = pwg_mail
        (
          '',
          array
          (
            'Bcc' => $Bcc,
            'subject' => l10n_args($keyargs_subject),
            'email_format' => $email_format,
            'content' => $mail_template->parse($tpl_shortname, true),
            'content_format' => $email_format,
            'template' => $elem['template'],
            'theme' => $elem['theme']
          )
        ) and $return;

        switch_lang_back();
      }
    }
  }

  return $return;
}

/*
 * sends an email, using PhpWebGallery specific informations
 *
 * @param:
 *   - to: Receiver, or receivers of the mail.
 *   - args: function params of mail function:
 *       o from: sender [default value webmaster email]
 *       o Cc: array of carbon copy receivers of the mail. [default value empty]
 *       o Bcc: array of blind carbon copy receivers of the mail. [default value empty]
 *       o subject  [default value 'PhpWebGallery']
 *       o content: content of mail    [default value '']
 *       o content_format: format of mail content  [default value 'text/plain']
 *       o email_format: global mail format  [default value $conf_mail['default_email_format']]
 *       o template: template to use [default get_default_template()]
 *       o theme: template to use [default get_default_template()]
 *
 * @return boolean (Ok or not)
 */
function pwg_mail($to, $args = array())
{
  global $conf, $conf_mail, $lang_info, $page;

  if (empty($to) and empty($args['Cc']) and empty($args['Bcc']))
  {
    return true;
  }
  
  if (!isset($conf_mail))
  {
    $conf_mail = get_mail_configuration();
  }

  if (empty($args['email_format']))
  {
    $args['email_format'] = $conf_mail['default_email_format'];
  }

  // Compute root_path in order have complete path
  if ($args['email_format'] == 'text/html')
  {
    set_make_full_url();
  }

  if (!empty($to))
  {
    $to = format_email('', $to);
  }

  if (empty($args['from']))
  {
    $args['from'] = $conf_mail['formated_email_webmaster'];
  }
  else
  {
    $args['from'] = format_email('', $args['from']);
  }

  if (empty($args['subject']))
  {
    $args['subject'] = 'PhpWebGallery';
  }
  // Spring cleaning
  $cvt_subject = trim(preg_replace('#[\n\r]+#s', '', $args['subject']));
  // Ascii convertion
  $cvt_subject = str_translate_to_ascii7bits($cvt_subject);

  if (!isset($args['content']))
  {
    $args['content'] = '';
  }

  if (empty($args['content_format']))
  {
    $args['content_format'] = 'text/plain';
  }

  if ($conf_mail['send_bcc_mail_webmaster'])
  {
    $args['Bcc'][] = $conf_mail['formated_email_webmaster'];
  }

  if (($args['content_format'] == 'text/html') and ($args['email_format'] == 'text/plain'))
  {
    // Todo find function to convert html text to plain text
    return false;
  }

  $args = array_merge($args, get_array_template_theme($args));

  $headers = 'From: '.$args['from']."\n";
  $headers.= 'Reply-To: '.$args['from']."\n";
  if (empty($to))
  {
    $headers.= 'To: undisclosed-recipients: ;'."\n";
  }

  if (!empty($args['Cc']))
  {
    $headers.= 'Cc: '.implode(',', $args['Cc'])."\n";
  }

  if (!empty($args['Bcc']))
  {
    $headers.= 'Bcc: '.implode(',', $args['Bcc'])."\n";
  }

  $headers.= 'Content-Type: multipart/alternative;'."\n";
  $headers.= '  boundary="---='.$conf_mail['boundary_key'].'";'."\n";
  $headers.= '  reply-type=original'."\n";
  $headers.= 'MIME-Version: 1.0'."\n";
  $headers.= 'X-Mailer: Piwigo Mailer'."\n";

  $content = '';

  if (!isset($conf_mail[$args['email_format']][$lang_info['charset']][$args['template']][$args['theme']]))
  {
    if (!isset($mail_template))
    {
      $mail_template = get_mail_template($args['email_format']);
    }

    $mail_template->set_filename('mail_header', 'header.tpl');
    $mail_template->set_filename('mail_footer', 'footer.tpl');

    $mail_template->assign_vars(
      array(
        //Header
        'BOUNDARY_KEY' => $conf_mail['boundary_key'],
        'CONTENT_TYPE' => $args['email_format'],
        'CONTENT_ENCODING' => $lang_info['charset'],
        'LANG' => $lang_info['code'],
        'DIR' => $lang_info['direction'],
        
        // Footer
        'GALLERY_URL' =>
          isset($page['gallery_url']) ?
                $page['gallery_url'] : $conf['gallery_url'],
        'GALLERY_TITLE' =>
          isset($page['gallery_title']) ?
                $page['gallery_title'] : $conf['gallery_title'],
        'VERSION' => $conf['show_version'] ? PHPWG_VERSION : '',
        'PHPWG_URL' => PHPWG_URL,

        'TITLE_MAIL' => urlencode(l10n('title_send_mail')),
        'MAIL' => get_webmaster_mail_address()
        ));

    if ($args['email_format'] == 'text/html')
    {
      $old_root = $mail_template->root;

      if (is_file($mail_template->root.'/global-mail-css.tpl'))
      {
        $mail_template->set_filename('global_mail_css', 'global-mail-css.tpl');
        $mail_template->assign_var_from_handle('GLOBAL_MAIL_CSS', 'global_mail_css');
      }

      $mail_template->root = PHPWG_ROOT_PATH.'template/'.$args['template'].'/theme/'.$args['theme'];
      if (is_file($mail_template->root.'/mail-css.tpl'))
      {
        $mail_template->set_filename('mail_css', 'mail-css.tpl');
        $mail_template->assign_var_from_handle('MAIL_CSS', 'mail_css');
      }

      $mail_template->root = PHPWG_ROOT_PATH.'template-common';
      if (is_file($mail_template->root.'/local-mail-css.tpl'))
      {
        $mail_template->set_filename('local_mail_css', 'local-mail-css.tpl');
        $mail_template->assign_var_from_handle('LOCAL_MAIL_CSS', 'local_mail_css');
      }

      $mail_template->root = $old_root;
    }

    // what are displayed on the header of each mail ?
    $conf_mail[$args['email_format']]
      [$lang_info['charset']]
      [$args['template']][$args['theme']]['header'] =
        $mail_template->parse('mail_header', true);

    // what are displayed on the footer of each mail ?
    $conf_mail[$args['email_format']]
      [$lang_info['charset']]
      [$args['template']][$args['theme']]['footer'] =
        $mail_template->parse('mail_footer', true);
  }

  // Header
  $content.= $conf_mail[$args['email_format']]
              [$lang_info['charset']]
              [$args['template']][$args['theme']]['header'];

  // Content
  if (($args['content_format'] == 'text/plain') and ($args['email_format'] == 'text/html'))
  {
    $content.= '<p>'.
                nl2br(
                  preg_replace("/(http:\/\/)([^\s,]*)/i",
                               "<a href='$1$2'>$1$2</a>",
                               htmlentities($args['content']))).
                '</p>';
  }
  else
  {
    $content.= $args['content'];
  }

  // Footer
  $content.= $conf_mail[$args['email_format']]
              [$lang_info['charset']]
              [$args['template']][$args['theme']]['footer'];

  // Close boundary
  $content.= "\n".'-----='.$conf_mail['boundary_key'].'--'."\n";

   // Undo Compute root_path in order have complete path
  if ($args['email_format'] == 'text/html')
  {
    unset_make_full_url();
  }

  /*Testing block
  {
    global $user;
    @mkdir(PHPWG_ROOT_PATH.'testmail');
    $filename = PHPWG_ROOT_PATH.'testmail/mail.'.$user['username'];
    if ($args['content_format'] == 'text/plain')
    {
      $filename .= '.txt';
    }
    else
    {
      $filename .= '.html';
    }
    $file = fopen($filename, 'w+');
    fwrite($file, $content);
    fclose($file);
    return true;
  }*/

  if ($conf_mail['use_smtp'])
  {
    include_once( PHPWG_ROOT_PATH.'include/class_smtp_mail.inc.php' );
    $smtp_mail = new smtp_mail(
      $conf_mail['smtp_host'], $conf_mail['smtp_user'], $conf_mail['smtp_password'],
      $conf_mail['email_webmaster']);
    return $smtp_mail->mail($to, $cvt_subject, $content, $headers);
  }
  else
  {
    if ($conf_mail['mail_options'])
    {
      $options = '-f '.$conf_mail['email_webmaster'];
      return mail($to, $cvt_subject, $content, $headers, $options);
    }
    else
    {
      return mail($to, $cvt_subject, $content, $headers);
    }
  }
}

?>
