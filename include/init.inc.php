<?php
/***************************************************************************
 *                               init.inc.php                              *
 *                            -------------------                          *
 *   application          : PhpWebGallery 1.3                              *
 *   author               : Pierrick LE GALL <pierrick@z0rglub.com>        *
 *                                                                         *
 ***************************************************************************

 ***************************************************************************
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation;                                         *
 *                                                                         *
 ***************************************************************************/
define( PREFIXE_INCLUDE, '' );
        
include_once( './include/config.inc.php' );
include_once( './include/user.inc.php' );
        
include( './theme/'.$user['theme'].'/conf.php' );
$user['lien_expanded']  = './theme/'.$user['theme'].'/expanded.gif';
$user['lien_collapsed'] = './theme/'.$user['theme'].'/collapsed.gif';
// calculation of the number of picture to display per page
$user['nb_image_page'] = $user['nb_image_line'] * $user['nb_line_page'];
// retrieving the restrictions for this user
$user['restrictions'] = get_restrictions( $user['id'], $user['status'], true );
        
$isadmin = false;
include_once( './language/'.$user['language'].'.php' );
if ( $user['is_the_guest'] )
{
  $user['pseudo'] = $lang['guest'];
}
include_once( './template/'.$user['template'].'/style.inc.php' );
include_once( './template/'.$user['template'].'/htmlfunctions.inc.php' );
?>