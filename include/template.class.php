<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008      Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 PhpWebGallery Team    http://phpwebgallery.net |
// | Copyright(C) 2002-2003 Pierrick LE GALL   http://le-gall.net/pierrick |
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


require_once 'smarty/libs/Smarty.class.php';

// migrate lang:XXX
//    sed "s/{lang:\([^}]\+\)}/{\'\1\'|@translate}/g" my_template.tpl
// migrate change root level vars {XXX}
//    sed "s/{pwg_root}/{ROOT_URL}/g" my_template.tpl
// migrate change root level vars {XXX}
//    sed "s/{\([a-zA-Z_]\+\)}/{$\1}/g" my_template.tpl
// migrate all
//    cat my_template.tpl | sed "s/{lang:\([^}]\+\)}/{\'\1\'|@translate}/g" | sed "s/{pwg_root}/{ROOT_URL}/g" | sed "s/{\([a-zA-Z_]\+\)}/{$\1}/g"


class Template {

  var $smarty;

  var $output = '';

  // Hash of filenames for each template handle.
  var $files = array();

  function Template($root = ".", $theme= "")
  {
    global $conf;

    $this->smarty = new Smarty;
    $this->smarty->debugging = $conf['debug_template'];
    //$this->smarty->force_compile = true;

    if ( isset($conf['compiled_template_dir'] ) )
    {
      $compile_dir = $conf['compiled_template_dir'];
    }
    else
    {
      $compile_dir = $conf['local_data_dir'];
      if ( !is_dir($compile_dir) )
      {
        mkdir( $compile_dir, 0777);
        file_put_contents($compile_dir.'/index.htm', '');
      }
      $compile_dir .= '/templates_c';
    }
    if ( !is_dir($compile_dir) )
    {
      mkdir( $compile_dir, 0777 );
      file_put_contents($compile_dir.'/index.htm', '');
    }

    $this->smarty->compile_dir = $compile_dir;

    $this->smarty->assign_by_ref( 'pwg', new PwgTemplateAdapter() );
    $this->smarty->register_modifier( 'translate', array('Template', 'mod_translate') );
    $this->smarty->register_modifier( 'explode', array('Template', 'mod_explode') );

    if ( !empty($theme) )
    {
      include($root.'/theme/'.$theme.'/themeconf.inc.php');
      $this->smarty->assign('themeconf', $themeconf);
    }

    $this->set_template_dir($root);
  }

  /**
   * Sets the template root directory for this Template object.
   */
  function set_template_dir($dir)
  {
    $this->smarty->template_dir = $dir;

    $real_dir = realpath($dir);
    $compile_id = crc32( $real_dir===false ? $dir : $real_dir);
    $this->smarty->compile_id = sprintf('%08X', $compile_id );
  }

  /**
   * Gets the template root directory for this Template object.
   */
  function get_template_dir()
  {
    return $this->smarty->template_dir;
  }

  /**
   * Deletes all compiled templates.
   */
  function delete_compiled_templates()
  {
      $save_compile_id = $this->smarty->compile_id;
      $this->smarty->compile_id = null;
      $this->smarty->clear_compiled_tpl();
      $this->smarty->compile_id = $save_compile_id;
      file_put_contents($this->smarty->compile_dir.'/index.htm', '');
  }

  function get_themeconf($val)
  {
    $tc = $this->smarty->get_template_vars('themeconf');
    return isset($tc[$val]) ? $tc[$val] : '';
  }

  /**
   * Sets the template filename for handle.
   */
  function set_filename($handle, $filename)
  {
    return $this->set_filenames( array($handle=>$filename) );
  }

  /**
   * Sets the template filenames for handles. $filename_array should be a
   * hash of handle => filename pairs.
   */
  function set_filenames($filename_array)
  {
    if (!is_array($filename_array))
    {
      return false;
    }

    reset($filename_array);
    while(list($handle, $filename) = each($filename_array))
    {
      if (is_null($filename))
        unset( $this->files[$handle] );
      else
        $this->files[$handle] = $filename;
    }
    return true;
  }

  /** see smarty assign http://www.smarty.net/manual/en/api.assign.php */
  function assign($tpl_var, $value = null)
  {
    $this->smarty->assign( $tpl_var, $value );
  }

  /**
   * Inserts the uncompiled code for $handle as the value of $varname in the
   * root-level. This can be used to effectively include a template in the
   * middle of another template.
   * This is equivalent to assign($varname, $this->parse($handle, true))
   */
  function assign_var_from_handle($varname, $handle)
  {
    $this->assign($varname, $this->parse($handle, true));
    return true;
  }

  /** see smarty append http://www.smarty.net/manual/en/api.append.php */
  function append($tpl_var, $value=null, $merge=false)
  {
    $this->smarty->append( $tpl_var, $value, $merge );
  }

  /**
   * Root-level variable concatenation. Appends a  string to an existing
   * variable assignment with the same name.
   */
  function concat($tpl_var, $value)
  {
    $old_val = & $this->smarty->get_template_vars($tpl_var);
    if ( isset($old_val) )
    {
      $old_val .= $value;
    }
    else
    {
      $this->assign($tpl_var, $value);
    }
  }

  /** see smarty append http://www.smarty.net/manual/en/api.clear_assign.php */
  function clear_assign($tpl_var)
  {
    $this->smarty->clear_assign( $tpl_var );
  }

  /** see smarty get_template_vars http://www.smarty.net/manual/en/api.get_template_vars.php */
  function &get_template_vars($name=null)
  {
    return $this->smarty->get_template_vars( $name );
  }


  /**
   * Load the file for the handle, eventually compile the file and run the compiled
   * code. This will add the output to the results or return the result if $return
   * is true.
   */
  function parse($handle, $return=false)
  {
    if ( !isset($this->files[$handle]) )
    {
      die("Template->parse(): Couldn't load template file for handle $handle");
    }

    $this->smarty->assign( 'ROOT_URL', get_root_url() );
    $this->smarty->assign( 'TAG_INPUT_ENABLED',
      ((is_adviser()) ? 'disabled="disabled" onclick="return false;"' : ''));
    $v = $this->smarty->fetch($this->files[$handle], null, null, false);
    if ($return)
    {
      return $v;
    }
    $this->output .= $v;
  }

  /**
   * Load the file for the handle, eventually compile the file and run the compiled
   * code. This will print out the results of executing the template.
   */
  function pparse($handle)
  {
    $this->parse($handle, false);
    echo $this->output;
    $this->output='';

  }


  /** flushes the output */
  function p()
  {
    $start = get_moment();

    echo $this->output;
    $this->output='';

    if ($this->smarty->debugging)
    {
      global $t2;
      $this->smarty->assign(
        array(
        'AAAA_DEBUG_OUTPUT_TIME__' => get_elapsed_time($start, get_moment()),
        'AAAA_DEBUG_TOTAL_TIME__' => get_elapsed_time($t2, get_moment())
        )
        );
      require_once(SMARTY_CORE_DIR . 'core.display_debug_console.php');
      echo smarty_core_display_debug_console(null, $this->smarty);
    }
  }

  /**
   * translate variable modifier - translates a text to the currently loaded
   * language
   */
  /*static*/ function mod_translate($text)
  {
    return l10n($text);
  }

  /**
   * explode variable modifier - similar to php explode
   * 'Yes;No'|@explode:';' -> array('Yes', 'No')
   */
  /*static*/ function mod_explode($text, $delimiter=',')
  {
    return explode($delimiter, $text);
  }
}

/**
 * This class contains basic functions that can be called directly from the
 * templates in the form $pwg->l10n('edit')
 */
class PwgTemplateAdapter
{
  function l10n($text)
  {
    return l10n($text);
  }

  function l10n_dec($s, $p, $v)
  {
    return l10n_dec($s, $p, $v);
  }

  function sprintf()
  {
    $args = func_get_args();
    return call_user_func_array('sprintf',  $args );
  }
}

?>
