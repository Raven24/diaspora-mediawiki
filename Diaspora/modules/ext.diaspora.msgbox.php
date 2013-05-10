<?php

/**
 * Message Box Functions for Diaspora extension
 *
 * @file
 * @ingroup Extensions
 */

class DiasporaExtMsgbox {
  protected static $tpl_msgbox =<<<'EOT'
  <div class="msgbox %1$s" id="msgbox_%2$s">
    <div class="img %3$s"></div>
    <div class="msg">%4$s</div>
  </div>
EOT;

  protected static $msgboxStyles = array();

  /**
   * add a generated inline-style element to the head section
   * for displaying the background images for the pictograms in msgboxes
   */
  protected static function addMsgboxStyles() {
    global $wgParser;

    foreach( self::$msgboxStyles as $key=>$val ) {
      $rand = DiasporaPFunctions::rand_str(7);
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
   * generate a message box from the given parameters.
   */
  public static function msgbox( $parser ) {

    $args = func_get_args();
    $msg = array_pop($args);

    $argv = DiasporaPFunctions::extractArgs( $args );

    $defaults = array(
      'type' => 'notice',
      'name' => 'blank',
      'image' => '',
      'date' => '',
      'sub'  => '',
      'cat'  => '',
    );

    $opts = array_merge($defaults, $argv);

    if( !$msg || empty($msg) ) {
      $msg = "ERROR: no content specified!";
    }

    $title = "";
    if( $opts['name'] != "blank" ) {
      $title = "<em>»» " . $opts['name'] . "</em><br>";
    }

    $sub = "";
    if( !empty($opts['sub']) ) {
      $sub = "<br>" . $opts['sub'];
    }

    $date = "";
    if( !empty($opts['date']) ) {
      $date = " <small>(" . $opts['date'] . ")</small>";
    }

    $cats = array();
    if( !empty($opts['cat']) ) {
      $cats = explode(",", $opts['cat']);
      $cats = array_map("DiasporaPFunctions::categorize", array_map("trim", $cats));
    }
    $cats = implode(" ", $cats);

    $img = "msgbox-" . DiasporaPFunctions::rand_str($opts['image'], 7);
    self::$msgboxStyles[$img] = DiasporaPFunctions::getImage($opts['image']);
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
