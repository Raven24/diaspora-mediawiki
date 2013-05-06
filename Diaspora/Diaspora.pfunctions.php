<?php

/**
 * Parser Functions for Diaspora extension
 *
 * @file
 * @ingroup Extensions
 */

class DiasporaPFunctions {

  /**
   * generate a 'random' string (md5) of a given input string and
   * return the first few characters of it, specified by the given length
   */
  public static function rand_str( $str, $len=5 ) {
    return substr(md5($str), 0, $len);
  }

  /**
   * build associative arguments from flat parameter list
   * @see http://www.mediawiki.org/wiki/Extension:CategoryTree
   */
  public static function extractArgs( $params ) {
    array_shift( $params ); // first is $parser, strip it

    $argv = array();
    foreach ( $params as $p ) {
      if ( preg_match( '/^\s*(\S.*?)\s*=\s*(.*?)\s*$/', $p, $m ) ) {
        $k = $m[1];
        $v = preg_replace( '/^"\s*(.*?)\s*"$/', '$1', $m[2] ); // strip any quotes enclusing the value
      } else {
        $k = trim( $p );
        $v = true;
      }
      $argv[$k] = $v;
    }

    return $argv;
  }

  /**
   * if the file for the given image is not found, the logo which is configured
   * in the MediaWiki settings is used
   */
  public static function getImage( $name ) {
    global $wgLogo;

    $file = wfFindFile( $name );
    if( $file ) {
      $path = $file->getFullUrl();
    } else {
      $path = $wgLogo;
    }

    return $path;
  }

  /**
   * wraps the given 'name' string to resemble the wiki markup for a category link
   */
  public static function categorize( $name ) {
    if( empty($name) ) {
      return "";
    }
    return sprintf("[[Category: %s]]", $name);
  }

}
