<?php
// +-----------------------------------------------------------------------+
// |                           register.php                                |
// +-----------------------------------------------------------------------+
// | application   : PhpWebGallery <http://phpwebgallery.net>              |
// | branch        : 1.4                                                   |
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
$phpwg_root_path = './';
include_once( $phpwg_root_path.'incelude/common.inc.php' );
//-------------------------------------------------- access authorization check
if ( $conf['access'] == "restricted" )
{
  echo $lang['only_members'];
  exit();
}
//----------------------------------------------------------- user registration
$error = array();
if ( isset( $_POST['submit'] ) )
{
  $error = register_user( $_POST['login'], $_POST['password'],
                          $_POST['password_conf'], $_POST['mail_address'] );
  if ( sizeof( $error ) == 0 )
  {
    $session_id = session_create( $_POST['login'] );
    $url = 'category.php?id='.$session_id;
    header( 'Request-URI: '.$url );
    header( 'Content-Location: '.$url );  
    header( 'Location: '.$url );
    exit();
  }
}
//----------------------------------------------------- template initialization
//
// Start output of page
//
$title= $lang['register_page_title'];
include('include/page_header.php');

$handle = $vtp->Open( './template/'.$user['template'].'/register.vtp' );
// language
$vtp->setGlobalVar( $handle, 'register_title',   $lang['register_title'] );
$vtp->setGlobalVar( $handle, 'ident_guest_visit',$lang['ident_guest_visit'] );
$vtp->setGlobalVar( $handle, 'submit',           $lang['submit'] );
initialize_template();
//----------------------------------------------------------------- form action
$vtp->setGlobalVar( $handle, 'form_action', './register.php' );
//-------------------------------------------------------------- errors display
if ( sizeof( $error ) != 0 )
{
  $vtp->addSession( $handle, 'errors' );
  for ( $i = 0; $i < sizeof( $error ); $i++ )
  {
    $vtp->addSession( $handle, 'li' );
    $vtp->setVar( $handle, 'li.li', $error[$i] );
    $vtp->closeSession( $handle, 'li' );
  }
  $vtp->closeSession( $handle, 'errors' );
}
//----------------------------------------------------------------------- login
$vtp->addSession( $handle, 'line' );
$vtp->setVar( $handle, 'line.name', $lang['login'] );
$vtp->addSession( $handle, 'text' );
$vtp->setVar( $handle, 'text.name', 'login' );
if (isset( $_POST['login']))
	$vtp->setVar( $handle, 'text.value', $_POST['login'] );
$vtp->closeSession( $handle, 'text' );
$vtp->closeSession( $handle, 'line' );
//-------------------------------------------------------------------- password
$vtp->addSession( $handle, 'line' );
$vtp->setVar( $handle, 'line.name', $lang['password'] );
$vtp->addSession( $handle, 'password' );
$vtp->setVar( $handle, 'password.name', 'password' );
$vtp->setVar( $handle, 'password.value', '' );
$vtp->closeSession( $handle, 'password' );
$vtp->closeSession( $handle, 'line' );
//------------------------------------------------------- password confirmation
$vtp->addSession( $handle, 'line' );
$vtp->setVar( $handle, 'line.name', $lang['reg_confirm'] );
$vtp->addSession( $handle, 'password' );
$vtp->setVar( $handle, 'password.name', 'password_conf' );
$vtp->setVar( $handle, 'password.value', '' );
$vtp->closeSession( $handle, 'password' );
$vtp->closeSession( $handle, 'line' );
//---------------------------------------------------------------- mail address
$vtp->addSession( $handle, 'line' );
$vtp->setVar( $handle, 'line.name', $lang['mail_address'] );
$vtp->addSession( $handle, 'text' );
$vtp->setVar( $handle, 'text.name', 'mail_address' );
if (isset( $_POST['mail_address']))
	$vtp->setVar( $handle, 'text.value', $_POST['mail_address'] );
$vtp->closeSession( $handle, 'text' );
$vtp->closeSession( $handle, 'line' );
//----------------------------------------------------------- html code display
$code = $vtp->Display( $handle, 0 );
echo $code;
include('include/page_tail.php');
?>