<?php
/**
 * Libravatar MediaWiki extension.
 *
 * PHP version 5.
 *
 * Provides a <libravatar email="foo@example.org"/> tag for MediaWiki
 * which displays the avatar image for a given email address.
 *
 * Supported attributes:
 * - email     - Email address to show avatar of. Required.
 * - size      - Size in pixels (avatars are quadratic)
 * - default   - Default icon type when no avatar has been found.
 *               Options: 404, mm, identicon, monsterid, wavatar, retro
 * - algorithm - Email hashing algorithm. md5 or sha256
 * - title     - Title attribute value for the image tag
 * - style     - "style" attribute value for the image tag
 *
 * Global configuration variables:
 * - $wgLibravatarSize
 * - $wgLibravatarDefault
 * - $wgLibravatarAlgorithm
 *
 * Requires the Services_Libravatar package from PEAR.
 *
 * Examples:
 * - Simple avatar:
 *   <libravatar email="foo@example.org"/>
 *
 * - Avatar with special size:
 *   <libravatar email="foo@example.org" size="32"/>
 *
 * - Image title:
 *   <libravatar email="foo@example.org" title="Foo's avatar"/>
 *
 * - Verbose notation:
 *   <libravatar default="monsterid">foo@example.org</libravtar>
 *
 * @category MediaWiki
 * @package  Libravatar
 * @author   Christian Weiske <christian.weiske@netresearch.de>
 * @license  AGPL http://www.gnu.org/licenses/agpl.txt
 * @link     http://www.mediawiki.org/wiki/Extension:Libravatar
 * @link     http://pear.php.net/package/Services_Libravatar/
 * @link     https://www.libravatar.org/
 */
if (!defined('MEDIAWIKI')) {
    die("This file is part of MediaWiki and is not a valid entry point\n");
}

$wgExtensionFunctions[] = 'mwLibravatarTagInit';

$wgExtensionCredits['Libravatar'][]= array(
    'name'         => 'Libravatar extension',
    'version'      => '0.1.0',
    'author'       => 'Christian Weiske <christian.weiske@netresearch.de>',
    'url'          => 'http://www.your.com/mynewtag/',
    'description'  => 'Embed gravatars from libravatar.org'
);

if (!isset($GLOBALS['wgLibravatarSize'])) {
    $GLOBALS['wgLibravatarSize'] = 32;
}

/**
 * Register the <libravatar/> tag with the MediaWiki parser
 *
 * @return void
 */
function mwLibravatarTagInit()
{
    global $wgParser;
    $wgParser->setHook('libravatar', 'mwLibravatarTagParse');
}

/**
 * Generates the output for a <libravatar/> tag during parsing.
 *
 * @param string $content Content of the tag (between opening and closing)
 * @param array  $params  Array of tag parameters.
 * @param object $parser  MediaWiki parser object
 *
 * @return string HTML representation of the libravatar tag.
 *
 * @throws MWException In case Services_Libravatar is not available
 */
function mwLibravatarTagParse($content, $params, $parser)
{
    include_once 'Services/Libravatar.php';
    if (!class_exists('Services_Libravatar')) {
        throw new MWException(
            'Libravatar: Services_Libravatar not available'
        );
    }
    $sla = new Services_Libravatar();
    $sla->detectHttps();

    $extra = '';

    try {
        if (!isset($params['email']) && $content != '') {
            $params['email'] = trim($content);
        } else {
            throw new InvalidArgumentException(
                'email attribute missing'
            );
        }
        if (isset($params['size'])) {
            $params['size'] = (int) $params['size'];
        } else if (isset($GLOBALS['wgLibravatarSize'])) {
            $params['size'] = (int) $GLOBALS['wgLibravatarSize'];
        }
        $sla->setSize($params['size']);
        $extra .= sprintf(
            ' width="%d" height="%d"', $params['size'], $params['size']
        );

        if (isset($params['default'])) {
            $sla->setDefault($params['default']);
        } else if (isset($GLOBALS['wgLibravatarDefault'])) {
            $sla->setDefault($GLOBALS['wgLibravatarDefault']);
        }
        if (isset($params['algorithm'])) {
            $sla->setAlgorithm($params['algorithm']);
        } else if (isset($GLOBALS['wgLibravatarAlgorithm'])) {
            $sla->setAlgorithm($GLOBALS['wgLibravatarAlgorithm']);
        }
    } catch (Exception $e) {
        return sprintf(
            '<span style="color:red">%s</span>',
            'Libravatar error: ' . htmlspecialchars($e->getMessage())
        );
    }

    if (isset($params['title'])) {
        $extra .= sprintf(' title="%s"', htmlspecialchars($params['title']));
    }

    return sprintf(
        '<img src="%s" alt="%s" width="%s"%s/>',
        htmlspecialchars($sla->getUrl($params['email'])),
        htmlspecialchars(
            'Avatar of '
            . str_replace(
                array('@', '.'),
                array(' at ', ' dot '),
                $params['email']
            )
        ),
        htmlspecialchars($params['size']),
        $extra
    );
}

?>
