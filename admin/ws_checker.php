<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2006 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date: 2006-12-15 23:16:37 +0200 (ven., 15 dec. 2006) $
// | last modifier : $Author: vdigital $
// | revision      : $Revision: 1658 $
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
// |                             functions                                 |
// +-----------------------------------------------------------------------+

// Function to migrate be useful for ws
function official_req()
{
return array(
    'random'                              /* Random order */
  , 'list'               /* list on MBt & z0rglub request */
  , 'maxviewed'             /* hit > 0 and hit desc order */
  , 'recent'        /* recent = Date_available desc order */
  , 'highrated'            /* avg_rate > 0 and desc order */
  , 'oldest'                  /* Date_available asc order */
  , 'lessviewed'                         /* hit asc order */
  , 'lowrated'                      /* avg_rate asc order */
  , 'undescribed'                  /* description missing */
  , 'unnamed'                         /* new name missing */
  , 'portraits'     /* width < height (portrait oriented) */
  , 'landscapes'   /* width > height (landscape oriented) */
  , 'squares'             /* width ~ height (square form) */
);
}

function expand_id_list($ids)
{
    $tid = array();
    foreach ( $ids as $id )
    {
      if ( is_numeric($id) )
      {
        $tid[] = (int) $id;
      }
      else
      {
        $range = explode( '-', $id );
        if ( is_numeric($range[0]) and is_numeric($range[1]) )
        {
          $from = min($range[0],$range[1]);
          $to = max($range[0],$range[1]);
          for ($i = $from; $i <= $to; $i++) 
          {
            $tid[] = (int) $i;
          }
        }
      }
    }
    $result = array_unique ($tid); // remove duplicates...
    sort ($result);
    return $result;
}

function check_target($list)
{
  if ( $list !== '' )
  {
    $type = explode('/',$list); // Find type list
    if ( !in_array($type[0],array('list','cat','tag') ) )
    {
      $type[0] = 'list'; // Assume an id list
    } 
    $ids = explode( ',',$type[1] );
    $list = $type[0] . '/';

    // 1,2,21,3,22,4,5,9-12,6,11,12,13,2,4,6,

    $result = expand_id_list( $ids ); 

    // 1,2,3,4,5,6,9,10,11,12,13,21,22, 
    // I would like
    // 1-6,9-13,21-22
    $serial[] = $result[0]; // To be shifted                      
    foreach ($result as $k => $id)
    {
      $next_less_1 = (isset($result[$k + 1]))? $result[$k + 1] - 1:-1;
      if ( $id == $next_less_1 and end($serial)=='-' )
      { // nothing to do 
      }
      elseif ( $id == $next_less_1 )
      {
        $serial[]=$id;
        $serial[]='-';
      }
      else
      {
        $serial[]=$id;  // end serie or non serie
      }
    }
    $null = array_shift($serial); // remove first value
    $list .= array_shift($serial); // add the real first one
    $separ = ',';
    foreach ($serial as $id)
    {
      $list .= ($id=='-') ? '' : $separ . $id;
      $separ = ($id=='-') ? '-':','; // add comma except if hyphen
    }
  }
  return $list;
}

// Next evolution... 
// Out of parameter WS management
// The remainer objective is to check 
//  -  Does Web Service working properly?
//  -  Does any access return something really?
//     Give a way to check to the webmaster...
// These questions are one of module name explainations (checker).

if((!defined("PHPWG_ROOT_PATH")) or (!$conf['allow_web_services']))
{
  die('Hacking attempt!');
}
include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);


// FIXME would be in migration process but could stay here 
// Config parameters
if (!isset($conf['ws_status']))
{
  $conf['ws_status'] = false;

  $query = '
  INSERT INTO '.CONFIG_TABLE.'
    (param,value,comment)
    VALUES
  (\'ws_status\', \'false\', \'Web Service status\' )
  ;';
  pwg_query($query);
}

// accepted queries
$req_type_list = official_req();


//--------------------------------------------------------- update informations

// Is status temporary changed?
if (isset($_POST['wss_submit']))
{
  $ws_status = get_boolean( $_POST['ws_status'] );      // Requested status
  $ws_update = $lang['ws_success_upd'];  // Normal update
  if ($conf['allow_web_services'] == false and $ws_status == true )
  { /* Set true is disallowed */
    $ws_status = false;
    $ws_update = $lang['ws_disallowed'];
  }
  if ( $ws_status !== true and $ws_status !== false )
  { /* Avoiding SQL injection by no change */
    $ws_status = $conf['ws_status'];
  }
  if ($conf['ws_status'] == $ws_status)
  {
    $ws_update = $lang['ws_disallowed'];
  }
  else
  {
    $query = '
UPDATE '.CONFIG_TABLE.' SET
 value = \''.boolean_to_string($ws_status).'\'
WHERE param = \'ws_status\' 
 AND value <> \''.boolean_to_string($ws_status).'\' 
;';
    pwg_query($query);
    $conf['ws_status'] = $ws_status;
  }
  $template->assign_block_vars(
    'update_result',
    array(
      'UPD_ELEMENT'=> $lang['ws_set_status'].': '.$ws_update,
      )
  );
}

// Next, is a new access required?

if (isset($_POST['wsa_submit']))
{
// Check $_post
$add_partner = htmlspecialchars( $_POST['add_partner'], ENT_QUOTES);
$add_access = check_target( $_POST['add_access']) ;
$add_start = ( is_numeric($_POST['add_start']) ) ? $_POST['add_start']:0; 
$add_end = ( is_numeric($_POST['add_end']) ) ? $_POST['add_end']:0;
$add_request = ( ctype_alpha($_POST['add_request']) ) ?
  $_POST['add_request']:'';
$add_high = ( $_POST['add_high'] == 'true' ) ? 'true':'false';
$add_normal = ( $_POST['add_normal'] == 'true' ) ? 'true':'false';
$add_limit = ( is_numeric($_POST['add_limit']) ) ? $_POST['add_limit']:1; 
$add_comment = htmlspecialchars( $_POST['add_comment'], ENT_QUOTES);
if ( strlen($add_partner) < 8 )
{
}
  $query = '
INSERT INTO '.WEB_SERVICES_ACCESS_TABLE.' 
( `name` , `access` , `start` , `end` , `request` , 
  `high` , `normal` , `limit` , `comment` ) 
VALUES (' . "
  '$add_partner', '$add_access',
  ADDDATE( NOW(), INTERVAL $add_start DAY),
  ADDDATE( NOW(), INTERVAL $add_end DAY),
  '$add_request', '$add_high', '$add_normal', '$add_limit', '$add_comment' );";

  pwg_query($query);
  
  $template->assign_block_vars(
    'update_result',
    array(
      'UPD_ELEMENT'=> $lang['ws_adding_legend'].$lang['ws_success_upd'],
      )
  );
}

// Next, Update selected access
if (isset($_POST['wsu_submit']))
{
  $upd_end = ( is_numeric($_POST['upd_end']) ) ? $_POST['upd_end']:0;
  $settxt = ' end = ADDDATE(NOW(), INTERVAL '. $upd_end .' DAY)';

  if ((isset($_POST['selection'])) and (trim($settxt) != ''))
  {
    $uid = (int) $_POST['selection'];
    $query = '
    UPDATE '.WEB_SERVICES_ACCESS_TABLE.' 
    SET '.$settxt.'
    WHERE id = '.$uid.'; ';
    pwg_query($query);
    $template->assign_block_vars(
      'update_result',
      array(
        'UPD_ELEMENT'=> $lang['ws_update_legend'].$lang['ws_success_upd'],
        )
    );
  } else {
    $template->assign_block_vars(
      'update_result',
      array(
        'UPD_ELEMENT'=> $lang['ws_update_legend'].$lang['ws_failed_upd'],
        )
    );
  }
}
// Next, Delete selected access

if (isset($_POST['wsX_submit']))
{
  if ((isset($_POST['delete_confirmation']))
   and (isset($_POST['selection'])))
  {
    $uid = (int) $_POST['selection'];
    $query = 'DELETE FROM '.WEB_SERVICES_ACCESS_TABLE.'
               WHERE id = '.$uid.'; ';
    pwg_query($query);
    $template->assign_block_vars(
      'update_result',
      array(
        'UPD_ELEMENT'=> $lang['ws_delete_legend'].$lang['ws_success_upd'],
        )
    );
  } else {
    $template->assign_block_vars(
      'update_result',
      array(
        'UPD_ELEMENT'=> $lang['Not selected / Not confirmed']
        .$lang['ws_failed_upd'],
        )
    );
  } 
}


$ws_status = $conf['ws_status'];
$template->assign_vars(
  array(
    'L_CURRENT_STATUS' => ( $ws_status == true ) ? 
       $lang['ws_enable']:$lang['ws_disable'],
    'STATUS_YES' => ( $ws_status == true ) ? '':'checked', 
    'STATUS_NO' => ( $ws_status == true ) ? 'checked':'', 
    'DEFLT_HIGH_YES' => '',
    'DEFLT_HIGH_NO' => 'checked',
    'DEFLT_NORMAL_YES' => '',
    'DEFLT_NORMAL_NO' => 'checked',
    'U_HELP' => PHPWG_ROOT_PATH.'popuphelp.php?page=web_service',    
    )
  );

// Build where
$where = '';
$order = ' ORDER BY `id` DESC' ;

$query = '
SELECT *
  FROM '.WEB_SERVICES_ACCESS_TABLE.'
WHERE 1=1  '
.$where.
' '
.$order.
';';
$result = pwg_query($query);
$acc_list = mysql_num_rows($result);
$result = pwg_query($query);
// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

$template->set_filenames(
  array(
    'ws_checker' => 'admin/ws_checker.tpl'
    )
  );

$checked = 'checked="checked"';
$selected = 'selected="selected"';
$num=0;
if ( $acc_list > 0 )
{
  $template->assign_block_vars(
    'acc_list', array() );
}

// Access List
while ($row = mysql_fetch_array($result))
{
  $num++;
  $template->assign_block_vars(
    'acc_list.access',
     array(
       'CLASS' => ($num % 2 == 1) ? 'row1' : 'row2',
       'ID'               => $row['id'],
       'NAME'             => 
         (is_adviser()) ? '*********' : $row['name'],       
       'ACCESS'           => $row['access'],
       'START'            => $row['start'],
       'END'              => $row['end'],
       'FORCE'            => $row['request'],
       'HIGH'             => $row['high'],
       'NORMAL'           => $row['normal'],
       'LIMIT'            => $row['limit'],
       'COMMENT'          => $row['comment'],
       'SELECTED'         => '',
     )
  );
}

$template->assign_block_vars(
  'add_request',
   array(
     'VALUE'=> '',
     'CONTENT' => '',
     'SELECTED' => $selected,
   )
);
foreach ($req_type_list as $value) {

  $template->assign_block_vars(
    'add_request',
     array(
       'VALUE'=> $value,
       'CONTENT' => $lang['ws_'.$value],
       'SELECTED' => '',
     )
  );
}

$columns = array (
       'ID'               => 'id',
       'ws_KeyName'       => 'name', 
       'ws_Access'        => 'ws_access',
       'ws_Start'         => 'ws_start',
       'ws_End'           => 'ws_end',
       'ws_Request'       => 'ws_request',
       'ws_High'          => 'ws_high',
       'ws_Normal'        => 'ws_normal',
       'ws_Limit'         => 'ws_limit',
       'ws_Comment'       => 'ws_comment',
);

foreach ($conf['ws_allowed_limit'] as $value) {
  $template->assign_block_vars(
    'add_limit',
     array(
       'VALUE'=> $value,
       'CONTENT' => $value,
       'SELECTED' => ($conf['ws_allowed_limit'][0] == $value) ? $selected:'',
     )
  );
}

// Postponed Start Date 
// By default 0, 1, 2, 3, 5, 7, 14 or 30 days
foreach ($conf['ws_postponed_start'] as $value) {
  $template->assign_block_vars(
    'add_start',
     array(
       'VALUE'=> $value,
       'CONTENT' => $value,
       'SELECTED' => ($conf['ws_postponed_start'][0] == $value) ? $selected:'',
     )
  );
}

// Durations (Allowed Web Services Period)
// By default 10, 5, 2, 1 year(s) or 6, 3, 1 month(s) or 15, 10, 7, 5, 1, 0 day(s)
foreach ($conf['ws_durations'] as $value) {
  $template->assign_block_vars(
    'add_end',
     array(
       'VALUE'=> $value,
       'CONTENT' => $value,
       'SELECTED' => ($conf['ws_durations'][3] == $value) ? $selected:'',
     )
  );
  if ( $acc_list > 0 )
  {
    $template->assign_block_vars(
      'acc_list.upd_end',
       array(
         'VALUE'=> $value,
         'CONTENT' => $value,
         'SELECTED' => ($conf['ws_durations'][3] == $value) ? $selected:'',
       )
    );
  }
}

//----------------------------------------------------------- sending html code

$template->assign_var_from_handle('ADMIN_CONTENT', 'ws_checker');
?>
