<?php
/***************************************************************************
 *                         functions_session.inc.php                       *
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
function generate_key()
{
  global $conf;
  $md5 = md5( substr( microtime(), 2, 6 ).$conf['session_keyword'] );
  $init = "";
  for ( $i = 0; $i < strlen( $md5 ); $i++ )
  {
    if ( is_numeric( $md5[$i] ) )
    {
      $init.= "$md5[$i]";
    }
  }
  $init = substr( $init, 0, 8 );
  mt_srand( $init );
  $key = "";
  for ( $i = 0; $i < $conf['session_id_size']; $i++ )
  {
    $c = mt_rand( 0, 2 );
    if ( $c == 0 )
    {
      $key .= chr( mt_rand( 65, 90 ) );
    }
    elseif ( $c == 1 )
      {
        $key .= chr( mt_rand( 97, 122 ) );
      }
    else
    {
      $key .= mt_rand( 0, 9 );
    }
  }
  return $key;
}
        
function session_create( $pseudo )
{
  global $conf,$prefixeTable,$REMOTE_ADDR;
  // 1. trouver une cl� de session inexistante
  $id_found = false;
  while ( !$id_found )
  {
    $generated_id = generate_key();
    $query = 'select id';
    $query.= ' from '.$prefixeTable.'sessions';
    $query.= " where id = '".$generated_id."';";
    $result = mysql_query( $query );
    if ( mysql_num_rows( $result ) == 0 )
    {
      $id_found = true;
    }
  }
  // 2. r�cup�ration de l'id de l'utilisateur dont le pseudo
  //    est pass� en param�tre
  $query = 'select id';
  $query.= ' from '.$prefixeTable.'users';
  $query.= " where pseudo = '".$pseudo."';";
  $row = mysql_fetch_array( mysql_query( $query ) );
  $user_id = $row['id'];
  // 3. insertion de la session dans la base de donn�e
  $expiration = $conf['session_time']*60+time();
  $query = 'insert into '.$prefixeTable.'sessions';
  $query.= ' (id,user_id,expiration,ip) values';
  $query.= "('".$generated_id."','".$user_id;
  $query.= "','".$expiration."','".$REMOTE_ADDR."');";
  mysql_query( $query );
                
  return $generated_id;
}
        
function add_session_id_to_url( $url, $redirect = false )
{
  global $page, $user;
  $amp = "&amp;";
  if ( $redirect )
  {
    $amp = "&";
  }
  if ( !$user['is_the_guest'] )
  {
    if ( ereg( "\.php\?",$url ) )
    {
      return $url.$amp."id=".$page['session_id'];
    }
    else
    {
      return $url."?id=".$page['session_id'];
    }
  }
  else
  {
    return $url;
  }
}

function add_session_id( $url, $redirect = false )
{
  global $page, $user;
  $amp = "&amp;";
  if ( $redirect )
  {
    $amp = "&";
  }
  if ( !$user['is_the_guest'] )
  {
    if ( ereg( "\.php\?",$url ) )
    {
      return $url.$amp."id=".$page['session_id'];
    }
    else
    {
      return $url."?id=".$page['session_id'];
    }
  }
  else
  {
    return $url;
  }
}
?>