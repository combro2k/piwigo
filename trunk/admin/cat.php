<?php
/***************************************************************************
 *                                  cat.php                                *
 *                            -------------------                          *
 *   application          : PhpWebGallery 1.3                              *
 *   website              : http://www.phpwebgallery.net                   *
 *   author               : Pierrick LE GALL <pierrick@z0rglub.com>        *
 *                                                                         *
 ***************************************************************************/

/***************************************************************************
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation;                                         *
 *                                                                         *
 ***************************************************************************/
include_once( './include/isadmin.inc.php' );
//----------------------------------------------------- template initialization
$sub = $vtp->Open( '../template/'.$user['template'].'/admin/cat.vtp' );
// language
$vtp->setGlobalVar( $sub, 'cat_edit',        $lang['cat_edit'] );
$vtp->setGlobalVar( $sub, 'cat_up',          $lang['cat_up'] );
$vtp->setGlobalVar( $sub, 'cat_down',        $lang['cat_down'] );
$vtp->setGlobalVar( $sub, 'cat_image_info',  $lang['cat_image_info'] );
$vtp->setGlobalVar( $sub, 'cat_permission',  $lang['cat_permission'] );
$vtp->setGlobalVar( $sub, 'cat_update',      $lang['cat_update'] );
//---------------------------------------------------------------  rank updates
if ( isset( $_GET['up'] ) && is_numeric( $_GET['up'] ) )
{
  // 1. searching level (id_uppercat)
  //    and rank of the category to move
  $query = 'select id_uppercat,rank';
  $query.= ' from '.PREFIX_TABLE.'categories';
  $query.= ' where id = '.$_GET['up'];
  $query.= ';';
  $row = mysql_fetch_array( mysql_query( $query ) );
  $level = $row['id_uppercat'];
  $rank  = $row['rank'];
  // 2. searching the id and the rank of the category
  //    just above at the same level
  $query = 'select id,rank';
  $query.= ' from '.PREFIX_TABLE.'categories';
  $query.= ' where rank < '.$rank;
  if ( $level == '' )
  {
    $query.= ' and id_uppercat is null';
  }
  else
  {
    $query.= ' and id_uppercat = '.$level;
  }
  $query.= ' order by rank desc';
  $query.= ' limit 0,1';
  $query.= ';';
  $row = mysql_fetch_array( mysql_query( $query ) );
  $new_rank     = $row['rank'];
  $replaced_cat = $row['id'];
  // 3. exchanging ranks between the two categories
  $query = 'update '.PREFIX_TABLE.'categories';
  $query.= ' set rank = '.$new_rank;
  $query.= ' where id = '.$_GET['up'];
  $query.= ';';
  mysql_query( $query );
  $query = 'update '.PREFIX_TABLE.'categories';
  $query.= ' set rank = '.$rank;
  $query.= ' where id = '.$replaced_cat;
  $query.= ';';
  mysql_query( $query );
}
if ( isset( $_GET['down'] ) && is_numeric( $_GET['down'] ) )
{
  // 1. searching level (id_uppercat)
  //    and rank of the category to move
  $query = 'select id_uppercat,rank';
  $query.= ' from '.PREFIX_TABLE.'categories';
  $query.= ' where id = '.$_GET['down'];
  $query.= ';';
  $row = mysql_fetch_array( mysql_query( $query ) );
  $level = $row['id_uppercat'];
  $rank  = $row['rank'];
  // 2. searching the id and the rank of the category
  //    just below at the same level
  $query = 'select id,rank';
  $query.= ' from '.PREFIX_TABLE.'categories';
  $query.= ' where rank > '.$rank;
  if ( $level == '' )
  {
    $query.= ' and id_uppercat is null';
  }
  else
  {
    $query.= ' and id_uppercat = '.$level;
  }
  $query.= ' order by rank asc';
  $query.= ' limit 0,1';
  $query.= ';';
  $row = mysql_fetch_array( mysql_query( $query ) );
  $new_rank     = $row['rank'];
  $replaced_cat = $row['id'];
  // 3. exchanging ranks between the two categories
  $query = 'update '.PREFIX_TABLE.'categories';
  $query.= ' set rank = '.$new_rank;
  $query.= ' where id = '.$_GET['down'];
  $query.= ';';
  mysql_query( $query );
  $query = 'update '.PREFIX_TABLE.'categories';
  $query.= ' set rank = '.$rank;
  $query.= ' where id = '.$replaced_cat;
  $query.= ';';
  mysql_query( $query );
}
//------------------------------------------------------------------ reordering
function ordering( $id_uppercat )
{
  $rank = 1;
		
  $query = 'select id';
  $query.= ' from '.PREFIX_TABLE.'categories';
  if ( !is_numeric( $id_uppercat ) )
  {
    $query.= ' where id_uppercat is NULL';
  }
  else
  {
    $query.= ' where id_uppercat = '.$id_uppercat;
  }
  $query.= ' order by rank asc, dir asc';
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    $query = 'update '.PREFIX_TABLE.'categories';
    $query.= ' set rank = '.$rank;
    $query.= ' where id = '.$row['id'];
    $query.= ';';
    mysql_query( $query );
    $rank++;
    ordering( $row['id'] );
  }
}
	
ordering( 'NULL' );
//----------------------------------------------------affichage de la page
function display_cat_manager( $id_uppercat, $indent,
                              $uppercat_visible, $level )
{
  global $lang,$conf,$sub,$vtp;
		
  // searching the min_rank and the max_rank of the category
  $query = 'select min(rank) as min, max(rank) as max';
  $query.= ' from '.PREFIX_TABLE.'categories';
  if ( !is_numeric( $id_uppercat ) )
  {
    $query.= ' where id_uppercat is NULL';
  }
  else
  {
    $query.= ' where id_uppercat = '.$id_uppercat;
  }
  $query.= ';';
  $result = mysql_query( $query );
  $row    = mysql_fetch_array( $result );
  $min_rank = $row['min'];
  $max_rank = $row['max'];
		
  // will we use <th> or <td> lines ?
  $td    = 'td';
  $class = '';
  if ( $level > 0 )
  {
    $class = 'row'.$level;
  }
  else
  {
    $td = 'th';
  }
		
  $query = 'select id,name,dir,nb_images,status,rank,site_id';
  $query.= ' from '.PREFIX_TABLE.'categories';
  if ( !is_numeric( $id_uppercat ) )
  {
    $query.= ' where id_uppercat is NULL';
  }
  else
  {
    $query.= ' where id_uppercat = '.$id_uppercat;
  }
  $query.= ' order by rank asc';
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    $subcat_visible = true;

    $vtp->addSession( $sub, 'cat' );
    $vtp->setVar( $sub, 'cat.td', $td );
    $vtp->setVar( $sub, 'cat.class', $class );
    $vtp->setVar( $sub, 'cat.indent', $indent );
    if ( $row['name'] == '' )
    {
      $name = str_replace( '_', ' ', $row['dir'] );
    }
    else
    {
      $name = $row['name'];
    }
    $vtp->setVar( $sub, 'cat.name', $name );
    $vtp->setVar( $sub, 'cat.dir', $row['dir'] );
    if ( $row['status'] == 'invisible' || !$uppercat_visible )
    {
      $subcat_visible = false;
      $vtp->setVar( $sub, 'cat.invisible', $lang['cat_invisible'] );
    }
    $vtp->setVar( $sub, 'cat.nb_picture', $row['nb_images'] );
    $url = add_session_id( './admin.php?page=edit_cat&amp;cat='.$row['id'] );
    $vtp->setVar( $sub, 'cat.edit_url', $url );
    if ( $row['rank'] != $min_rank )
    {
      $vtp->addSession( $sub, 'up' );
      $url = add_session_id( './admin.php?page=cat&amp;up='.$row['id'] );
      $vtp->setVar( $sub, 'up.up_url', $url );
      $vtp->closeSession( $sub, 'up' );
    }
    else
    {
      $vtp->addSession( $sub, 'no_up' );
      $vtp->closeSession( $sub, 'no_up' );
    }
    if ( $row['rank'] != $max_rank )
    {
      $vtp->addSession( $sub, 'down' );
      $url = add_session_id( './admin.php?page=cat&amp;down='.$row['id'] );
      $vtp->setVar( $sub, 'down.down_url', $url );
      $vtp->closeSession( $sub, 'down' );
    }
    else
    {
      $vtp->addSession( $sub, 'no_down' );
      $vtp->closeSession( $sub, 'no_down' );
    }
    if ( $row['nb_images'] > 0 )
    {
      $vtp->addSession( $sub, 'image_info' );
      $url = add_session_id( './admin.php?page=infos_images&amp;cat_id='
                             .$row['id'] );
      $vtp->setVar( $sub, 'image_info.image_info_url', $url );
      $vtp->closeSession( $sub, 'image_info' );
    }
    else
    {
      $vtp->addSession( $sub, 'no_image_info' );
      $vtp->closeSession( $sub, 'no_image_info' );
    }
    $url = add_session_id( './admin.php?page=perm&amp;cat_id='.$row['id'] );
    $vtp->setVar( $sub, 'cat.permission_url', $url );
    if ( $row['site_id'] == 1 )
    {
      $vtp->addSession( $sub, 'update' );
      $url = add_session_id('./admin.php?page=update&amp;update='.$row['id']);
      $vtp->setVar( $sub, 'update.update_url', $url );
      $vtp->closeSession( $sub, 'update' );
    }
    else
    {
      $vtp->addSession( $sub, 'no_update' );
      $vtp->closeSession( $sub, 'no_update' );
    }

    $vtp->closeSession( $sub, 'cat' );

    display_cat_manager( $row['id'], $indent.str_repeat( '&nbsp', 4 ),
                         $subcat_visible, $level + 1 );
  }
}
display_cat_manager( 'NULL', str_repeat( '&nbsp', 4 ), true, 0 );
//----------------------------------------------------------- sending html code
$vtp->Parse( $handle , 'sub', $sub );
?>