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

$wgHooks['ParserFirstCallInit'][] = 'libravatarParserFirstCallInit';

$wgExtensionCredits['parserhook'][]= array(
    'name'         => 'Libravatar',
    'version'      => '0.1.0',
    'author'       => array('Christian Weiske', 'Philipp Spitzer'),
    'url'          => 'http://www.mediawiki.org/wiki/Extension:Libravatar',
    'description'  => 'Embed gravatars from libravatar.org'
);

if (!isset($GLOBALS['wgLibravatarSize'])) {
    $GLOBALS['wgLibravatarSize'] = 32;
}


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
        // email attribute
        if (isset($params['email'])) {
            //all fine
        } else if (trim($content) != '') {
            $params['email'] = trim($content);
        // take email from MediaWiki user
        } else if (isset($params['user'])) {
            $user = User::newFromName($params['user']);
            if ($user === false) throw new InvalidArgumentException('user does not exist'); // maybe show a special image in that case?
            $params['email'] = $user->getEmail();
        } else {
            throw new InvalidArgumentException(
                'email (or user) attribute missing'
            );
        }

        // validate email address
        if (!Sanitizer::validateEmail($params['email'])) throw new InvalidArgumentException('email address invalid.');
        $params['email'] = $parser->recursiveTagParse(
            $params['email'], $frame
        );

        // size attribute
        if (isset($params['size'])) {
            $params['size'] = (int) $params['size'];
        } else if (isset($GLOBALS['wgLibravatarSize'])) {
            $params['size'] = (int) $GLOBALS['wgLibravatarSize'];
        }
        $params['size'] = $parser->recursiveTagParse(
            $params['size'], $frame
        );
        $sla->setSize($params['size']);
        $extra .= sprintf(
            ' width="%d" height="%d"', $params['size'], $params['size']
        );

        // default attribute
        if (isset($params['default'])) {
            // ok
        } else if (isset($GLOBALS['wgLibravatarDefault'])) {
            $params['default'] = $GLOBALS['wgLibravatarDefault'];
        }
        if (isset($params['default'])) {
            $params['default'] = $parser->recursiveTagParse(
                $params['default'], $frame
            );
            $sla->setDefault($params['default']);
        }

        // algorithm attribute
        if (isset($params['algorithm'])) {
            // ok
        } else if (isset($GLOBALS['wgLibravatarAlgorithm'])) {
            $params['algorithm'] = $GLOBALS['wgLibravatarAlgorithm'];
        }
        if (isset($params['algorithm'])) {
            $params['algorithm'] = $parser->recursiveTagParse(
                $params['algorithm'], $frame
            );
            $sla->setAlgorithm($params['algorithm']);
        }
    } catch (Exception $e) {
        return sprintf(
            '<span class="error">%s</span>',
            'Libravatar error: ' . htmlspecialchars($e->getMessage())
        );
    }

    if (isset($params['title'])) {
        $extra .= sprintf(
            ' title="%s"',
            htmlspecialchars(
                $parser->recursiveTagParse(
                    $params['title'], $frame
                )
            )
        );
    }

    if (isset($params['style'])) {
        $extra .= sprintf(
            ' style="%s"',
            htmlspecialchars(
                $parser->recursiveTagParse(
                    $params['style'], $frame
                )
            )
        );
    }

    return sprintf(
        '<img src="%s" alt="%s" %s/>',
        htmlspecialchars($sla->getUrl($params['email'])),
        htmlspecialchars(
            'Avatar of '
            . str_replace(
                array('@', '.'),
                array(' at ', ' dot '),
                $params['email']
            )
        ),
        $extra
    );
}

?>
