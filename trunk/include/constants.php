<?php
/***************************************************************************
 *                              constant.php                             *
 *                            -------------------                          *
 *   application   : PhpWebGallery 1.4 <http://phpwebgallery.net>          *
 *   author        : Pierrick LE GALL <pierrick@z0rglub.com>               *
 *                                                                         *
 *   $Id$
 *                                                                         *
 ***************************************************************************

 ***************************************************************************
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation;                                         *
 *                                                                         *
 ***************************************************************************/

define( 'PREFIX_INCLUDE', '' );
 
// Debug Level
define('DEBUG', 1); // Debugging on
//define('DEBUG', 0); // Debugging off 
 
// Error codes
define('GENERAL_MESSAGE', 200);
define('GENERAL_ERROR', 202);
define('CRITICAL_MESSAGE', 203);
define('CRITICAL_ERROR', 204); 

// xml tags 
define( 'ATT_REG', '\w+' );
define( 'VAL_REG', '[^"]*' );
  
// Table names
define('CATEGORIES_TABLE', $table_prefix.'categories');
define('COMMENTS_TABLE', $table_prefix.'comments');
define('CONFIG_TABLE', $table_prefix.'config');
define('FAVORITES_TABLE', $table_prefix.'favorites');
define('GROUPS_ACCESS_TABLE', $table_prefix.'group_access');
define('GROUPS_TABLE', $table_prefix.'groups');
define('HISTORY_TABLE', $table_prefix.'history');
define('IMAGE_CATEGORY_TABLE', $table_prefix.'image_category');
define('IMAGES_TABLE', $table_prefix.'images');
define('SESSIONS_TABLE', $table_prefix.'sessions');
define('SITES_TABLE', $table_prefix.'sites');
define('USER_ACCESS_TABLE', $table_prefix.'user_access');
define('USER_CATEGORY_TABLE', $table_prefix.'user_category');
define('USER_GROUP_TABLE', $table_prefix.'user_group');
define('USERS_TABLE', $table_prefix.'users');
define('WAITING_TABLE', $table_prefix.'waiting');

?>