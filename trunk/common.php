<?php
/***************************************************************************
 *                                common.php                              *
 *                            -------------------                          *
 *   application   : PhpWebGallery 1.4 <http://phpwebgallery.net>          *
 *   author        : Pierrick LE GALL <pierrick@z0rglub.com>               *
 *                                                                         *
 *   $Id$
 *                                                                         *
 ***************************************************************************/

/***************************************************************************
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation;                                         *
 *                                                                         *
 ***************************************************************************/
// determine the initial instant to indicate the generation time of this page
$t1 = explode( ' ', microtime() );
$t2 = explode( '.', $t1[0] );
$t2 = $t1[1].'.'.$t2[1];

set_magic_quotes_runtime(0); // Disable magic_quotes_runtime

//
// addslashes to vars if magic_quotes_gpc is off
// this is a security precaution to prevent someone
// trying to break out of a SQL statement.
//
if( !get_magic_quotes_gpc() )
{
  if( is_array($HTTP_GET_VARS) )
  {
    while( list($k, $v) = each($HTTP_GET_VARS) )
    {
	  if( is_array($HTTP_GET_VARS[$k]) )
	  {
        while( list($k2, $v2) = each($HTTP_GET_VARS[$k]) )
        {
		  $HTTP_GET_VARS[$k][$k2] = addslashes($v2);
		}
	  @reset($HTTP_GET_VARS[$k]);
	  }
	  else
	  {
		$HTTP_GET_VARS[$k] = addslashes($v);
	  }
	}
	@reset($HTTP_GET_VARS);
  }
  
  if( is_array($HTTP_POST_VARS) )
  {
	while( list($k, $v) = each($HTTP_POST_VARS) )
	{
	  if( is_array($HTTP_POST_VARS[$k]) )
	  {
		while( list($k2, $v2) = each($HTTP_POST_VARS[$k]) )
		{
		  $HTTP_POST_VARS[$k][$k2] = addslashes($v2);
		}
	  @reset($HTTP_POST_VARS[$k]);
	  }
	  else
	  {
		$HTTP_POST_VARS[$k] = addslashes($v);
	  }
    }
    @reset($HTTP_POST_VARS);
  }

  if( is_array($HTTP_COOKIE_VARS) )
  {
    while( list($k, $v) = each($HTTP_COOKIE_VARS) )
    {
	  if( is_array($HTTP_COOKIE_VARS[$k]) )
	  {
	    while( list($k2, $v2) = each($HTTP_COOKIE_VARS[$k]) )
	    {
		  $HTTP_COOKIE_VARS[$k][$k2] = addslashes($v2);
	    }
	    @reset($HTTP_COOKIE_VARS[$k]);
	  }
	  else
	  {
	    $HTTP_COOKIE_VARS[$k] = addslashes($v);
	  }
    }
    @reset($HTTP_COOKIE_VARS);
  }
}

//
// Define some basic configuration arrays this also prevents
// malicious rewriting of language and otherarray values via
// URI params
//
$conf = array();
$page = array();
$user = array();
$lang = array();

include($phpwg_root_path .'config.php');

if( !defined("PHPWG_INSTALLED") )
{
	header("Location: install.php");
	exit;
}

include($phpwg_root_path . 'include/constants.php');
include($phpwg_root_path . 'include/functions.inc.php');
include($phpwg_root_path . 'include/template.php');
include($phpwg_root_path . 'include/vtemplate.class.php');
include($phpwg_root_path . 'include/config.inc.php');

//
// Database connection
//

mysql_connect( $cfgHote, $cfgUser, $cfgPassword )
    or die ( "Could not connect to server" );
mysql_select_db( $cfgBase )
    or die ( "Could not connect to database" );
	
//
// Obtain and encode users IP
//
if( getenv('HTTP_X_FORWARDED_FOR') != '' )
{
  $client_ip = ( !empty($HTTP_SERVER_VARS['REMOTE_ADDR']) ) ? $HTTP_SERVER_VARS['REMOTE_ADDR'] : ( ( !empty($HTTP_ENV_VARS['REMOTE_ADDR']) ) ? $HTTP_ENV_VARS['REMOTE_ADDR'] : $REMOTE_ADDR );

  if ( preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", getenv('HTTP_X_FORWARDED_FOR'), $ip_list) )
  {
    $private_ip = array('/^0\./', '/^127\.0\.0\.1/', '/^192\.168\..*/', '/^172\.16\..*/', '/^10.\.*/', '/^224.\.*/', '/^240.\.*/');
    $client_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);
  }
}
else
{
  $client_ip = ( !empty($HTTP_SERVER_VARS['REMOTE_ADDR']) ) ? $HTTP_SERVER_VARS['REMOTE_ADDR'] : ( ( !empty($HTTP_ENV_VARS['REMOTE_ADDR']) ) ? $HTTP_ENV_VARS['REMOTE_ADDR'] : $REMOTE_ADDR );
}
$user_ip = encode_ip($client_ip);

//
// Setup forum wide options, if this fails
// then we output a CRITICAL_ERROR since
// basic forum information is not available
//
$sql = "SELECT * FROM " . CONFIG_TABLE;
if( !($result = mysql_query($sql)) )
{
  die("Could not query config information");
}

$row =mysql_fetch_array($result);
// rertieving the configuration informations for site
// $infos array is used to know the fields to retrieve in the table "config"
// Each field becomes an information of the array $conf.
// Example :
//            prefix_thumbnail --> $conf['prefix_thumbnail']
$infos = array( 'prefix_thumbnail', 'webmaster', 'mail_webmaster', 'access',
                'session_id_size', 'session_keyword', 'session_time',
                'max_user_listbox', 'show_comments', 'nb_comment_page',
                'upload_available', 'upload_maxfilesize', 'upload_maxwidth',
                'upload_maxheight', 'upload_maxwidth_thumbnail',
                'upload_maxheight_thumbnail','log','comments_validation',
                'comments_forall','authorize_cookies','mail_notification' );
// affectation of each field of the table "config" to an information of the
// array $conf.
foreach ( $infos as $info ) {
  if ( isset( $row[$info] ) ) $conf[$info] = $row[$info];
  else                        $conf[$info] = '';
  // If the field is true or false, the variable is transformed into a boolean
  // value.
  if ( $conf[$info] == 'true' or $conf[$info] == 'false' )
  {
    $conf[$info] = get_boolean( $conf[$info] );
  }
}

if (file_exists('install.php') && !DEBUG)
{
	die('Please ensure both the install/ and contrib/ directories are deleted');
}


//---------------
// A partir d'ici il faudra dispatcher le code dans d'autres fichiers
//---------------

include($phpwg_root_path . 'include/user.inc.php');

// displaying the username in the language of the connected user, instead of
// "guest" as you can find in the database
if ( $user['is_the_guest'] ) $user['username'] = $lang['guest'];
include_once( './template/'.$user['template'].'/htmlfunctions.inc.php' );
define('PREFIX_TABLE', $table_prefix);
?>