<?php
// +-----------------------------------------------------------------------+
// |                               admin.php                               |
// +-----------------------------------------------------------------------+
// | application   : PhpWebGallery <http://phpwebgallery.net>              |
// | branch        : BSF (Best So Far)                                     |
// +-----------------------------------------------------------------------+
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

//----------------------------------------------------------- include
define('PHPWG_ROOT_PATH','./');
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );
include_once( './admin/include/isadmin.inc.php' );
//----------------------------------------------------- template initialization
$vtp = new VTemplate;
$handle = $vtp->Open( './template/'.$user['template'].'/admin.vtp' );
// language
$tpl = array( 'title_default','charset','install_warning' );
templatize_array( $tpl, 'lang', $handle );
//-------------------------------------------------- install.php still exists ?
if ( is_file( './install.php' ) )
{
  $vtp->addSession( $handle, 'install_warning' );
  $vtp->closeSession( $handle, 'install_warning' );
}
//--------------------------------------- validating page and creation of title
$page_valide = false;
$title = '';
if (isset( $_GET['page'] ))
switch ( $_GET['page'] )
{
 case 'user_list':
   $title = $lang['title_liste_users'];   $page_valide = true; break;
 case 'user_modify':
   $title = $lang['title_modify'];        $page_valide = true; break;
 case 'user_perm':
   if ( !is_numeric( $_GET['user_id'] ) ) $_GET['user_id'] = -1;
   $query = 'SELECT status,username';
   $query.= ' FROM '.USERS_TABLE;
   $query.= ' WHERE id = '.$_GET['user_id'];
   $query.= ';';
   $result = mysql_query( $query );
   if ( mysql_num_rows( $result ) > 0 )
   {
     $row = mysql_fetch_array( $result );
     $page['user_status']   = $row['status'];
     if ( $row['username'] == 'guest' ) $row['username'] = $lang['guest'];
     $page['user_username'] = $row['username'];
     $page_valide = true;
     $title = $lang['title_user_perm'].' "'.$page['user_username'].'"';
   }
   else
   {
     $page_valide = false;
   }
   break;
 case 'group_list' :
   $title = $lang['title_groups'];        $page_valide = true; break;
 case 'group_perm' :
   if ( !is_numeric( $_GET['group_id'] ) ) $_GET['group_id'] = -1;
   $query = 'SELECT name';
   $query.= ' FROM '.PREFIX_TABLE.'groups';
   $query.= ' WHERE id = '.$_GET['group_id'];
   $query.= ';';
   $result = mysql_query( $query );
   if ( mysql_num_rows( $result ) > 0 )
   {
     $row = mysql_fetch_array( $result );
     $title = $lang['title_group_perm'].' "'.$row['name'].'"';
     $page_valide = true;
   }
   else
   {
     $page_valide = false;
   }
   break;
 case 'stats':
   $title = $lang['title_history'];       $page_valide = true; break;
 case 'update':
   $title = $lang['title_update'];        $page_valide = true; break;
 case 'configuration':
   $title = $lang['title_configuration']; $page_valide = true; break;
 case 'help':
   $title = $lang['title_instructions'];  $page_valide = true; break;
 case 'cat_perm':
   $title = $lang['title_cat_perm'];
   if ( isset( $_GET['cat_id'] ) )
   {
     check_cat_id( $_GET['cat_id'] );
     if ( isset( $page['cat'] ) and is_numeric( $page['cat'] ) )
     {
       $result = get_cat_info( $page['cat'] );
       $name = get_cat_display_name( $result['name'],' &gt; ', '' );
       $title.= ' "'.$name.'"';
     }
   }
   $page_valide = true;
   break;
 case 'cat_list':
   $title = $lang['title_categories'];    $page_valide = true; break;
 case 'cat_modify':
   $title = $lang['title_edit_cat'];      $page_valide = true; break;
 case 'infos_images':
   $title = $lang['title_info_images'];   $page_valide = true; break;
 case 'waiting':
   $title = $lang['title_waiting'];       $page_valide = true; break;
 case 'thumbnail':
   $title = $lang['title_thumbnails'];
   if ( isset( $_GET['dir'] ) )
   {
     $title.= ' '.$lang['title_thumbnails_2'].' <span style="color:#006699;">';
     // $_GET['dir'] contains :
     // ./galleries/vieux_lyon ou
     // ./galleries/vieux_lyon/visite ou
     // ./galleries/vieux_lyon/visite/truc ...
     $dir = explode( "/", $_GET['dir'] );
     $title.= $dir[2];
     for ( $i = 3; $i < sizeof( $dir ) - 1; $i++ )
     {
       $title.= ' &gt; '.$dir[$i];
     }
     $title.= "</span>";
   }
   $page_valide = true;
   break;
 case 'comments' :
   $title = $lang['title_comments'];
   $page_valide = true;
   break;
 case 'picture_modify' :
   $title = $lang['title_picmod'];
   $page_valide = true;
   break;
 default:
   $title = $lang['title_default']; break;
}
if ( $title == '' ) $title = $lang['title_default'];
$vtp->setGlobalVar( $handle, 'title', $title );
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
$vtp->setVar( $handle, 'summary.indent', '| ' );
$vtp->setVar( $handle, 'summary.link',
              add_session_id( $link_start.'user_list' ) );
$vtp->setVar( $handle, 'summary.name', $lang['menu_users'] );
$vtp->closeSession( $handle, 'summary' );
// groups
$vtp->addSession( $handle, 'summary' );
$vtp->setVar( $handle, 'summary.indent', '| ' );
$vtp->setVar( $handle, 'summary.link',
              add_session_id( $link_start.'group_list' ) );
$vtp->setVar( $handle, 'summary.name', $lang['menu_groups'] );
$vtp->closeSession( $handle, 'summary' );
// categories
$vtp->addSession( $handle, 'summary' );
$vtp->setVar( $handle, 'summary.indent', '| ' );
$vtp->setVar( $handle, 'summary.link',add_session_id( $link_start.'cat_list'));
$vtp->setVar( $handle, 'summary.name', $lang['menu_categories'] );
$vtp->closeSession( $handle, 'summary' );
// waiting
$vtp->addSession( $handle, 'summary' );
$vtp->setVar( $handle, 'summary.indent', '| ' );
$vtp->setVar( $handle, 'summary.link',
              add_session_id( $link_start.'waiting' ) );
$query = 'SELECT id';
$query.= ' FROM '.PREFIX_TABLE.'waiting';
$query.= " WHERE validated='false'";
$query.= ';';
$result = mysql_query( $query );
$nb_waiting = '';
if ( mysql_num_rows( $result ) > 0 )
{
  $nb_waiting =  ' [ '.mysql_num_rows( $result ).' ]';
}
$vtp->setVar( $handle, 'summary.name', $lang['menu_waiting'].$nb_waiting );
$vtp->closeSession( $handle, 'summary' );
// comments
$vtp->addSession( $handle, 'summary' );
$vtp->setVar( $handle, 'summary.indent', '| ' );
$vtp->setVar( $handle, 'summary.link',
              add_session_id( $link_start.'comments' ) );
$query = 'SELECT id';
$query.= ' FROM '.PREFIX_TABLE.'comments';
$query.= " WHERE validated='false'";
$query.= ';';
$result = mysql_query( $query );
$nb_waiting = '';
if ( mysql_num_rows( $result ) > 0 )
{
  $nb_waiting =  ' [ '.mysql_num_rows( $result ).' ]';
}
$vtp->setVar( $handle, 'summary.name', $lang['menu_comments'].$nb_waiting );
$vtp->closeSession( $handle, 'summary' );
// update
$vtp->addSession( $handle, 'summary' );
$vtp->setVar( $handle, 'summary.indent', '| ' );
$vtp->setVar( $handle, 'summary.link',
              add_session_id( $link_start.'update' ) );
$vtp->setVar( $handle, 'summary.name', $lang['menu_update'] );
$vtp->closeSession( $handle, 'summary' );
// thumbnails
$vtp->addSession( $handle, 'summary' );
$vtp->setVar( $handle, 'summary.indent', '| ' );
$vtp->setVar( $handle, 'summary.link',
              add_session_id( $link_start.'thumbnail' ) );
$vtp->setVar( $handle, 'summary.name', $lang['menu_thumbnails'] );
$vtp->closeSession( $handle, 'summary' );
// history
$vtp->addSession( $handle, 'summary' );
$vtp->setVar( $handle, 'summary.indent', '| ' );
$vtp->setVar( $handle, 'summary.link',
              add_session_id( $link_start.'stats' ) );
$vtp->setVar( $handle, 'summary.name', $lang['menu_history'] );
$vtp->closeSession( $handle, 'summary' );
// instructions
$vtp->addSession( $handle, 'summary' );
$vtp->setVar( $handle, 'summary.indent', '| ' );
$vtp->setVar( $handle, 'summary.link',
              add_session_id( $link_start.'help' ) );
$vtp->setVar( $handle, 'summary.name', $lang['menu_instructions'] );
$vtp->closeSession( $handle, 'summary' );
// back to thumbnails page
$vtp->addSession( $handle, 'summary' );
$vtp->setVar( $handle, 'summary.indent', '| ' );
$vtp->setVar( $handle, 'summary.link', add_session_id( './category.php' ) );
$vtp->setVar( $handle, 'summary.name', $lang['menu_back'] );
$vtp->closeSession( $handle, 'summary' );
//------------------------------------------------------------- content display
if ( $page_valide )
{
  include ( './admin/'.$_GET['page'].'.php' );
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
