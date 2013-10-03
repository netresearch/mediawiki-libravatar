<?php
/**
 * This is a fork of the
 * Libravatar MediaWiki extension.
 * The original extension was written by Christian Weiske,
 * the fork was done by Philipp Spitzer
 *
 * PHP version 5.
 *
 * Provides a <libravatar email="foo@example.org"/> tag for MediaWiki
 * which displays the avatar image for a given email address.
 *
 * @category MediaWiki
 * @package  Libravatar
 * @author   Christian Weiske <christian.weiske@netresearch.de>
 * @author   Philipp Spitzer <philipp.spitzer@winterrodeln.org>
 * @license  AGPL http://www.gnu.org/licenses/agpl.txt
 * @link     http://www.mediawiki.org/wiki/Extension:Libravatar
 * @link     http://pear.php.net/package/Services_Libravatar/
 * @link     https://www.libravatar.org/
 */
if (!defined('MEDIAWIKI')) {
    die("This file is part of MediaWiki and is not a valid entry point\n");
}


$wgExtensionCredits['parserhook'][]= array(
    'name'         => 'Libravatar',
    'version'      => '0.2.0',
    'author'       => array('Christian Weiske', 'Philipp Spitzer'),
    'url'          => 'http://www.mediawiki.org/wiki/Extension:Libravatar',
    'description'  => 'Embed gravatars from libravatar.org'
);


// Global variables (to be changed in LocalSettings.ini)
$wgLibravatarSize = 32; // avatar image width and height
$wgLibravatarDefault = null; // default image: '404', 'mm', 'identicon', 'monsterid', 'wavatar', 'retro' or null
$wgLibravatarAlgorithm = 'md5'; // 'md5' or 'sha256'


// Auto load class and register hook
$wgAutoloadClasses['LibravatarExtension'] = dirname(__FILE__) . '/Libravatar.body.php';
$wgHooks['ParserFirstCallInit'][] = 'libravatarParserFirstCallInit';
$wgExtensionMessagesFiles['Libravatar'] = dirname( __FILE__ ) . '/Libravatar.i18n.php';

// Register the <libravatar/> tag with the MediaWiki parser
function libravatarParserFirstCallInit($wgParser) {
    $wgParser->setHook('libravatar', 'LibravatarExtension::render');
    return true;
}

?>
