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
    'version'      => '0.1.0',
    'author'       => array('Christian Weiske', 'Philipp Spitzer'),
    'url'          => 'http://www.mediawiki.org/wiki/Extension:Libravatar',
    'description'  => 'Embed gravatars from libravatar.org'
);


// Global variables (to be changed in LocalSettings.ini)
$wgLibravatarSize = 32; // avatar image width and height
$wgLibravatarDefault = null; // default image: '404', 'mm', 'identicon', 'monsterid', 'wavatar', 'retro' or null
$wgLibravatarAlgorithm = 'md5'; // 'md5' or 'sha256'


$wgHooks['ParserFirstCallInit'][] = 'libravatarParserFirstCallInit';


/**
 * Register the <libravatar/> tag with the MediaWiki parser
 */
function libravatarParserFirstCallInit($wgParser)
{
    $wgParser->setHook('libravatar', 'mwLibravatarTagParse');
    return true;
}

/**
 * Generates the output for a <libravatar/> tag during parsing.
 *
 * @param string      $content Content of the tag
 *                             (between opening and closing tag)
 * @param array       $params  Array of tag parameters.
 * @param Parser      $parser  MediaWiki parser object
 * @param PPFrame_DOM $frame   Context data with e.g. template variables
 *
 * @return string HTML representation of the libravatar tag.
 *
 * @throws MWException In case Services_Libravatar is not available
 */
function mwLibravatarTagParse($content, $params, $parser, $frame)
{
    // setup variables
    global $wgLibravatarSize;
    global $wgLibravatarDefault;
    global $wgLibravatarAlgorithm;


    // parse attributes
    try {
        // user attribute (optional)
        $user = null;
        if (isset($params['user']))
            $user = $parser->recursiveTagParse($params['user'], $frame);

        // email attribute
        $email = null;
        if (isset($params['email']))   $email = $parser->recursiveTagParse($params['email'], $frame);
        else if (trim($content) != '') $email = $parser->recursiveTagParse(trim($content), $frame);
        else if (!is_null($user)) {
            // take email from MediaWiki user
            $mwuser = User::newFromName($user);
            // if the MediaWiki user does not exist we throw an exception
            if ($mwuser === false) throw new InvalidArgumentException('user does not exist'); 
            $email = $mwuser->getEmail();
        } else throw new InvalidArgumentException('email (or user) attribute missing');

        // validate email address
        if (!Sanitizer::validateEmail($email)) throw new InvalidArgumentException('email address invalid.');

        // size attribute
        $size = (int) $wgLibravatarSize; // default size
        if (isset($params['size'])) $size = (int) $parser->recursiveTagParse($params['size'], $frame);

        // default attribute
        $default = $wgLibravatarDefault;
        if (isset($params['default'])) $default = $parser->recursiveTagParse($params['default'], $frame);

        // algorithm attribute
        $algorithm = $wgLibravatarAlgorithm;
        if (isset($params['algorithm'])) $algorithm = $parser->recursiveTagParse($params['algorithm'], $frame);
        
        // title attribute
        $title = null;
        if (isset($params['title'])) {
            $title = $parser->recursiveTagParse($params['title'], $frame);
        }

        // class attribute
        $class = null;
        if (isset($params['class'])) {
            $class = $parser->recursiveTagParse($params['class'], $frame);
        }

        // style attribute
        $style = null;
        if (isset($params['style'])) {
            $style = $parser->recursiveTagParse($params['style'], $frame);
        }

    } catch (Exception $e) {
        return sprintf(
            '<span class="error">%s</span>',
            'Libravatar error: ' . htmlspecialchars($e->getMessage())
        );
    }

    
    // use Services_Libravatar library to get avatar URL
    include_once 'Services/Libravatar.php';
    if (!class_exists('Services_Libravatar')) {
        throw new MWException(
            'Libravatar: Services_Libravatar not available'
        );
    }
    $sla = new Services_Libravatar();
    $sla->detectHttps();
    $sla->setSize($size);
    $sla->setDefault($default);
    $sla->setAlgorithm($algorithm);
    $url = $sla->getUrl($email);


    // convert to HTML <img ... /> tag
    $doc = new DOMDocument();
    $img = $doc->appendChild($doc->createElement("img"));
    $img->setAttribute('src', $url);
    $img->setAttribute('alt', 'Avatar of ' . str_replace(array('@', '.'), array(' at ', ' dot '), $email));
    $img->setAttribute('width', sprintf('%d', $size));
    $img->setAttribute('height', sprintf('%d', $size));
    if (!is_null($title)) $img->setAttribute('title', $title);
    if (!is_null($class)) $img->setAttribute('class', $class);
    if (!is_null($style)) $img->setAttribute('style', $style);
    $html = $doc->saveHTML($img);


    // return result (markerType => nowiki prevents wiki formatting of the result)
    return array($html, 'markerType' => 'nowiki');
}

?>
