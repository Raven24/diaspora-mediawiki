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
    <div class="img" style="background-image: url('%3$s');"></div>
    <div class="msg">%4$s</div>
  </div>
EOT;

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
      'sub' => '',
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

    // parse the inner content again 
    $msg = $title . $msg . $sub . $date;
    $lp = new Parser();
    $po = $lp->parse($msg, $parser->mTitle, $parser->mOptions);
    $msg = $po->getText();

    // generate output string
    $text = sprintf(
      self::$tpl_msgbox,
      $opts['type'],
      Sanitizer::decodeCharReferencesAndNormalize( strtolower(preg_replace("/\s+/", "-", $opts['name'])) ),
      self::getImage($opts['image']),
      $msg
    );

    return array($text, 'isHTML' => true);
  }

}
