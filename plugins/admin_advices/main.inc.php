<?php /*
Plugin Name: Admin Advices
Version: 1.8
Description: Give you an advice on the administration page.
Plugin URI: http://www.phpwebgallery.net
Author: PhpWebGallery team
Author URI: http://www.phpwebgallery.net
*/

add_event_handler('loc_end_page_header', 'set_admin_advice_add_css' );

// Add a XHTML tag in HEAD section
function set_admin_advice_add_css()
{
  global $template, $page;
  if ( isset($page['body_id']) and $page['body_id']=='theAdminPage'
    and $page['page'] == 'intro'
    )
  {// This Plugin works only on the Admin page
    $template->append(
      'head_elements',
      '<link rel="stylesheet" type="text/css" '
                    . 'href="'.PHPWG_PLUGINS_PATH.'admin_advices/default-layout.css">'
     );
    add_event_handler('loc_begin_page_tail', 'set_admin_advice' );
  }
}

// Build an advice on the Admin Intro page
function set_admin_advice()
{
  global $page, $user, $template, $conf;

// Setup Advice Language (Maybe there is already a variable)
  $advlang = ( isset($user['language']) ) ?
    $user['language'] : get_default_language(); // en_UK
  $my_path = dirname(__FILE__).'/';
  $adv = array();
  if ( !@file_exists($my_path."$advlang/lang.adv.php") )
  {
    $advlang = 'en_UK';
  }
//  Include language advices
  @include_once( $my_path."$advlang/lang.adv.php" );

//  If there is an advice
  if ( $cond )
  {
    $template->set_filenames(array(
      'admin_advice' => $my_path.'admin_advices.tpl')
      );

// Random Thumbnail
    $query = '
SELECT *
FROM '.IMAGES_TABLE.'
ORDER BY RAND(NOW())
LIMIT 0, 1
;';
    $result = pwg_query($query);
    $row = mysql_fetch_assoc($result);
    if ( is_array($row) )
    {
      $url_modify = get_root_url().'admin.php?page=picture_modify'
                  .'&amp;image_id='.$row['id'];
      $query = '
SELECT * FROM '.IMAGE_TAG_TABLE.'
WHERE image_id =  ' . $row['id'] .'
;';
      $tag_count = mysql_num_rows(mysql_query($query));
      $template->assign('thumbnail',
         array(
           'IMAGE'              => get_thumbnail_url($row),
           'IMAGE_ALT'          => $row['file'],
           'IMAGE_TITLE'        => $row['name'],
           'METADATA'           => (empty($row['date_metadata_update'])) ?
                                   'un' : '',
           'NAME'               => (empty($row['name'])) ?
                                   'un' : '',
           'COMMENT'            => (empty($row['comment'])) ?
                                   'un' : '',
           'AUTHOR'             => (empty($row['author'])) ?
                                   'un' : '',
           'CREATE_DATE'        => (empty($row['date_creation'])) ?
                                   'un' : '',
           'TAGS'               => ($tag_count == 0) ?
                                   'un' : '',
           'NUM_TAGS'           => $tag_count,
           'U_MODIFY'           => $url_modify,
         )
       );
    }
    $advice_text = array_shift($adv);
    $template->assign(
      array(
        'ADVICE_ABOUT' => '$conf[' . "'$confk'] ",
        'ADVICE_TEXT'  => $advice_text,
         )
      );
  $template->assign('More', $adv );
  $template->pparse('admin_advice');
  }
}
?>
