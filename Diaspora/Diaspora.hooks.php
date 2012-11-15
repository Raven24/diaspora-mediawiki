<?php
/**
 * Hooks for Diaspora extension
 *
 * @file
 * @ingroup Extensions
 */

class DiasporaHooks {

  protected static $features = array(
    // experimental modules, requires the user to enable them
    'experimental' => array(
      'preferences' => array(
        'diaspora-experimental' => array(
          'type' => 'toggle',
          'label-message' => 'diaspora-experimental-preference',
          'section' => 'rendering/advancedrendering',
        ),
      ),
      'requirements' => array(
        'diaspora-experimental' => true,
      ),
      'modules' => array(
        'ext.diaspora.stylesheet',
        'ext.diaspora.msgbox'
      ),
    ),
    // put non-experimental modules here
    'stable' => array(
      'modules' => array(
      ),
    ),
  );

  /**
   * check, if a given feature that has requirements is enabled by user preference
   */
  protected static function isEnabled( $name ) {
    global $wgUser;

    if( isset(self::$features[$name]['requirements']) ) {
      foreach( self::$features[$name]['requirements'] as $req => $val ) {
        if( $wgUser->getOption( $req ) != $val ) {
          return false;
        }
      }
    }

    return true;
  }

  /**
   * add modules to the page output
   */
  public static function beforePageDisplay( $out, $skin ) {
    if( $skin instanceof SkinVector ) {
      foreach( self::$features as $name => $feature ) {
        if( isset($feature['modules']) && self::isEnabled( $name ) ) {
          $out->addModules( $feature['modules'] );
        }
      }
    }
    return true;
  }

  /** 
   * add custom preferences to user page
   */
  public static function getPreferences( $user, &$defaultPreferences ) {
    foreach( self::$features as $name => $feature ) {
      if( isset($feature['preferences']) ) {
        foreach( $feature['preferences'] as $key => $opts ) {
          $defaultPreferences[$key] = $opts;
        }
      }
    }
    return true;
  }

  /**
   * add custom functions to the parser
   */
  public static function setupParserFunctions( &$parser ) {
    if( self::isEnabled('experimental') ) {
      $parser->setFunctionHook( 'msgbox', 'DiasporaPFunctions::msgbox' );
    }

    return true;
  }

}
