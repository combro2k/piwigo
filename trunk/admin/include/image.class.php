<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2011 Piwigo Team                  http://piwigo.org |
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

// +-----------------------------------------------------------------------+
// |                           Image Interface                             |
// +-----------------------------------------------------------------------+

// Define all needed methods for image class
interface imageInterface
{
  function get_width();

  function get_height();

  function set_compression_quality($quality);

  function crop($width, $height, $x, $y);

  function strip();

  function rotate($rotation);

  function resize($width, $height);

  function write($destination_filepath);
}

// +-----------------------------------------------------------------------+
// |                          Main Image Class                             |
// +-----------------------------------------------------------------------+

class pwg_image
{
  var $image;
  var $library = '';
  var $source_filepath = '';

  function __construct($source_filepath, $library=null)
  {
    $this->source_filepath = $source_filepath;

    trigger_action('load_image_library', array(&$this) );

    if (is_object($this->image))
    {
      return; // A plugin may have load its own library
    }

    $extension = strtolower(get_extension($source_filepath));

    if (!in_array($extension, array('jpg', 'jpeg', 'png', 'gif')))
    {
      die('[Image] unsupported file extension');
    }

    if (!($this->library = self::get_library($library, $extension)))
    {
      die('No image library available on your server.');
    }

    $class = 'image_'.$this->library;
    $this->image = new $class($source_filepath);
  }

  // Unknow methods will be redirected to image object
  function __call($method, $arguments)
  {
    return call_user_func_array(array($this->image, $method), $arguments);
  }

  // Piwigo resize function
  function pwg_resize($destination_filepath, $max_width, $max_height, $quality, $automatic_rotation=true, $strip_metadata=false, $crop=false, $follow_orientation=true)
  {
    $starttime = get_moment();
    
    // width/height
    $source_width  = $this->image->get_width();
    $source_height = $this->image->get_height();

    // Crop image
    if ($crop)
    {
      $x = 0;
      $y = 0;

      if ($source_width < $source_height and $follow_orientation)
      {
        list($max_width, $max_height) = array($max_height, $max_width);
      }

      $img_ratio = $source_width / $source_height;
      $dest_ratio = $max_width / $max_height;

      if($dest_ratio > $img_ratio)
      {
        $destHeight = round($source_width * $max_height / $max_width);
        $y = round(($source_height - $destHeight) / 2 );
        $source_height = $destHeight;
      }
      elseif ($dest_ratio < $img_ratio)
      {
        $destWidth = round($source_height * $max_width / $max_height);
        $x = round(($source_width - $destWidth) / 2 );
        $source_width = $destWidth;
      }

      $this->image->crop($source_width, $source_height, $x, $y);
    }

    $rotation = null;
    if ($automatic_rotation)
    {
      $rotation = self::get_rotation_angle($this->source_filepath);
    }
    $resize_dimensions = self::get_resize_dimensions($source_width, $source_height, $max_width, $max_height, $rotation);

    // testing on height is useless in theory: if width is unchanged, there
    // should be no resize, because width/height ratio is not modified.
    if ($resize_dimensions['width'] == $source_width and $resize_dimensions['height'] == $source_height)
    {
      // the image doesn't need any resize! We just copy it to the destination
      copy($this->source_filepath, $destination_filepath);
      return $this->get_resize_result($destination_filepath, $resize_dimensions['width'], $resize_dimensions['height'], $starttime);
    }

    $this->image->set_compression_quality($quality);
    
    if ($strip_metadata)
    {
      // we save a few kilobytes. For example a thumbnail with metadata weights 25KB, without metadata 7KB.
      $this->image->strip();
    }
    
    $this->image->resize($resize_dimensions['width'], $resize_dimensions['height']);

    if (isset($rotation))
    {
      $this->image->rotate($rotation);
    }

    $this->image->write($destination_filepath);

    // everything should be OK if we are here!
    return $this->get_resize_result($destination_filepath, $resize_dimensions['width'], $resize_dimensions['height'], $starttime);
  }

  static function get_resize_dimensions($width, $height, $max_width, $max_height, $rotation=null)
  {
    $rotate_for_dimensions = false;
    if (isset($rotation) and in_array(abs($rotation), array(90, 270)))
    {
      $rotate_for_dimensions = true;
    }

    if ($rotate_for_dimensions)
    {
      list($width, $height) = array($height, $width);
    }
    
    $ratio_width  = $width / $max_width;
    $ratio_height = $height / $max_height;
    $destination_width = $width; 
    $destination_height = $height;
    
    // maximal size exceeded ?
    if ($ratio_width > 1 or $ratio_height > 1)
    {
      if ($ratio_width < $ratio_height)
      { 
        $destination_width = round($width / $ratio_height);
        $destination_height = $max_height;
      }
      else
      { 
        $destination_width = $max_width; 
        $destination_height = round($height / $ratio_width);
      }
    }

    if ($rotate_for_dimensions)
    {
      list($destination_width, $destination_height) = array($destination_height, $destination_width);
    }

    return array(
      'width' => $destination_width,
      'height'=> $destination_height,
      );
  }

  static function get_rotation_angle($source_filepath)
  {
    list($width, $height, $type) = getimagesize($source_filepath);
    if (IMAGETYPE_JPEG != $type)
    {
      return null;
    }
    
    if (!function_exists('exif_read_data'))
    {
      return null;
    }

    $rotation = null;
    
    $exif = exif_read_data($source_filepath);
    
    if (isset($exif['Orientation']) and preg_match('/^\s*(\d)/', $exif['Orientation'], $matches))
    {
      $orientation = $matches[1];
      if (in_array($orientation, array(3, 4)))
      {
        $rotation = 180;
      }
      elseif (in_array($orientation, array(5, 6)))
      {
        $rotation = 270;
      }
      elseif (in_array($orientation, array(7, 8)))
      {
        $rotation = 90;
      }
    }

    return $rotation;
  }

  private function get_resize_result($destination_filepath, $width, $height, $time)
  {
    return array(
      'source'      => $this->source_filepath,
      'destination' => $destination_filepath,
      'width'       => $width,
      'height'      => $height,
      'size'        => floor(filesize($destination_filepath) / 1024).' KB',
      'time'	      => number_format((get_moment() - $time) * 1000, 2, '.', ' ').' ms',
      'library'     => $this->library,
    );
  }

  static function is_imagick()
  {
    return extension_loaded('imagick');
  }

  static function is_ext_imagick()
  {
    global $conf;

    if (!function_exists('exec'))
    {
      return false;
    }
    @exec($conf['ext_imagick_dir'].'convert -version', $returnarray, $returnvalue);
    if (!$returnvalue and !empty($returnarray[0]) and preg_match('/ImageMagick/i', $returnarray[0]))
    {
      return true;
    }
    return false;
  }

  static function is_gd()
  {
    return function_exists('gd_info');
  }

  static function get_library($library=null, $extension=null)
  {
    global $conf;

    if (is_null($library))
    {
      $library = $conf['image_library'];
    }

    // Choose image library
    switch (strtolower($library))
    {
      case 'auto':
      case 'imagick':
        if ($extension != 'gif' and self::is_imagick())
        {
          return 'imagick';
        }
      case 'ext_imagick':
        if ($extension != 'gif' and self::is_ext_imagick())
        {
          return 'ext_imagick';
        }
      case 'gd':
        if (self::is_gd())
        {
          return 'gd';
        }
      default:
        if ($library != 'auto')
        {
          // Requested library not available. Try another library
          return self::get_library('auto');
        }
    }
    return false;
  }

  function destroy()
  {
    if (method_exists($this->image, 'destroy'))
    {
      return $this->image->destroy();
    }
    return true;
  }
}

// +-----------------------------------------------------------------------+
// |                   Class for Imagick extension                         |
// +-----------------------------------------------------------------------+

class image_imagick implements imageInterface
{
  var $image;

  function __construct($source_filepath)
  {
    // A bug cause that Imagick class can not be extended
    $this->image = new Imagick($source_filepath);
  }

  function get_width()
  {
    return $this->image->getImageWidth();
  }

  function get_height()
  {
    return $this->image->getImageHeight();
  }

  function set_compression_quality($quality)
  {
    return $this->image->setImageCompressionQuality($quality);
  }

  function crop($width, $height, $x, $y)
  {
    return $this->image->cropImage($width, $height, $x, $y);
  }

  function strip()
  {
    return $this->image->stripImage();
  }

  function rotate($rotation)
  {
    $this->image->rotateImage(new ImagickPixel(), -$rotation);
    $this->image->setImageOrientation(Imagick::ORIENTATION_TOPLEFT);
    return true;
  }

  function resize($width, $height)
  {
    $this->image->setInterlaceScheme(Imagick::INTERLACE_LINE);
    return $this->image->resizeImage($width, $height, Imagick::FILTER_LANCZOS, 0.9);
  }

  function write($destination_filepath)
  {
    return $this->image->writeImage($destination_filepath);
  }
}

// +-----------------------------------------------------------------------+
// |            Class for ImageMagick external installation                |
// +-----------------------------------------------------------------------+

class image_ext_imagick implements imageInterface
{
  var $imagickdir = '';
  var $source_filepath = '';
  var $image_data = array();
  var $commands = array();

  function __construct($source_filepath, $imagickdir='')
  {
    $this->source_filepath = $source_filepath;
    $this->imagickdir = $imagickdir;

    $command = $imagickdir."identify -verbose ".realpath($source_filepath);
    @exec($command, $returnarray, $returnvalue);
    if($returnvalue)
    {
      die("[External ImageMagick] Corrupt image");
    }

    foreach($returnarray as $value)
    {
      $arr = explode(':', $value, 2);
      if (count($arr) == 2)
      {
        $this->image_data[trim($arr[0])] = trim($arr[1]);
      }
    }
  }

  function add_command($command, $params=null)
  {
    $this->commands[$command] = $params;
  }

  function get_width()
  {
    preg_match('#^(\d+)x#', $this->image_data['Geometry'], $match);
    return isset($match[1]) ? $match[1] : false;
  }

  function get_height()
  {
    preg_match('#^\d+x(\d+)(?:\+|$)#', $this->image_data['Geometry'], $match);
    return isset($match[1]) ? $match[1] : false;
  }

  function crop($width, $height, $x, $y)
  {
    $this->add_command('crop', $width.'x'.$height.'+'.$x.'+'.$y);
    return true;
  }

  function strip()
  {
    $this->add_command('strip');
    return true;
  }

  function rotate($rotation)
  {
    $this->add_command('rotate', -$rotation);
    $this->add_command('orient', 'top-left');
    return true;
  }

  function set_compression_quality($quality)
  {
    $this->add_command('quality', $quality);
    return true;
  }

  function resize($width, $height)
  {
    $this->add_command('interlace', 'line');
    $this->add_command('filter', 'Lanczos');
    $this->add_command('resize', $width.'x'.$height.'!');
    return true;
  }

  function write($destination_filepath)
  {
    $exec = $this->imagickdir.'convert';
    $exec .= ' '.realpath($this->source_filepath);

    foreach ($this->commands as $command => $params)
    {
      $exec .= ' -'.$command;
      if (!empty($params))
      {
        $exec .= ' '.$params;
      }
    }

    $dest = pathinfo($destination_filepath);
    $exec .= ' '.realpath($dest['dirname']).'/'.$dest['basename'];
    @exec($exec, $returnarray, $returnvalue);
    return !$returnvalue;
  }
}

// +-----------------------------------------------------------------------+
// |                       Class for GD library                            |
// +-----------------------------------------------------------------------+

class image_gd implements imageInterface
{
  var $image;
  var $quality = 95;

  function __construct($source_filepath)
  {
    $gd_info = gd_info();
    $extension = strtolower(get_extension($source_filepath));

    if (in_array($extension, array('jpg', 'jpeg')))
    {
      $this->image = imagecreatefromjpeg($source_filepath);
    }
    else if ($extension == 'png')
    {
      $this->image = imagecreatefrompng($source_filepath);
    }
    elseif ($extension == 'gif' and $gd_info['GIF Read Support'] and $gd_info['GIF Create Support'])
    {
      $this->image = imagecreatefromgif($source_filepath);
    }
    else
    {
      die('[Image GD] unsupported file extension');
    }
  }

  function get_width()
  {
    return imagesx($this->image);
  }

  function get_height()
  {
    return imagesy($this->image);
  }

  function crop($width, $height, $x, $y)
  {
    $dest = imagecreatetruecolor($width, $height);

    imagealphablending($dest, false);
    imagesavealpha($dest, true);
    if (function_exists('imageantialias'))
    {
      imageantialias($dest, true);
    }

    $result = imagecopymerge($dest, $this->image, 0, 0, $x, $y, $width, $height, 100);

    if ($result !== false)
    {
      imagedestroy($this->image);
      $this->image = $dest;
    }
    else
    {
      imagedestroy($dest);
    }
    return $result;
  }

  function strip()
  {
    return true;
  }

  function rotate($rotation)
  {
    $dest = imagerotate($this->image, $rotation, 0);
    imagedestroy($this->image);
    $this->image = $dest;
    return true;
  }

  function set_compression_quality($quality)
  {
    $this->quality = $quality;
    return true;
  }

  function resize($width, $height)
  {
    $dest = imagecreatetruecolor($width, $height);

    imagealphablending($dest, false);
    imagesavealpha($dest, true);
    if (function_exists('imageantialias'))
    {
      imageantialias($dest, true);
    }

    $result = imagecopyresampled($dest, $this->image, 0, 0, 0, 0, $width, $height, $this->get_width(), $this->get_height());

    if ($result !== false)
    {
      imagedestroy($this->image);
      $this->image = $dest;
    }
    else
    {
      imagedestroy($dest);
    }
    return $result;
  }

  function write($destination_filepath)
  {
    $extension = strtolower(get_extension($destination_filepath));

    if ($extension == 'png')
    {
      imagepng($this->image, $destination_filepath);
    }
    elseif ($extension == 'gif')
    {
      imagegif($this->image, $destination_filepath);
    }
    else
    {
      imagejpeg($this->image, $destination_filepath, $this->quality);
    }
  }

  function destroy()
  {
    imagedestroy($this->image);
  }
}

?>