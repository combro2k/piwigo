<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2010 Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 PhpWebGallery Team    http://phpwebgallery.net |
// | Copyright(C) 2002-2003 Pierrick LE GALL   http://le-gall.net/pierrick |
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

if (!defined('PHPWG_ROOT_PATH'))
{
  die ("Hacking attempt!");
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

if (!empty($_POST) or isset($_GET['action']))
{
  check_pwg_token();
}

/**
 * requests the given $url (a remote create_listing_file.php) and fills a
 * list of lines corresponding to request output
 *
 * @param string $url
 * @return void
 */
function remote_output($url)
{
  global $template, $page;

  if (fetchRemote($url, $result))
  {
    $lines = explode("\r\n", $result);
    // cleaning lines from HTML tags
    foreach ($lines as $line)
    {
      $line = trim(strip_tags($line));
      if (preg_match('/^PWG-([A-Z]+)-/', $line, $matches))
      {
        $template->append(
          'remote_output',
          array(
            'CLASS' => 'remote'.ucfirst(strtolower($matches[1])),
            'CONTENT' => $line
           )
         );
      }
    }
  }
  else
  {
    array_push($page['errors'], l10n('file create_listing_file.php on remote site was not found'));
  }
}


// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+
$template->set_filenames(array('site_manager'=>'site_manager.tpl'));

// +-----------------------------------------------------------------------+
// |                        new site creation form                         |
// +-----------------------------------------------------------------------+
if (isset($_POST['submit']) and !empty($_POST['galleries_url'])
    and !is_adviser() )
{
  $is_remote = url_is_remote( $_POST['galleries_url'] );
  $url = preg_replace('/[\/]*$/', '', $_POST['galleries_url']);
  $url.= '/';
  if (! $is_remote)
  {
    if ( ! (strpos($url, '.') === 0 ) )
    {
      $url = './' . $url;
    }
  }

  // site must not exists
  $query = '
SELECT COUNT(id) AS count
  FROM '.SITES_TABLE.'
  WHERE galleries_url = \''.$url.'\'
;';
  $row = pwg_db_fetch_assoc(pwg_query($query));
  if ($row['count'] > 0)
  {
    array_push($page['errors'],
      l10n('This site already exists').' ['.$url.']');
  }
  if (count($page['errors']) == 0)
  {
    if ($is_remote)
    {
      if ( ! isset($_POST['no_check']) )
      {
        $clf_url = $url.'create_listing_file.php';
        $get_data = array(
          'action' => 'test',
          'version' => PHPWG_VERSION,
        );
        if (fetchRemote($clf_url, $result, $get_data))
        {
          $lines = explode("\r\n", $result);
          $first_line = strip_tags($lines[0]);
          if (!preg_match('/^PWG-INFO-2:/', $first_line))
          {
            array_push($page['errors'],
                       l10n('an error happened').' : '.$first_line);
          }
        }
        else
        {
          array_push($page['errors'], l10n('file create_listing_file.php on remote site was not found') );
        }
      }
    }
    else
    { // local directory
      if ( ! file_exists($url) )
      {
        array_push($page['errors'],
          l10n('Directory does not exist').' ['.$url.']');
      }
    }
  }

  if (count($page['errors']) == 0)
  {
    $query = '
INSERT INTO '.SITES_TABLE.'
  (galleries_url)
  VALUES
  (\''.$url.'\')
;';
    pwg_query($query);
    array_push($page['infos'],
               $url.' '.l10n('created'));
  }
}

// +-----------------------------------------------------------------------+
// |                            actions on site                            |
// +-----------------------------------------------------------------------+
if (isset($_GET['site']) and is_numeric($_GET['site']))
{
  $page['site'] = $_GET['site'];
}
if (isset($_GET['action']) and isset($page['site']) and !is_adviser())
{
  $query = '
SELECT galleries_url
  FROM '.SITES_TABLE.'
  WHERE id = '.$page['site'].'
;';
  list($galleries_url) = pwg_db_fetch_row(pwg_query($query));
  switch($_GET['action'])
  {
    case 'generate' :
    {
      $title = $galleries_url.' : '.l10n('generate listing');
      remote_output($galleries_url.'create_listing_file.php?action=generate');
      break;
    }
    case 'test' :
    {
      $title = $galleries_url.' : '.l10n('test');
      remote_output($galleries_url.'create_listing_file.php?action=test&version='.PHPWG_VERSION);
      break;
    }
    case 'clean' :
    {
      $title = $galleries_url.' : '.l10n('clean');
      remote_output($galleries_url.'create_listing_file.php?action=clean');
      break;
    }
    case 'delete' :
    {
      delete_site($page['site']);
      array_push($page['infos'],
                 $galleries_url.' '.l10n('deleted'));
      break;
    }
  }
}

$template->assign(
  array(
    'U_HELP'    => get_root_url().'admin/popuphelp.php?page=site_manager',
    'F_ACTION'  => get_root_url().'admin.php'.get_query_string_diff(array('action','site','pwg_token')),
    'PWG_TOKEN' => get_pwg_token(),
    )
  );

// +-----------------------------------------------------------------------+
// |                           remote sites list                           |
// +-----------------------------------------------------------------------+

if ( is_file(PHPWG_ROOT_PATH.'listing.xml') )
{
  $xml_content = getXmlCode(PHPWG_ROOT_PATH.'listing.xml');
  $local_listing_site_url = getAttribute(
          getChild($xml_content, 'informations'),
          'url'
        );
  if ( !url_is_remote($local_listing_site_url) )
  {
    $local_listing_site_url = null;
  }
}

$query = '
SELECT c.site_id, COUNT(DISTINCT c.id) AS nb_categories, COUNT(i.id) AS nb_images
  FROM '.CATEGORIES_TABLE.' AS c LEFT JOIN '.IMAGES_TABLE.' AS i
  ON c.id=i.storage_category_id 
  WHERE c.site_id IS NOT NULL
  GROUP BY c.site_id
;';
$sites_detail = hash_from_query($query, 'site_id'); 

$query = '
SELECT *
  FROM '.SITES_TABLE.'
;';
$result = pwg_query($query);

while ($row = pwg_db_fetch_assoc($result))
{
  $is_remote = url_is_remote($row['galleries_url']);
  $base_url = PHPWG_ROOT_PATH.'admin.php';
  $base_url.= '?page=site_manager';
  $base_url.= '&amp;site='.$row['id'];
  $base_url.= '&amp;pwg_token='.get_pwg_token();
  $base_url.= '&amp;action=';

  $update_url = PHPWG_ROOT_PATH.'admin.php';
  $update_url.= '?page=site_update';
  $update_url.= '&amp;site='.$row['id'];
  
  $tpl_var =
    array(
      'NAME' => $row['galleries_url'],
      'TYPE' => l10n( $is_remote ? 'Remote' : 'Local' ),
      'CATEGORIES' => (int)@$sites_detail[$row['id']]['nb_categories'],
      'IMAGES' => (int)@$sites_detail[$row['id']]['nb_images'],
      'U_SYNCHRONIZE' => $update_url
     );
     
   if ($is_remote)
   {
     $tpl_var['remote'] =
       array(
         'U_TEST' => $base_url.'test',
         'U_GENERATE' => $row['galleries_url'].'create_listing_file.php?action=generate',
         'U_CLEAN' => $base_url.'clean',
         );
   }

  if ($row['id'] != 1)
  {
    $tpl_var['U_DELETE'] = $base_url.'delete';
  }

  $plugin_links = array();
  //$plugin_links is array of array composed of U_HREF, U_HINT & U_CAPTION
  $plugin_links =
    trigger_event('get_admins_site_links',
      $plugin_links, $row['id'], $is_remote);
  $tpl_var['plugin_links'] = $plugin_links;

  $template->append('sites', $tpl_var);

  if ( isset($local_listing_site_url) and
       $row['galleries_url']==$local_listing_site_url )
  {
    $local_listing_site_id = $row['id'];
    $template->assign( 'local_listing',
        array(
          'URL' =>  $local_listing_site_url,
          'U_SYNCHRONIZE' => $update_url.'&amp;local_listing=1'
        )
      );
  }
}

if ( isset($local_listing_site_url) and !isset($local_listing_site_id) )
{
  $template->assign( 'local_listing',
      array(
        'URL' =>  $local_listing_site_url,
        'CREATE' => true
      )
    );
}


$template->assign_var_from_handle('ADMIN_CONTENT', 'site_manager');
?>
