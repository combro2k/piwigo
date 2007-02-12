<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// | Copyright (C) 2006 Ruben ARNAUD - team@phpwebgallery.net              |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date: 2005-11-26 21:15:50 +0100 (sam., 26 nov. 2005) $
// | last modifier : $Author: plg $
// | revision      : $Revision: 958 $
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
 * - Extract mail fonctions of password.php
 * - Modify pwg_mail (add pararameters + news fonctionnalities)
 * - Var conf_mail, function get_mail_configuration, format_email, pwg_mail
 */

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
    'default_email_format' => $conf['default_email_format']
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
    $cvt7b_name = str_translate_to_ascii7bits($name);

    if (strpos($email, '<') === false)
    {
      return $cvt7b_name.' <'.$email.'>';
    }
    else
    {
      return $cvt7b_name.$email;
    }
  }
  else
  {
    return $email;
  }
}

/**
 * Return an completed array template/theme
 * completed with $conf['default_template']
 *
 * @params:
 *   - args: incompleted array of template/theme
 *       o template: template to use [default $conf['default_template']]
 *       o theme: template to use [default $conf['default_template']]
 */
function get_array_template_theme($args = array())
{
  global $conf;

  $res = array();
  
  if (empty($args['template']) or empty($args['theme']))
  {
    list($res['template'], $res['theme']) = explode('/', $conf['default_template']);
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
 *       o template: template to use [default $conf['default_template']]
 *       o theme: template to use [default $conf['default_template']]
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

/**
 * sends an email, using PhpWebGallery specific informations
 *
 * @param:
 *   - to: Receiver, or receivers of the mail.
 *   - args: function params of mail function:
 *       o from: sender [default value webmaster email]
 *       o subject  [default value 'PhpWebGallery']
 *       o content: content of mail    [default value '']
 *       o content_format: format of mail content  [default value 'text/plain']
 *       o email_format: global mail format  [default value $conf_mail['default_email_format']]
 *       o template: template to use [default $conf['default_template']]
 *       o theme: template to use [default $conf['default_template']]
 */
//function pwg_mail($to, $from = '', $subject = 'PhpWebGallery', $infos = '', $infos_format = 'text/plain', $email_format = null)
function pwg_mail($to, $args = array())
{
  global $conf, $conf_mail, $lang_info, $page;

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

  $to = format_email('', $to);

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
  $cvt7b_subject = str_translate_to_ascii7bits($args['subject']);

  if (!isset($args['content']))
  {
    $args['content'] = '';
  }

  if (empty($args['content_format']))
  {
    $args['content_format'] = 'text/plain';
  }

  if (($args['content_format'] == 'text/html') and ($args['email_format'] == 'text/plain'))
  {
    // Todo find function to convert html text to plain text
    return false;
  }

  $args = array_merge($args, get_array_template_theme($args));

  $headers = 'From: '.$args['from']."\n";
  $headers.= 'Reply-To: '.$args['from']."\n";
  $headers.= 'Content-Type: multipart/alternative;'."\n";
  $headers.= '  boundary="---='.$conf_mail['boundary_key'].'";'."\n";
  $headers.= '  reply-type=original'."\n";
  $headers.= 'MIME-Version: 1.0'."\n";

  if ($conf_mail['send_bcc_mail_webmaster'])
  {
    $headers.= 'Bcc: '.$conf_mail['formated_email_webmaster']."\n";
  }

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
    $content.= '<p>'.nl2br(htmlentities($args['content'])).'</p>';
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

  if ($conf_mail['mail_options'])
  {
    $options = '-f '.$conf_mail['email_webmaster'];
    
    return mail($to, $cvt7b_subject, $content, $headers, $options);
  }
  else
  {
    return mail($to, $cvt7b_subject, $content, $headers);
  }
}

?>
