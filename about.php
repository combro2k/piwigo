<?php
/***************************************************************************
 *                                about.php                                *
 *                            ------------------                           *
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
include_once( './include/init.inc.php' );
//----------------------------------------------------- template initialization
$vtp = new VTemplate;
$handle = $vtp->Open( './template/'.$user['template'].'/about.vtp' );
initialize_template();

$tpl = array('about_page_title','about_title','about_message','about_return');
templatize_array( $tpl, 'lang', $handle );
$vtp->setVar( $handle, 'user_template', $user['template'] );

$url = './category.php?'.$_SERVER['QUERY_STRING'];
$vtp->setVar( $handle, 'back_url', add_session_id( $url ) );
//----------------------------------------------------------- html code display
$code = $vtp->Display( $handle, 0 );
echo $code;
?>