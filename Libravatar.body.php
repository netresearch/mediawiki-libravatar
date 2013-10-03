<?php

class LibravatarExtension {
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
    function render($content, $params, $parser, $frame) {
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

            // email attribute (mandatory if no user attribute is given)
            $email = null;
            if (isset($params['email']))  $email = $parser->recursiveTagParse($params['email'], $frame);
            elseif (trim($content) != '') $email = $parser->recursiveTagParse(trim($content), $frame);
            elseif (!is_null($user)) {
                // take email from MediaWiki user
                $mwuser = User::newFromName($user);
                // if the MediaWiki user is invalid or does not exist we throw an exception
                if ($mwuser === false) throw new InvalidArgumentException('username has invalid characters');
                if ($mwuser->getId() == 0) throw new InvalidArgumentException('user does not exist');
                $email = $mwuser->getEmail();
            } else throw new InvalidArgumentException('email (or user) attribute missing');

            // validate email address
            if (!Sanitizer::validateEmail($email)) throw new InvalidArgumentException('email address invalid.');

            // size attribute (optional)
            $size = (int) $wgLibravatarSize; // default size
            if (isset($params['size'])) $size = (int) $parser->recursiveTagParse($params['size'], $frame);

            // default attribute (optional)
            $default = $wgLibravatarDefault;
            if (isset($params['default'])) $default = $parser->recursiveTagParse($params['default'], $frame);

            // algorithm attribute (optional)
            $algorithm = $wgLibravatarAlgorithm;
            if (isset($params['algorithm'])) $algorithm = $parser->recursiveTagParse($params['algorithm'], $frame);
            
            // alt attribute (optional)
            $alt = null;
            if (isset($params['alt'])) {
                $alt = $parser->recursiveTagParse($params['alt'], $frame);
            } elseif (is_null($user)) {
                $alt = 'Avatar of ' . str_replace(array('@', '.'), array(' at ', ' dot '), $email);
            } else {
                $alt = 'Avatar of ' . $user;
            }

            // title attribute (optional)
            $title = null;
            if (isset($params['title'])) {
                $title = $parser->recursiveTagParse($params['title'], $frame);
            }

            // class attribute (optional)
            $class = null;
            if (isset($params['class'])) {
                $class = $parser->recursiveTagParse($params['class'], $frame);
            }

            // style attribute (optional)
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
        $img->setAttribute('alt', $alt);
        $img->setAttribute('width', sprintf('%d', $size));
        $img->setAttribute('height', sprintf('%d', $size));
        if (!is_null($title)) $img->setAttribute('title', $title);
        if (!is_null($class)) $img->setAttribute('class', $class);
        if (!is_null($style)) $img->setAttribute('style', $style);
        $html = $doc->saveHTML($img);


        // return result (markerType => nowiki prevents wiki formatting of the result)
        return array($html, 'markerType' => 'nowiki');
    }

}

?>
