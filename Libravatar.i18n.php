<?php
// Internationalisation file for extension Libravatar.
$messages = array();


// There are different types of entries. All are expected to be UTF-8 encoded.
// * Text: In principle, all characters are allowed and will be escaped before showing them.
// * HTML: This entries are _not_ escaped therefore HTML tags can be used but special characters as '<' have to be escaped here with htmlspecialchars.
// * WikiText: WikiText syntax is allowed and interpreted by MediaWiki.

$messages['qqq'] = array(
    'libravatar-description' => 'Description of the extension. WikiText.',
    'libravatar-error' => 'Text to wrap a Libravatar error message. Text.',
    'libravatar-invalidusername' => 'Error message that appears when a username with invalid characters is given. Text.',
    'libravatar-userunknown' => 'Error message that appears when a user name of a non-existing mediawiki user is given. Text.',
    'libravatar-noemail' => 'Error message that appears when no email address (and no user) is given. Text.',
    'libravatar-invalidemail' => 'Error message that appears when the given email address is no validly formatted email address. Text.',
    'libravatar-avatarof' => 'Text appearing in the alt tag of the image saying that this is the avatar of $1. Text.'
);


$messages['en'] = array(
    'libravatar-description' => 'Embed gravatars from libravatar.org',
    'libravatar-error' => 'Libravatar error: $1',
    'libravatar-invalidusername' => 'username has invalid characters',
    'libravatar-userunknown' => 'user does not exist',
    'libravatar-noemail' => 'email (or user) attribute missing',
    'libravatar-invalidemail' => 'email address invalid.',
    'libravatar-avatarof' => 'Avatar of $1'
);


$messages['de'] = array(
    'libravatar-description' => 'Gravatare von libravatar.org einbinden.',
    'libravatar-error' => 'Libravatar Fehler: $1',
    'libravatar-invalidusername' => 'Der Benutzername enthält ungültige Zeichen.',
    'libravatar-userunknown' => 'Der Benutzer existiert nicht.',
    'libravatar-noemail' => 'Es wurde keine E-Mailadresse (bzw. kein Benutzername) angegeben.',
    'libravatar-invalidemail' => 'Die angegebene E-Mailadresse ist ungültig.',
    'libravatar-avatarof' => 'Avatar von $1'
);


$messages['de-formal'] = array(
);


?>
