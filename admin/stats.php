<?php
// +-----------------------------------------------------------------------+
// |                               stats.php                               |
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
if( !defined("PHPWG_ROOT_PATH") )
{
	die ("Hacking attempt!");
}
include_once( PHPWG_ROOT_PATH.'admin/include/isadmin.inc.php' );
$max_pixels = 500;
//------------------------------------------------------------ comment deletion
if ( isset( $_GET['del'] ) and is_numeric( $_GET['del'] ) )
{
  $query = 'DELETE FROM '.COMMENTS_TABLE;
  $query.= ' WHERE id = '.$_GET['del'];
  $query.= ';';
  mysql_query( $query );
}
//--------------------------------------------------------- history table empty
if ( isset( $_GET['act'] ) and $_GET['act'] == 'empty' )
{
  $query = 'DELETE FROM '.HISTORY_TABLE.';';
  mysql_query( $query );
}

// empty link
$url_empty = PHPWG_ROOT_PATH.'admin.php?page=stats';
if (isset($_GET['last_days']))
  	$url_empty .='&amp;last_days='.$_GET['last_days'];
$url_empty.= '&amp;act=empty';
//----------------------------------------------------- template initialization
$template->set_filenames( array('stats'=>'admin/stats.tpl') );

if ( isset( $_GET['last_days'] ) ) define( 'MAX_DAYS', $_GET['last_days'] );
else                               define( 'MAX_DAYS', 0 );

foreach ( $conf['last_days'] as $option ) {
  $url = $_SERVER['PHP_SELF'].'?last_days='.($option - 1);
  $url.= '&amp;page=stats';
  $template->assign_block_vars(
    'last_day_option',
    array(
      'OPTION'=>$option,
      'T_STYLE'=>(( $option == MAX_DAYS + 1 )?'text-decoration:underline;':''),
      'U_OPTION'=>add_session_id( $url )
      )
    );
}

$template->assign_vars(array(
  'L_STAT_LASTDAYS'=>$lang['stats_last_days'],
  'L_STAT_DATE'=>$lang['date'],
  'L_STAT_LOGIN'=>$lang['login'],
  'L_STAT_IP'=>$lang['IP'],
  'L_STAT_FILE'=>$lang['file'],
  'L_STAT_CATEGORY'=>$lang['category'],
  'L_STAT_PICTURE'=>$lang['picture'],
  'L_STAT_EMPTY'=>$lang['stats_empty'],
  'L_STAT_SEEN'=>$lang['stats_pages_seen'],
  'L_STAT_VISITOR'=>$lang['stats_visitors'],
  
  'STAT_EMPTY_URL'=>$url_empty
  ));

$tpl = array( 'stats_pages_seen_graph_title', 'stats_visitors_graph_title');

//---------------------------------------------------------------- log  history
$days = array();
$max_nb_visitors = 0;
$max_pages_seen = 0;

$starttime = mktime(  0, 0, 0,date('n'),date('j'),date('Y') );
$endtime   = mktime( 23,59,59,date('n'),date('j'),date('Y') );
for ( $i = 0; $i <= MAX_DAYS; $i++ )
{
  $day = array();
  $template->assign_block_vars('day',array(
    ));
  
  // link to open the day to see details
  $local_expand = $page['expand_days'];
  if ( in_array( $i, $page['expand_days'] ) )
  {
    $vtp->addSession( $sub, 'expanded' );
    $vtp->closeSession( $sub, 'expanded' );
    $vtp->setVar( $sub, 'day.open_or_close', $lang['close'] );
    $local_expand = array_remove( $local_expand, $i );
  }
  else
  {
    $vtp->addSession( $sub, 'collapsed' );
    $vtp->closeSession( $sub, 'collapsed' );
    $vtp->setVar( $sub, 'day.open_or_close', $lang['open'] );
    array_push( $local_expand, $i );
  }
  $url = './admin.php?page=stats';
  if (isset($_GET['last_days']))
  	$url.= '&amp;last_days='.$_GET['last_days'];
  $url.= '&amp;expand='.implode( ',', $local_expand );
  $vtp->setVar( $sub, 'day.url', add_session_id( $url ) );
  // date displayed like this (in English ) :
  //                     Sunday 15 June 2003
  $date = $lang['day'][date( 'w', $starttime )];   // Sunday
  $date.= date( ' j ', $starttime );               // 15
  $date.= $lang['month'][date( 'n', $starttime )]; // June
  $date.= date( ' Y', $starttime );                // 2003
  $day['date'] = $date;
  $vtp->setVar( $sub, 'day.name', $date );
  // number of visitors for this day
  $query = 'SELECT DISTINCT(IP) as nb_visitors';
  $query.= ' FROM '.PREFIX_TABLE.'history';
  $query.= ' WHERE date > '.$starttime;
  $query.= ' AND date < '.$endtime;
  $query.= ';';
  $result = mysql_query( $query );
  $nb_visitors = mysql_num_rows( $result );
  $day['nb_visitors'] = $nb_visitors;
  if ( $nb_visitors > $max_nb_visitors ) $max_nb_visitors = $nb_visitors;
  $vtp->setVar( $sub, 'day.nb_visitors', $nb_visitors );
  // log lines for this day
  $query = 'SELECT date,login,IP,category,file,picture';
  $query.= ' FROM '.PREFIX_TABLE.'history';
  $query.= ' WHERE date > '.$starttime;
  $query.= ' AND date < '.$endtime;
  $query.= ' ORDER BY date DESC';
  $query.= ';';
  $result = mysql_query( $query );
  $nb_pages_seen = mysql_num_rows( $result );
  $day['nb_pages_seen'] = $nb_pages_seen;
  if ( $nb_pages_seen > $max_pages_seen ) $max_pages_seen = $nb_pages_seen;
  $vtp->setVar( $sub, 'day.nb_pages', $nb_pages_seen );
  if ( in_array( $i, $page['expand_days'] ) )
  {
    while ( $row = mysql_fetch_array( $result ) )
    {
      $vtp->addSession( $sub, 'line' );
      $vtp->setVar( $sub, 'line.date', date( 'G:i:s', $row['date'] ) );
      $vtp->setVar( $sub, 'line.login', $row['login'] );
      $vtp->setVar( $sub, 'line.IP', $row['IP'] );
      $vtp->setVar( $sub, 'line.category', $row['category'] );
      $vtp->setVar( $sub, 'line.file', $row['file'] );
      $vtp->setVar( $sub, 'line.picture', $row['picture'] );
      $vtp->closeSession( $sub, 'line' );
    }
  }
  $starttime-= 24*60*60;
  $endtime  -= 24*60*60;
  $vtp->closeSession( $sub, 'day' );
  array_push( $days, $day );*/
}
//------------------------------------------------------------ pages seen graph
foreach ( $days as $day ) {
  /*$vtp->addSession( $sub, 'pages_day' );
  if ( $max_pages_seen > 0 )
    $width = floor( ( $day['nb_pages_seen']*$max_pixels ) / $max_pages_seen );
  else $width = 0;
  $vtp->setVar( $sub, 'pages_day.date', $day['date'] );
  $vtp->setVar( $sub, 'pages_day.width', $width );
  $vtp->setVar( $sub, 'pages_day.nb_pages', $day['nb_pages_seen'] );
  $vtp->closeSession( $sub, 'pages_day' );*/
}
//-------------------------------------------------------------- visitors grpah
foreach ( $days as $day ) {
  /*$vtp->addSession( $sub, 'visitors_day' );
  if ( $max_nb_visitors > 0 )
    $width = floor( ( $day['nb_visitors'] * $max_pixels ) / $max_nb_visitors );
  else $width = 0;
  $vtp->setVar( $sub, 'visitors_day.date', $day['date'] );
  $vtp->setVar( $sub, 'visitors_day.width', $width );
  $vtp->setVar( $sub, 'visitors_day.nb_visitors', $day['nb_visitors'] );
  $vtp->closeSession( $sub, 'visitors_day' );*/
}
//----------------------------------------------------------- sending html code
$template->assign_var_from_handle('ADMIN_CONTENT', 'stats');
?>
