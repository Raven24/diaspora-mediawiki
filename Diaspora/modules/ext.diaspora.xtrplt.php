<?php

/**
 * Command extrapolation for Diaspora extension
 *
 * @file
 * @ingroup Extensions
 */

class DiasporaExtXtrplt  {

  protected static $tpl_command =<<<'EOT'
<syntaxhighlight%1$s>
%2$s
</syntaxhighlight>
EOT;

  /**
   * extract the env var name and options from the 'special' syntax
   * example:
   *   PRAM_NAME{option1,option2}
   */
  protected static function readVar( $param_str, &$params ) {
    if( preg_match("/(.+?)\{(.+?)\}/", $param_str, $matches) === 1 ) {
      if( strpos($matches[2], ',') !== false ) {
        $params[$matches[1]] = explode(',', $matches[2]);
      } else {
        $params[$matches[1]] = array($matches[2]);
      }
    }
    return null;
  }

  /**
   * split the list of env vars and return them as an array
   */
  protected static function splitVars( $params_str ) {
    if( strpos($params_str, '/') !== false ) {
      return explode('/', $params_str);
    }
    return array($params_str);
  }

  /**
   * takes the list of env vars and returns an array with a command
   * line for every possible combination of variables (cartesian product)
   */
  protected static function createCmdStrings( $params, $cmd_str ) {
    $param_list = self::splitVars($params);
    $param_arr = array();
    foreach($param_list as $env_var) {
      self::readVar($env_var, $param_arr);
    }

    $param_cart = array_cartesian($param_arr);
    $output_list = array();
    foreach($param_cart as $combinations) {
      $cmd_line = "";
      foreach($combinations as $env_var=>$val) {
        $cmd_line .= $env_var . '="' . $val . '" ';
      }
      $cmd_line .= $cmd_str;
      array_push($output_list, $cmd_line);
    }

    return $output_list;
  }

  /**
   * generate a list of command lines, one for every combination of
   * given env vars
   */
  public static function xtrplt( $parser ) {
    $args = func_get_args();
    $cmd = array_pop($args);

    $argv = DiasporaPFunctions::extractArgs( $args );

    $defaults = array(
      'params' => '',
      'lang'   => 'bash',
    );

    $opts = array_merge($defaults, $argv);

    if( !$cmd || empty($cmd) ) {
      $cmd = "ERROR: no command specified!";
    }

    $cmd_variations = array($cmd);
    if( !empty($opts['params']) ) {
      $cmd_variations = self::createCmdStrings($opts['params'], $cmd);
    }

    $lang = "";
    if( !empty($opts['lang']) ) {
      $lang = ' lang="'.$opts['lang'].'"';
    }

    $output_arr = array();
    foreach($cmd_variations as $cmd_line) {
      $line = sprintf(
        self::$tpl_command,
        $lang,
        $cmd_line
      );
      array_push($output_arr, $line);
    }

    $text = implode("\n\nor\n\n", $output_arr);
    return array($text, 'noparse' => false);
  }

}
