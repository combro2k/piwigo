<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | file          : $Id$
// | last update   : $Date$
// | last modifier : $Author$
// | revision      : $Rev$
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

define ('PHPWG_ROOT_PATH', './');

include_once(PHPWG_ROOT_PATH.'include/common.inc.php');
check_status(ACCESS_NONE);
include_once(PHPWG_ROOT_PATH.'include/ws_core.inc.php');

if ( !$conf['allow_web_services'] )
{
  page_forbidden('Web services are disabled');
}

/**
 * event handler that registers standard methods with the web service
 */
function ws_addDefaultMethods( $arr )
{
  include_once(PHPWG_ROOT_PATH.'include/ws_functions.inc.php');
  global $conf, $user;
  $service = &$arr[0];
  $service->addMethod('pwg.getVersion', 'ws_getVersion', null,
      'retrieves the PWG version');

  $service->addMethod('pwg.categories.getImages', 'ws_categories_getImages',
      array(
        'cat_id'=>array('default'=>0, 'flags'=>WS_PARAM_FORCE_ARRAY),
        'recursive'=>array('default'=>false),
        'per_page' => array('default'=>100, 'maxValue'=>$conf['ws_max_images_per_page']),
        'page' => array('default'=>0),
        'order' => array('default'=>null),
        'f_min_rate' => array( 'default'=> null ),
        'f_max_rate' => array( 'default'=> null ),
        'f_min_hit' => array( 'default'=> null ),
        'f_max_hit' => array( 'default'=> null ),
        'f_min_date_available' => array( 'default'=> null ),
        'f_max_date_available' => array( 'default'=> null ),
        'f_min_date_created' => array( 'default'=> null ),
        'f_max_date_created' => array( 'default'=> null ),
        'f_min_ratio' => array( 'default'=> null ),
        'f_max_ratio' => array( 'default'=> null ),
        'f_with_thumbnail' => array( 'default'=> false ),
      ),
      'Returns elements for the corresponding categories.
<br/><b>cat_id</b> can be empty if <b>recursive</b> is true. Can be sent as an array.
<br/><b>order</b> comma separated fields for sorting (file,id, average_rate,...)'
    );

  $service->addMethod('pwg.categories.getList', 'ws_categories_getList',
      array(
        'cat_id' => array('default'=>0),
        'recursive' => array('default'=>false),
        'public' => array('default'=>false),
      ),
      'retrieves a list of categories' );

  $service->addMethod('pwg.images.addComment', 'ws_images_addComment',
      array(
        'image_id' => array(),
        'author' => array( 'default' => $user['is_the_guest']? 'guest':$user['username']),
        'content' => array(),
        'key' => array(),
      ),
      'add a comment to an image' );

  $service->addMethod('pwg.images.getInfo', 'ws_images_getInfo',
      array(
        'image_id' => array(),
        'comments_page' => array('default'=>0 ),
        'comments_per_page' => array( 
              'default' => $conf['nb_comment_page'],  
              'maxValue' => 2*$conf['nb_comment_page'],
            ),
      ),
      'retrieves information about the given photo' );

  $service->addMethod('pwg.images.search', 'ws_images_search',
      array(
        'query'=>array(),
        'per_page' => array('default'=>100, 'maxValue'=>$conf['ws_max_images_per_page']),
        'page' => array('default'=>0),
        'order' => array('default'=>null),
        'f_min_rate' => array( 'default'=> null ),
        'f_max_rate' => array( 'default'=> null ),
        'f_min_hit' => array( 'default'=> null ),
        'f_max_hit' => array( 'default'=> null ),
        'f_min_date_available' => array( 'default'=> null ),
        'f_max_date_available' => array( 'default'=> null ),
        'f_min_date_created' => array( 'default'=> null ),
        'f_max_date_created' => array( 'default'=> null ),
        'f_min_ratio' => array( 'default'=> null ),
        'f_max_ratio' => array( 'default'=> null ),
        'f_with_thumbnail' => array( 'default'=> false ),
      ),
      'Returns elements for the corresponding query search.'
    );

  $service->addMethod('pwg.session.getStatus', 'ws_session_getStatus', null, '' );
  $service->addMethod('pwg.session.login', 'ws_session_login',
    array('username', 'password'),
    'POST method only' );
  $service->addMethod('pwg.session.logout', 'ws_session_logout', null, '');

  $service->addMethod('pwg.tags.getList', 'ws_tags_getList',
    array('sort_by_counter' => array('default' =>false) ),
    'retrieves a list of available tags');
  $service->addMethod('pwg.tags.getImages', 'ws_tags_getImages',
      array(
        'tag_id'=>array('default'=>null, 'flags'=>WS_PARAM_FORCE_ARRAY ),
        'tag_url_name'=>array('default'=>null, 'flags'=>WS_PARAM_FORCE_ARRAY ),
        'tag_name'=>array('default'=>null, 'flags'=>WS_PARAM_FORCE_ARRAY ),
        'tag_mode_and'=>array('default'=>false),
        'per_page' => array('default'=>100, 'maxValue'=>$conf['ws_max_images_per_page']),
        'page' => array('default'=>0),
        'order' => array('default'=>null),
        'f_min_rate' => array( 'default'=> null ),
        'f_max_rate' => array( 'default'=> null ),
        'f_min_hit' => array( 'default'=> null ),
        'f_max_hit' => array( 'default'=> null ),
        'f_min_date_available' => array( 'default'=> null ),
        'f_max_date_available' => array( 'default'=> null ),
        'f_min_date_created' => array( 'default'=> null ),
        'f_max_date_created' => array( 'default'=> null ),
        'f_min_ratio' => array( 'default'=> null ),
        'f_max_ratio' => array( 'default'=> null ),
        'f_with_thumbnail' => array( 'default'=> false ),
      ),
      'Returns elements for the corresponding tags. Note that tag_id, tag_url_name, tag_name an be arrays. Fill at least one of them. '
    );
}

add_event_handler('ws_add_methods', 'ws_addDefaultMethods');


add_event_handler('ws_invoke_allowed', 'ws_isInvokeAllowed', EVENT_HANDLER_PRIORITY_NEUTRAL, 3);

$calling_partner_id = '';
$requestFormat = null;
$responseFormat = null;

if ( isset($_GET['partner']) )
{
  $calling_partner_id = $_GET['partner'];
}
if ( isset($_GET['format']) )
{
  $responseFormat = $_GET['format'];
}

if ( isset($HTTP_RAW_POST_DATA) )
{
  $HTTP_RAW_POST_DATA = trim($HTTP_RAW_POST_DATA);
  if ( strncmp($HTTP_RAW_POST_DATA, '<?xml', 5) == 0 )
  {
  }
  else
  {
    $requestFormat = "json";
  }
}
else
{
  $requestFormat = "rest";
}

if ( !isset($responseFormat) and isset($requestFormat) )
{
  $responseFormat = $requestFormat;
}

$service = new PwgServer();

if (!is_null($requestFormat))
{
  $handler = null;
  switch ($requestFormat)
  {
    case 'rest':
      include_once(PHPWG_ROOT_PATH.'include/ws_protocols/rest_handler.php');
      $handler = new PwgRestRequestHandler();
      break;
  }
  $service->setHandler($requestFormat, $handler);
}

if (!is_null($responseFormat))
{
  $encoder = null;
  switch ($responseFormat)
  {
    case 'rest':
      include_once(PHPWG_ROOT_PATH.'include/ws_protocols/rest_encoder.php');
      $encoder = new PwgRestEncoder();
      break;
    case 'php':
      include_once(PHPWG_ROOT_PATH.'include/ws_protocols/php_encoder.php');
      $encoder = new PwgSerialPhpEncoder();
      break;
    case 'json':
      include_once(PHPWG_ROOT_PATH.'include/ws_protocols/json_encoder.php');
      $encoder = new PwgJsonEncoder();
      break;
    case 'xmlrpc':
      include_once(PHPWG_ROOT_PATH.'include/ws_protocols/xmlrpc_encoder.php');
      $encoder = new PwgXmlRpcEncoder();
      break;
  }
  $service->setEncoder($responseFormat, $encoder);
}

set_make_full_url();
$service->run();

?>
