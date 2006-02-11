<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2006 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date: 2005-12-03 17:03:58 -0500 (Sat, 03 Dec 2005) $
// | last modifier : $Author: plg $
// | revision      : $Revision: 967 $
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
  die ('Hacking attempt!');
}
include_once( PHPWG_ROOT_PATH.'admin/include/isadmin.inc.php');

if (! is_numeric($_GET['site']) )
{
  die ('site param missing or invalid');
}
$site_id = $_GET['site'];
$query='SELECT galleries_url FROM '.SITES_TABLE.'
WHERE id='.$site_id.'
;';
list($site_url)=mysql_fetch_row(pwg_query($query));
if (! isset($site_url) )
{
  die ("site $site_id does not exist");
}
$site_is_remote = url_is_remote($site_url);

list($dbnow) = mysql_fetch_row(pwg_query('SELECT NOW();'));
define('CURRENT_DATE', $dbnow);

$error_labels = array(
  'PWG-UPDATE-1' => array( l10n('update_wrong_dirname_short'), 
                           l10n('update_wrong_dirname_info') ),
  'PWG-UPDATE-2' => array( l10n('update_missing_tn_short'),
                           l10n('update_missing_tn_info') 
                           . implode(',', $conf['picture_ext']) ),
  'PWG-ERROR-NO-FS' => array( l10n('Does not exist'), 
                             l10n('update_missing_file_or_dir_info')),
  'PWG-ERROR-VERSION' => array( l10n('Invalid PhpWebGalley version'), 
                             l10n('update_pwg_version_differs_info')),
  'PWG-ERROR-NOLISTING' => array( l10n('remote_site_listing_not_found'), 
                             l10n('remote_site_listing_not_found_info'))
                      );
$errors = array();
$infos = array();

if ($site_is_remote)
{
  include_once( PHPWG_ROOT_PATH.'admin/site_reader_remote.php');
  $site_reader = new RemoteSiteReader($site_url);
}
else
{
  include_once( PHPWG_ROOT_PATH.'admin/site_reader_local.php');
  $site_reader = new LocalSiteReader($site_url);
}

$general_failure=true;
if (isset($_POST['submit']))
{
  if ($site_reader->open())
  {
    $general_failure = false;
  }
  // shall we simulate only
  if (isset($_POST['simulate']) and $_POST['simulate'] == 1)
  {
    $simulate = true;
  }
  else
  {
    $simulate = false;
  }
}

// +-----------------------------------------------------------------------+
// |                      directories / categories                         |
// +-----------------------------------------------------------------------+
if (isset($_POST['submit'])
    and ($_POST['sync'] == 'dirs' or $_POST['sync'] == 'files')
    and !$general_failure)
{
  $counts['new_categories'] = 0;
  $counts['del_categories'] = 0;
  $counts['del_elements'] = 0;
  $counts['new_elements'] = 0;

  $start = get_moment();
  // which categories to update ?
  $cat_ids = array();

  $query = '
SELECT id, uppercats, global_rank, status, visible
  FROM '.CATEGORIES_TABLE.'
  WHERE dir IS NOT NULL
    AND site_id = '.$site_id;
  if (isset($_POST['cat']) and is_numeric($_POST['cat']))
  {
    if (isset($_POST['subcats-included']) and $_POST['subcats-included'] == 1)
    {
      $query.= '
    AND uppercats REGEXP \'(^|,)'.$_POST['cat'].'(,|$)\'
';
    }
    else
    {
      $query.= '
    AND id = '.$_POST['cat'].'
';
    }
  }
  $query.= '
;';
  $result = pwg_query($query);

  $db_categories = array();
  while ($row = mysql_fetch_array($result))
  {
    $db_categories[$row['id']] = $row;
  }

  // get categort full directories in an array for comparison with file
  // system directory tree
  $db_fulldirs = get_fulldirs(array_keys($db_categories));

  // what is the base directory to search file system sub-directories ?
  if (isset($_POST['cat']) and is_numeric($_POST['cat']))
  {
    $basedir = $db_fulldirs[$_POST['cat']];
  }
  else
  {
    $basedir = preg_replace('#/*$#', '', $site_url);
  }

  // we need to have fulldirs as keys to make efficient comparison
  $db_fulldirs = array_flip($db_fulldirs);

  // finding next rank for each id_uppercat. By default, each category id
  // has 1 for next rank on its sub-categories to create
  $next_rank['NULL'] = 1;

  $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    $next_rank[$row['id']] = 1;
  }

  // let's see if some categories already have some sub-categories...
  $query = '
SELECT id_uppercat, MAX(rank)+1 AS next_rank
  FROM '.CATEGORIES_TABLE.'
  GROUP BY id_uppercat
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    // for the id_uppercat NULL, we write 'NULL' and not the empty string
    if (!isset($row['id_uppercat']) or $row['id_uppercat'] == '')
    {
      $row['id_uppercat'] = 'NULL';
    }
    $next_rank[$row['id_uppercat']] = $row['next_rank'];
  }

  // next category id available
  $query = '
SELECT IF(MAX(id)+1 IS NULL, 1, MAX(id)+1) AS next_id
  FROM '.CATEGORIES_TABLE.'
;';
  list($next_id) = mysql_fetch_array(pwg_query($query));

  // retrieve sub-directories fulldirs from the site reader
  $fs_fulldirs = $site_reader->get_full_directories($basedir);
  //print_r( $fs_fulldirs ); echo "<br>";

  // get_full_directories doesn't include the base directory, so if it's a
  // category directory, we need to include it in our array
  if (isset($_POST['cat']))
  {
    array_push($fs_fulldirs, $basedir);
  }

  $inserts = array();
  // new categories are the directories not present yet in the database
  foreach (array_diff($fs_fulldirs, array_keys($db_fulldirs)) as $fulldir)
  {
    $dir = basename($fulldir);
    if (preg_match('/^[a-zA-Z0-9-_.]+$/', $dir))
    {
      $insert = array();

      $insert{'id'} = $next_id++;
      $insert{'dir'} = $dir;
      $insert{'name'} = str_replace('_', ' ', $dir);
      $insert{'site_id'} = $site_id;
      $insert{'commentable'} = $conf['newcat_default_commentable'];
      if (! $site_is_remote)
      {
        $insert{'uploadable'} = $conf['newcat_default_uploadable'];
      }
      else
      {
        $insert{'uploadable'} = 'false';
      }
      $insert{'status'} = $conf{'newcat_default_status'};
      $insert{'visible'} = $conf{'newcat_default_visible'};

      if (isset($db_fulldirs[dirname($fulldir)]))
      {
        $parent = $db_fulldirs[dirname($fulldir)];

        $insert{'id_uppercat'} = $parent;
        $insert{'uppercats'} =
          $db_categories[$parent]['uppercats'].','.$insert{'id'};
        $insert{'rank'} = $next_rank[$parent]++;
        $insert{'global_rank'} =
          $db_categories[$parent]['global_rank'].'.'.$insert{'rank'};
        if ('private' == $db_categories[$parent]['status'])
        {
          $insert{'status'} = 'private';
        }
        if ('false' == $db_categories[$parent]['visible'])
        {
          $insert{'visible'} = 'false';
        }
      }
      else
      {
        $insert{'uppercats'} = $insert{'id'};
        $insert{'rank'} = $next_rank['NULL']++;
        $insert{'global_rank'} = $insert{'rank'};
      }

      array_push($inserts, $insert);
      array_push($infos, array('path' => $fulldir,
                               'info' => l10n('update_research_added')));

      // add the new category to $db_categories and $db_fulldirs array
      $db_categories[$insert{'id'}] =
        array(
          'id' => $insert{'id'},
          'status' => $insert{'status'},
          'visible' => $insert{'visible'},
          'uppercats' => $insert{'uppercats'},
          'global_rank' => $insert{'global_rank'}
          );
      $db_fulldirs[$fulldir] = $insert{'id'};
      $next_rank[$insert{'id'}] = 1;
    }
    else
    {
      array_push($errors, array('path' => $fulldir, 'type' => 'PWG-UPDATE-1'));
    }
  }

  if (count($inserts) > 0)
  {
    if (!$simulate)
    {
      $dbfields = array(
        'id','dir','name','site_id','id_uppercat','uppercats','commentable',
        'uploadable','visible','status','rank','global_rank'
        );
      mass_inserts(CATEGORIES_TABLE, $dbfields, $inserts);
    }

    $counts['new_categories'] = count($inserts);
  }

  // to delete categories
  $to_delete = array();
  foreach (array_diff(array_keys($db_fulldirs), $fs_fulldirs) as $fulldir)
  {
    array_push($to_delete, $db_fulldirs[$fulldir]);
    unset($db_fulldirs[$fulldir]);
    array_push($infos, array('path' => $fulldir,
                             'info' => l10n('update_research_deleted')));
  }
  if (count($to_delete) > 0)
  {
    if (!$simulate)
    {
      delete_categories($to_delete);
    }
    $counts['del_categories'] = count($to_delete);
  }

  echo '<!-- scanning dirs : ';
  echo get_elapsed_time($start, get_moment());
  echo ' -->'."\n";
}
// +-----------------------------------------------------------------------+
// |                           files / elements                            |
// +-----------------------------------------------------------------------+
if (isset($_POST['submit']) and $_POST['sync'] == 'files'
      and !$general_failure)
{
  $start_files = get_moment();
  $start= $start_files;

  $fs = $site_reader->get_elements($basedir);
  //print_r($fs); echo "<br>";
  echo '<!-- get_elements: '.get_elapsed_time($start, get_moment())." -->\n";

  $cat_ids = array_diff(array_keys($db_categories), $to_delete);

  $db_elements = array();
  $db_unvalidated = array();

  if (count($cat_ids) > 0)
  {
    $query = '
SELECT id, path
  FROM '.IMAGES_TABLE.'
  WHERE storage_category_id IN (
'.wordwrap(implode(', ', $cat_ids), 80, "\n").')
;';
    $result = pwg_query($query);
    while ($row = mysql_fetch_array($result))
    {
      $db_elements[$row['id']] = $row['path'];
    }

    // searching the unvalidated waiting elements (they must not be taken into
    // account)
    $query = '
SELECT file,storage_category_id
  FROM '.WAITING_TABLE.'
  WHERE storage_category_id IN (
'.wordwrap(implode(', ', $cat_ids), 80, "\n").')
    AND validated = \'false\'
;';
    $result = pwg_query($query);
    while ($row = mysql_fetch_array($result))
    {
      array_push(
        $db_unvalidated,
        array_search($row['storage_category_id'],
                     $db_fulldirs).'/'.$row['file']
        );
    }
  }

  // next element id available
  $query = '
SELECT IF(MAX(id)+1 IS NULL, 1, MAX(id)+1) AS next_element_id
  FROM '.IMAGES_TABLE.'
;';
  list($next_element_id) = mysql_fetch_array(pwg_query($query));

  $start = get_moment();

  $inserts = array();
  $insert_links = array();

  foreach (array_diff(array_keys($fs), $db_elements, $db_unvalidated) as $path)
  {
    $insert = array();
    // storage category must exist
    $dirname = dirname($path);
    if (!isset($db_fulldirs[$dirname]))
    {
      continue;
    }
    $filename = basename($path);
    if (!preg_match('/^[a-zA-Z0-9-_.]+$/', $filename))
    {
      array_push($errors, array('path' => $path, 'type' => 'PWG-UPDATE-1'));
      continue;
    }

    // 2 cases : the element is a picture or not. Indeed, for a picture
    // thumbnail is mandatory and for non picture element, thumbnail and
    // representative are optionnal
    if (in_array(get_extension($filename), $conf['picture_ext']))
    {
      // if we found a thumnbnail corresponding to our picture...
      if ( isset($fs[$path]['tn_ext']) )
      {
        $insert{'id'} = $next_element_id++;
        $insert{'file'} = $filename;
        $insert{'storage_category_id'} = $db_fulldirs[$dirname];
        $insert{'date_available'} = CURRENT_DATE;
        $insert{'tn_ext'} = $fs[$path]['tn_ext'];
        $insert{'has_high'} = $fs[$path]['has_high'];
        $insert{'path'} = $path;

        array_push($inserts, $insert);
        array_push($insert_links,
                   array('image_id' => $insert{'id'},
                         'category_id' => $insert{'storage_category_id'}));
        array_push($infos, array('path' => $insert{'path'},
                                 'info' => l10n('update_research_added')));
      }
      else
      {
        array_push($errors, array('path' => $path, 'type' => 'PWG-UPDATE-2'));
      }
    }
    else
    {
      $insert{'id'} = $next_element_id++;
      $insert{'file'} = $filename;
      $insert{'storage_category_id'} = $db_fulldirs[$dirname];
      $insert{'date_available'} = CURRENT_DATE;
      $insert{'has_high'} = $fs[$path]['has_high'];
      $insert{'path'} = $path;

      if ( isset($fs[$path]['tn_ext']) )
      {
        $insert{'tn_ext'} = $fs[$path]['tn_ext'];
      }
      if (isset($fs[$path]['representative_ext']))
      {
        $insert{'representative_ext'} = $fs[$path]['representative_ext'];
      }

      array_push($inserts, $insert);
      array_push($insert_links,
                 array('image_id' => $insert{'id'},
                       'category_id' => $insert{'storage_category_id'}));
      array_push($infos, array('path' => $insert{'path'},
                               'info' => l10n('update_research_added')));
    }
  }

  if (count($inserts) > 0)
  {
    if (!$simulate)
    {
      // inserts all new elements
      $dbfields = array(
        'id','file','storage_category_id','date_available','tn_ext'
        ,'representative_ext', 'has_high', 'path'
        );
      mass_inserts(IMAGES_TABLE, $dbfields, $inserts);

      // insert all links between new elements and their storage category
      $dbfields = array('image_id','category_id');
      mass_inserts(IMAGE_CATEGORY_TABLE, $dbfields, $insert_links);
    }
    $counts['new_elements'] = count($inserts);
  }

  // delete elements that are in database but not in the filesystem
  $to_delete_elements = array();
  foreach (array_diff($db_elements, array_keys($fs)) as $path)
  {
    array_push($to_delete_elements, array_search($path, $db_elements));
    array_push($infos, array('path' => $path,
                             'info' => l10n('update_research_deleted')));
  }
  if (count($to_delete_elements) > 0)
  {
    if (!$simulate)
    {
      delete_elements($to_delete_elements);
    }
    $counts['del_elements'] = count($to_delete_elements);
  }

  echo '<!-- scanning files : ';
  echo get_elapsed_time($start_files, get_moment());
  echo ' -->'."\n";

  // retrieving informations given by uploaders
  if (!$simulate and count($cat_ids) > 0)
  {
    $query = '
SELECT id,file,storage_category_id,infos
  FROM '.WAITING_TABLE.'
  WHERE storage_category_id IN (
'.wordwrap(implode(', ', $cat_ids), 80, "\n").')
    AND validated = \'true\'
;';
    $result = pwg_query($query);

    $datas = array();
    $fields =
      array(
        'primary' => array('id'),
        'update'  => array('date_creation', 'author', 'name', 'comment')
        );

    $waiting_to_delete = array();

    while ($row = mysql_fetch_array($result))
    {
      $data = array();

      $query = '
SELECT id
  FROM '.IMAGES_TABLE.'
  WHERE storage_category_id = \''.$row['storage_category_id'].'\'
    AND file = \''.$row['file'].'\'
;';
      list($data['id']) = mysql_fetch_array(pwg_query($query));

      foreach ($fields['update'] as $field)
      {
        $data[$field] = getAttribute($row['infos'], $field);
      }

      array_push($datas, $data);
      array_push($waiting_to_delete, $row['id']);
    }

    if (count($datas) > 0)
    {
      mass_updates(IMAGES_TABLE, $fields, $datas);

      // delete now useless waiting elements
      $query = '
DELETE
  FROM '.WAITING_TABLE.'
  WHERE id IN ('.implode(',', $waiting_to_delete).')
;';
      pwg_query($query);
    }
  }
}

// +-----------------------------------------------------------------------+
// |                          synchronize files                            |
// +-----------------------------------------------------------------------+
if (isset($_POST['submit'])
    and ($_POST['sync'] == 'dirs' or $_POST['sync'] == 'files'))
{
  $template->assign_block_vars(
    'update_result',
    array(
      'NB_NEW_CATEGORIES'=>$counts['new_categories'],
      'NB_DEL_CATEGORIES'=>$counts['del_categories'],
      'NB_NEW_ELEMENTS'=>$counts['new_elements'],
      'NB_DEL_ELEMENTS'=>$counts['del_elements'],
      'NB_ERRORS'=>count($errors),
      ));

  if (!$simulate)
  {
    $start = get_moment();
    update_category('all');
    echo '<!-- update_category(all) : ';
    echo get_elapsed_time($start,get_moment());
    echo ' -->'."\n";
    $start = get_moment();
    ordering();
    update_global_rank();
    echo '<!-- ordering categories : ';
    echo get_elapsed_time($start, get_moment());
    echo ' -->'."\n";
  }
}

// +-----------------------------------------------------------------------+
// |                          synchronize metadata                         |
// +-----------------------------------------------------------------------+
if (isset($_POST['submit']) and preg_match('/^metadata/', $_POST['sync']) 
         and !$general_failure)
{
  // sync only never synchronized files ?
  if ($_POST['sync'] == 'metadata_new')
  {
    $opts['only_new'] = true;
  }
  else
  {
    $opts['only_new'] = false;
  }
  $opts['category_id'] = '';
  $opts['recursive'] = true;

  if (isset($_POST['cat']))
  {
    $opts['category_id'] = $_POST['cat'];
    // recursive ?
    if (!isset($_POST['subcats-included']) or $_POST['subcats-included'] != 1)
    {
      $opts['recursive'] = false;
    }
  }
  $start = get_moment();
  $files = get_filelist($opts['category_id'], $site_id,
                        $opts['recursive'],
                        $opts['only_new']);

  echo '<!-- get_filelist : ';
  echo get_elapsed_time($start, get_moment());
  echo ' -->'."\n";

  $start = get_moment();
  $datas = array();
  foreach ( $files as $id=>$file )
  {
    $data = $site_reader->get_element_update_attributes($file);
    if ( is_array($data) )
    {
      $data['date_metadata_update'] = CURRENT_DATE;
      $data['id']=$id;
      array_push($datas, $data);
    }
    else
    {
      array_push($errors, array('path' => $file, 'type' => 'PWG-ERROR-NO-FS'));
    }
  }
  $update_fields = $site_reader->get_update_attributes();
  $update_fields = array_merge($update_fields, 'date_metadata_update');
  $fields =
      array(
        'primary' => array('id'),
        'update'  => array_unique($update_fields)
        );
  //print_r($datas);
  if (!$simulate and count($datas)>0 )
  {
    mass_updates(IMAGES_TABLE, $fields, $datas);
  }
  
  echo '<!-- metadata update : ';
  echo get_elapsed_time($start, get_moment());
  echo ' -->'."\n";

  $template->assign_block_vars(
    'metadata_result',
    array(
      'NB_ELEMENTS_DONE' => count($datas),
      'NB_ELEMENTS_CANDIDATES' => count($files),
      'NB_ERRORS' => count($errors),
      ));
}

// +-----------------------------------------------------------------------+
// |                        template initialization                        |
// +-----------------------------------------------------------------------+
$template->set_filenames(array('update'=>'admin/site_update.tpl'));
$result_title = '';
if (isset($simulate) and $simulate)
{
  $result_title.= l10n('update_simulation_title').' ';
}

// used_metadata string is displayed to inform admin which metadata will be
// used from files for synchronization
$used_metadata = implode( ', ', $site_reader->get_update_attributes());
if ($site_is_remote and !isset($_POST['submit']) )
{
  $used_metadata.= ' + ' . l10n('Aditionnal remote attributes');
}

$template->assign_vars(
  array(
    'SITE_URL'=>$site_url,
    'U_SITE_MANAGER'=> PHPWG_ROOT_PATH.'admin.php?page=site_manager',
    'L_RESULT_UPDATE'=>$result_title.l10n('update_part_research'),
    'L_RESULT_METADATA'=>$result_title.l10n('update_result_metadata'),
    'METADATA_LIST' => $used_metadata
    ));

$template->assign_vars(
  array(
    'U_HELP' => PHPWG_ROOT_PATH.'/popuphelp.php?page=synchronize'
    )
  );
// +-----------------------------------------------------------------------+
// |                        introduction : choices                         |
// +-----------------------------------------------------------------------+
if (!isset($_POST['submit']) or (isset($simulate) and $simulate))
{
  $template->assign_block_vars('introduction', array());

  if (isset($simulate) and $simulate)
  {
    switch ($_POST['sync'])
    {
      case 'dirs' :
      {
        $template->assign_vars(
          array('SYNC_DIRS_CHECKED'=>'checked="checked"'));
        break;
      }
      case 'files' :
      {
        $template->assign_vars(
          array('SYNC_ALL_CHECKED'=>'checked="checked"'));
        break;
      }
      case 'metadata_new' :
      {
        $template->assign_vars(
          array('SYNC_META_NEW_CHECKED'=>'checked="checked"'));
        break;
      }
      case 'metadata_all' :
      {
        $template->assign_vars(
          array('SYNC_META_ALL_CHECKED'=>'checked="checked"'));
        break;
      }
    }

    if (isset($_POST['display_info']) and $_POST['display_info'] == 1)
    {
      $template->assign_vars(
        array('DISPLAY_INFO_CHECKED'=>'checked="checked"'));
    }

    if (isset($_POST['subcats-included']) and $_POST['subcats-included'] == 1)
    {
      $template->assign_vars(
        array('SUBCATS_INCLUDED_CHECKED'=>'checked="checked"'));
    }

    if (isset($_POST['cat']) and is_numeric($_POST['cat']))
    {
      $cat_selected = array($_POST['cat']);
    }
    else
    {
      $cat_selected = array();
    }
  }
  else
  {
    $template->assign_vars(
      array('SYNC_DIRS_CHECKED' => 'checked="checked"',
            'SUBCATS_INCLUDED_CHECKED'=>'checked="checked"'));

    $cat_selected = array();
  }

  $query = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE site_id = '.$site_id.'
;';
  display_select_cat_wrapper($query,
                             $cat_selected,
                             'introduction.category_option',
                             false);
}

if (count($errors) > 0)
{
  $template->assign_block_vars('sync_errors', array());
  foreach ($errors as $error)
  {
    $template->assign_block_vars(
      'sync_errors.error',
      array(
        'ELEMENT' => $error['path'],
        'LABEL' => $error['type'].' ('.$error_labels[$error['type']][0].')'
        ));
  }

  foreach ($error_labels as $error_type=>$error_description)
  {
    $template->assign_block_vars(
      'sync_errors.error_caption',
      array(
        'TYPE' => $error_type,
        'LABEL' => $error_description[1]
        ));
  }

}
if (count($infos) > 0
    and isset($_POST['display_info'])
    and $_POST['display_info'] == 1)
{
  $template->assign_block_vars('sync_infos', array());
  foreach ($infos as $info)
  {
    $template->assign_block_vars(
      'sync_infos.info',
      array(
        'ELEMENT' => $info['path'],
        'LABEL' => $info['info']
        ));
  }
}

// +-----------------------------------------------------------------------+
// |                          sending html code                            |
// +-----------------------------------------------------------------------+
$template->assign_var_from_handle('ADMIN_CONTENT', 'update');
?>