<?php

/**
 * Parser Functions for Diaspora extension
 *
 * @file
 * @ingroup Extensions
 */

class DiasporaPFunctions {

  protected static $tpl_msgbox =<<<'EOT'
  <div class="msgbox %1$s" id="msgbox_%2$s">
    <div class="img %3$s"></div>
    <div class="msg">%4$s</div>
  </div>
EOT;

  protected static $msgboxStyles = array();

  /**
   * build associative arguments from flat parameter list
   * @see http://www.mediawiki.org/wiki/Extension:CategoryTree
   */
  protected static function extractArgs( $params ) {
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
   * if the image is not found, the logo is used
   */
  protected static function getImage( $name ) {
    global $wgLogo, $IP;

    $file = wfFindFile( $name );
    if( $file ) {
      $path = $file->getFullUrl();
    } else {
      $path = $wgLogo;
    }

    return $path;
  }

  /**
   * wraps the given name string to resemble a category link
   */
  public static function categorize( $name ) {
    if( empty($name) ) {
      return "";
    }
    return sprintf("[[Category: %s]]", $name);
  }

  /**
   * generate a 'random' string of arbitrary length
   */
  protected static function rand_str( $str, $len=5 ) {
    return substr(md5($str), 0, $len);
  }

  /**
   * add a generated inline-style element to the head section
   * to display the background images in msgboxes
   */
  protected static function addMsgboxStyles() {
    global $wgParser;

    foreach( self::$msgboxStyles as $key=>$val ) {
      $rand = self::rand_str(7);
      $style = sprintf( <<<'EOT'
<style>
  .%s { background-image: url('%s'); }
</style>
EOT
, $key, $val);
      $wgParser->mOutput->addHeadItem($style);
    }

    self::$msgboxStyles = array();
  }

  /**
   * generate a message box with the given parameters
   */
  public static function msgbox( $parser ) {

    $args = func_get_args();
    $msg = array_pop($args);

    $argv = self::extractArgs( $args );

    $defaults = array(
      'type' => 'notice',
      'name' => 'blank',
      'image' => '',
      'date' => '',
      'sub'  => '',
      'cat'  => '',
    );

    $opts = array_merge($defaults, $argv);

    if( !$msg || $msg == "" ) {
      $msg = "Error: no content given!";
    }

    $title = "";
    if( $opts['name'] != "blank" ) {
      $title = "<em>»» " . $opts['name'] . "</em><br>";
    }

    $sub = "";
    if( $opts['sub'] != "" ) {
      $sub = "<br>" . $opts['sub'];
    }

    $date = "";
    if( $opts['date'] != "" ) {
      $date = " <small>(" . $opts['date'] . ")</small>";
    }

    $cats = array();
    if( $opts['cat'] != "" ) {
      $cats = explode(",", $opts['cat']);
      $cats = array_map("DiasporaPFunctions::categorize", array_map("trim", $cats));
    }
    $cats = implode(" ", $cats);

    $img = "msgbox-" . self::rand_str($opts['image'], 7);
    self::$msgboxStyles[$img] = self::getImage($opts['image']);
    self::addMsgboxStyles();

    // concatenate all the contents
    $msg = $title . $msg . $sub . $date . $cats;

    // generate output string
    $text = sprintf(
      self::$tpl_msgbox,
      $opts['type'],
      Sanitizer::decodeCharReferencesAndNormalize( strtolower(preg_replace("/\s+/", "-", $opts['name'])) ),
      $img,
      $msg
    );

    return array($text, 'noparse' => true);
  }

}
