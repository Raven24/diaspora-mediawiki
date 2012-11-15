<?php
/**
 * Diaspora extension
 *
 * @file
 * @ingroup Extensions
 *
 * most of the stuff you see here is 'inspired' by the 'Vector' extension
 * that comes bundled with the recent versions of the Mediawiki software
 *
 * @author Florian Staudacher
 * @license GPL v2 or later
 * @version 0.0.1
 */

 /* Setup */

$wgExtensionCredits['other'][] = array(
  'path' => __FILE__,
  'name' => 'Diaspora MW extension',
  'author' => array( 'Florian Staudacher' ),
  'version' => '0.0.1',
  'url' => 'https://github.com/Raven24/diaspora-mw',
  'descriptionmsg' => 'diaspora-desc',
);

$wgAutoloadClasses['DiasporaHooks'] = dirname(__FILE__) . '/Diaspora.hooks.php';
$wgAutoloadClasses['DiasporaPFunctions'] = dirname(__FILE__) . '/Diaspora.pfunctions.php';

$wgExtensionMessagesFiles['Diaspora'] = dirname(__FILE__) . '/Diaspora.i18n.php';
$wgExtensionMessagesFiles['DiasporaMagic'] = dirname(__FILE__) . '/Diaspora.i18n.magic.php';

$wgHooks['BeforePageDisplay'][] = 'DiasporaHooks::beforePageDisplay';
$wgHooks['GetPreferences'][] = 'DiasporaHooks::getPreferences';
$wgHooks['ParserFirstCallInit'][] = 'DiasporaHooks::setupParserFunctions';

$tplResources = array(
  'localBasePath' => dirname( __FILE__ ) . '/modules',
  'remoteExtPath' => 'Diaspora/modules',
  'group' => 'ext.diaspora',
);

$wgResourceModules += array(
  'ext.diaspora.stylesheet' => $tplResources + array(
    'styles' => 'ext.diaspora.stylesheet.css',
    'position' => 'top',
  ),
  'ext.diaspora.msgbox' => $tplResources + array(
    'styles' => 'ext.diaspora.msgbox.css',
  ),
);