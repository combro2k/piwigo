<?php
/***************************************************************************
 *                 register.php is a part of PhpWebGallery                 *
 *                            -------------------                          *
 *   last update          : Saturday, December 28, 2002                    *
 *   email                : pierrick@z0rglub.com                           *
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
include_once( './include/init.inc.php' );
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
$vtp = new VTemplate;
$handle = $vtp->Open( './template/'.$user['template'].'/register.vtp' );
// language
$vtp->setGlobalVar( $handle, 'register_page_title',
                    $lang['register_page_title'] );
$vtp->setGlobalVar( $handle, 'register_title',   $lang['register_title'] );
$vtp->setGlobalVar( $handle, 'ident_guest_visit',$lang['ident_guest_visit'] );
$vtp->setGlobalVar( $handle, 'submit',           $lang['submit'] );
// user
$vtp->setGlobalVar( $handle, 'page_style',       $user['style'] );
// structure
$vtp->setGlobalVar( $handle, 'frame_start',      get_frame_start() );
$vtp->setGlobalVar( $handle, 'frame_begin',      get_frame_begin() );
$vtp->setGlobalVar( $handle, 'frame_end',        get_frame_end() );
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
$vtp->setVar( $handle, 'line.name', $lang['reg_mail_address'] );
$vtp->addSession( $handle, 'text' );
$vtp->setVar( $handle, 'text.name', 'mail_address' );
$vtp->setVar( $handle, 'text.value', $_POST['mail_address'] );
$vtp->closeSession( $handle, 'text' );
$vtp->closeSession( $handle, 'line' );
//----------------------------------------------------------- html code display
$code = $vtp->Display( $handle, 0 );
echo $code;
?>