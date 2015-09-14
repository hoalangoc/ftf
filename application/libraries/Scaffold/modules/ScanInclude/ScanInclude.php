<?php

class ScanInclude
{
  public static $loaded = array();

  public static function import_process()
  {
      # Add the original file to the loaded array
      self::$loaded[] = Scaffold::$css->file;

      # Find all the @server imports
      Scaffold::$css->string = self::server_import(Scaffold::$css->string);
  }

  public static function server_import($css)
  {
    if( preg_match_all('/\@scan\s+(?:\'|\")([^\'\"]+)(?:\'|\")\s+(?:\'|\")([^\'\"]+)(?:\'|\")(\s+(?:\'|\")([^\'\"]+)(?:\'|\"))?\;/', $css, $matches) )
    {
      $files = array();
      foreach( $matches[0] as $index => $null )
      {
        $original_text = $matches[0][$index];
        $rel_scan_path = str_replace('/', DIRECTORY_SEPARATOR, $matches[1][$index]);
        $scan_suffix = $matches[2][$index];
        $ignore = @$matches[3][$index];
        $scan_path = SCAFFOLD_DOCROOT . DIRECTORY_SEPARATOR . $rel_scan_path;
        if($scan_path === APPLICATION_PATH_MOD) {
          $ignore = self::addDisabledModuleDirectories($ignore);
        }

        $contents = '';
        foreach( scandir($scan_path) as $subdir )
        {
          if( substr($subdir, 0, 1) === '.' ) continue;
          $full_path = $scan_path . '/'. $subdir . '/' . $scan_suffix;
          if( !@file_exists($full_path) ) continue;
          if( !self::isNotIgnored($subdir, $ignore) ) continue;
          // Already included
          if( in_array($full_path, self::$loaded) ) continue;
          // Get contentes
          self::$loaded[] = $full_path;

          $contents .= file_get_contents($full_path);
        }

        $css = str_replace($matches[0][0], $contents, $css);
      }
    }

    return $css;
  }

  public static function isNotIgnored($subdir, $extra)
  {
    $okay = true;

    if( !empty($extra) ) {
      if( is_string($extra) ) {
        $extra = explode(',', trim($extra, ' "'));
      }
      if( is_array($extra) ) {
        $extra = array_filter($extra);
        if( in_array($subdir, $extra) ) {
          $okay = false;
        }
      }
    }

    return $okay;
  }

  public static function addDisabledModuleDirectories($ignoreDir)
  {
    if(empty($ignoreDir)) {
      $ignoreDir = array();
    }
    if( is_string($ignoreDir) ) {
      $ignoreDir = explode(',', trim($ignoreDir, ' "'));
    }
    $file = SCAFFOLD_DOCROOT . '/application/settings/enabled_module_directories.php';
    if (!file_exists($file) || !($enabledModuleDirectories = include $file)) {
      return $ignoreDir;
    }

    foreach (scandir(APPLICATION_PATH_MOD) as $subdir) {
      if (substr($subdir, 0, 1) === '.' || !is_dir(APPLICATION_PATH_MOD . DS . $subdir) || in_array($subdir, $enabledModuleDirectories)) {
        continue;
      }
      array_push($ignoreDir, $subdir);
    }

    return $ignoreDir;
  }
}
