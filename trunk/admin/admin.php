<?php
/***************************************************************************
 *                                 admin.php                               *
 *                            -------------------                          *
 *   application          : PhpWebGallery 1.3                              *
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

//----------------------------------------------------------- personnal include
include_once( './include/isadmin.inc.php' );
//----------------------------------------------------- template initialization
$vtp = new VTemplate;
$handle = $vtp->Open( '../template/'.$user['template'].'/admin/admin.vtp' );
// language
$vtp->setGlobalVar( $handle, 'page_title',  $lang['title_default'] );
$vtp->setGlobalVar( $handle, 'menu_title',  $lang['menu_title'] );
//--------------------------------------- validating page and creation of title
$page_valide = false;
switch ( $_GET['page'] )
{
 case 'user_add':
   $titre = $lang['title_add'];           $page_valide = true; break;
 case 'user_list':
   $titre = $lang['title_liste_users'];   $page_valide = true; break;
 case 'user_modify':
   $titre = $lang['title_modify'];        $page_valide = true; break;
 case 'historique':
   $titre = $lang['title_history'];       $page_valide = true; break;
 case 'update':
   $titre = $lang['title_update'];        $page_valide = true; break;
 case 'configuration':
   $titre = $lang['title_configuration']; $page_valide = true; break;
 case 'manuel':
   $titre = $lang['title_instructions'];  $page_valide = true; break;
 case 'perm':
   $titre = $lang['title_permissions'];   $page_valide = true; break;
 case 'cat':
   $titre = $lang['title_categories'];    $page_valide = true; break;
 case 'edit_cat':
   $titre = $lang['title_edit_cat'];      $page_valide = true; break;
 case 'infos_images':
   $titre = $lang['title_info_images'];   $page_valide = true; break;
 case 'waiting':
   $titre = $lang['title_waiting'];       $page_valide = true; break;
 case 'thumbnail':
   $titre = $lang['title_thumbnails'];
   if ( isset( $_GET['dir'] ) )
   {
     $titre.= ' '.$lang['title_thumbnails_2'].' <span style="color:#006699;">';
     // $_GET['dir'] contient :
     // ../galleries/vieux_lyon ou
     // ../galleries/vieux_lyon/visite ou
     // ../galleries/vieux_lyon/visite/truc ...
     $dir = explode( "/", $_GET['dir'] );
     $titre.= $dir[2];
     for ( $i = 3; $i < sizeof( $dir ) - 1; $i++ )
     {
       $titre.= ' &gt; '.$dir[$i];
     }
     $titre.= "</span>";
   }
   $page_valide = true;
   break;
 default:
   $titre = $lang['title_default']; break;
}
$vtp->setGlobalVar( $handle, 'title', $titre );
//--------------------------------------------------------------------- summary
$link_start = './admin.php?page=';
// configuration
$vtp->addSession( $handle, 'summary' );
$vtp->setVar( $handle, 'summary.indent', '' );
$vtp->setVar( $handle, 'summary.link',
              add_session_id( $link_start.'configuration' ) );
$vtp->setVar( $handle, 'summary.name', $lang['menu_config'] );
$vtp->closeSession( $handle, 'summary' );
// users
$vtp->addSession( $handle, 'summary' );
$vtp->setVar( $handle, 'summary.indent', '' );
$vtp->setVar( $handle, 'summary.link',
              add_session_id( $link_start.'liste_users' ) );
$vtp->setVar( $handle, 'summary.name', $lang['menu_users'] );
$vtp->closeSession( $handle, 'summary' );
// user list
$vtp->addSession( $handle, 'summary' );
$vtp->setVar( $handle, 'summary.indent', '&nbsp;&nbsp;' );
$vtp->setVar( $handle, 'summary.link',
              add_session_id( $link_start.'user_list' ) );
$vtp->setVar( $handle, 'summary.name', $lang['menu_list_user'] );
$vtp->closeSession( $handle, 'summary' );
// user add
$vtp->addSession( $handle, 'summary' );
$vtp->setVar( $handle, 'summary.indent', '&nbsp;&nbsp;' );
$vtp->setVar(
  $handle, 'summary.link', add_session_id( $link_start.'user_add' ) );
$vtp->setVar( $handle, 'summary.name', $lang['menu_add_user'] );
$vtp->closeSession( $handle, 'summary' );
// categories
$vtp->addSession( $handle, 'summary' );
$vtp->setVar( $handle, 'summary.indent', '' );
$vtp->setVar( $handle, 'summary.link', add_session_id( $link_start.'cat' ) );
$vtp->setVar( $handle, 'summary.name', $lang['menu_categories'] );
$vtp->closeSession( $handle, 'summary' );
// waiting
$vtp->addSession( $handle, 'summary' );
$vtp->setVar( $handle, 'summary.indent', '' );
$vtp->setVar( $handle, 'summary.link',
              add_session_id( $link_start.'waiting' ) );
$query = 'select id from '.PREFIX_TABLE.'waiting;';
$result = mysql_query( $query );
$nb_waiting = '';
if ( mysql_num_rows( $result ) > 0 )
{
  $nb_waiting =  ' [ '.mysql_num_rows( $result ).' ]';
}
$vtp->setVar( $handle, 'summary.name', $lang['menu_waiting'].$nb_waiting );
$vtp->closeSession( $handle, 'summary' );
// update
$vtp->addSession( $handle, 'summary' );
$vtp->setVar( $handle, 'summary.indent', '' );
$vtp->setVar( $handle, 'summary.link',
              add_session_id( $link_start.'update' ) );
$vtp->setVar( $handle, 'summary.name', $lang['menu_update'] );
$vtp->closeSession( $handle, 'summary' );
// thumbnails
$vtp->addSession( $handle, 'summary' );
$vtp->setVar( $handle, 'summary.indent', '' );
$vtp->setVar( $handle, 'summary.link',
              add_session_id( $link_start.'thumbnail' ) );
$vtp->setVar( $handle, 'summary.name', $lang['menu_thumbnails'] );
$vtp->closeSession( $handle, 'summary' );
// history
$vtp->addSession( $handle, 'summary' );
$vtp->setVar( $handle, 'summary.indent', '' );
$vtp->setVar( $handle, 'summary.link',
              add_session_id( $link_start.'historique' ) );
$vtp->setVar( $handle, 'summary.name', $lang['menu_history'] );
$vtp->closeSession( $handle, 'summary' );
// instructions
$vtp->addSession( $handle, 'summary' );
$vtp->setVar( $handle, 'summary.indent', '' );
$vtp->setVar( $handle, 'summary.link',
              add_session_id( $link_start.'manuel' ) );
$vtp->setVar( $handle, 'summary.name', $lang['menu_instructions'] );
$vtp->closeSession( $handle, 'summary' );
// back to thumbnails page
$vtp->addSession( $handle, 'summary' );
$vtp->setVar( $handle, 'summary.indent', '' );
$vtp->setVar( $handle, 'summary.link', add_session_id( '../category.php' ) );
$vtp->setVar( $handle, 'summary.name', $lang['menu_back'] );
$vtp->closeSession( $handle, 'summary' );
//------------------------------------------------------------- content display
if ( $page_valide )
{
  include ( $_GET['page'].'.php' );
}
else
{
  $vtp->setVar(
    $handle, 'sub',
    '<div style="text-align:center">'.$lang['default_message'].'</div>' );
}
//----------------------------------------------------------- html code display
$code = $vtp->Display( $handle, 0 );
echo $code;
?>