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
  'version' => '0.0.2',
  'url' => 'https://github.com/Raven24/diaspora-mediawiki',
  'descriptionmsg' => 'diaspora-desc',
);

$wgAutoloadClasses['DiasporaHooks']      = dirname(__FILE__) . '/Diaspora.hooks.php';
$wgAutoloadClasses['DiasporaExtMsgbox']  = dirname(__FILE__) . '/modules/ext.diaspora.msgbox.php';
$wgAutoloadClasses['DiasporaExtXtrplt']  = dirname(__FILE__) . '/modules/ext.diaspora.xtrplt.php';
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

// http://stackoverflow.com/a/8936492
function array_cartesian($arrays) {
  $result = array();
  $keys = array_keys($arrays);
  $reverse_keys = array_reverse($keys);
  $size = intval(count($arrays) > 0);
  foreach ($arrays as $array) {
    $size *= count($array);
  }
  for ($i = 0; $i < $size; $i ++) {
    $result[$i] = array();
    foreach ($keys as $j) {
      $result[$i][$j] = current($arrays[$j]);
    }
    foreach ($reverse_keys as $j) {
      if (next($arrays[$j])) {
        break;
      }
      elseif (isset ($arrays[$j])) {
        reset($arrays[$j]);
      }
    }
  }
  return $result;
}
