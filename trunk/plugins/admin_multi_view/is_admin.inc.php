<?php
if (! defined('MULTIVIEW_CONTROLLER') )
{
  global $user;
  $view_as = pwg_get_session_var( 'multiview_as', 0 );
  if ($view_as)
  {
    $user = build_user( $view_as, true);
  }
  $theme = pwg_get_session_var( 'multiview_theme', '' );
  if ( !empty($theme) )
  {
    list($user['template'], $user['theme']) = explode('/', $theme);
  }
  $lang = pwg_get_session_var( 'multiview_lang', '' );
  if ( !empty($lang) )
  {
    $user['language'] = $lang;
  }
  global $conf;
  if (pwg_get_session_var( 'multiview_show_queries', 0 ))
    $conf['show_queries'] = true;
  if (pwg_get_session_var( 'multiview_debug_l10n', 0 ))
    $conf['debug_l10n'] = true;
}

add_event_handler('loc_end_page_header', 'multiview_loc_end_page_header');

function multiview_loc_end_page_header()
{
  global $template;
  $my_root_url = get_root_url().'plugins/'. basename(dirname(__FILE__)).'/';
  $js =
'<script type="text/javascript">
var theController = window.open("", "mview_controller", "alwaysRaised=yes,dependent=yes,toolbar=no,height=200,width=220,menubar=no,resizable=yes,scrollbars=yes,status=no");
if ( theController.location.toString()=="about:blank" || !theController.location.toString().match(/^(https?.*\/)controller\.php(\?.+)?$/))
{
  theController.location = "'.$my_root_url.'controller.php";
}
</script>';

  $template->assign_block_vars( 'head_element', array(
    'CONTENT' => $js
      )
    );
}
?>
