******************************
Libravatar MediaWiki extension
******************************

Provides a ``<libravatar email="foo@example.org"/>`` tag for MediaWiki__
which displays the avatar image for a given email address,
using the Libravatar__ service.
It is federated and falls back to Gravatar if no Libravatar is found.

See the Homepage__ and `source code`__.

__ http://www.mediawiki.org/
__ https://www.libravatar.org/
__ http://www.mediawiki.org/wiki/Extension:Libravatar
__ https://github.com/phispi/mediawiki-libravatar


Examples
========
Simple avatar::

  <libravatar email="foo@example.org"/>

Avatar with special size::

  <libravatar email="foo@example.org" size="32"/>

Image title::

  <libravatar email="foo@example.org" title="Foo's avatar"/>

Verbose notation and default avatar::

  <libravatar default="monsterid">foo@example.org</libravtar>

MediaWiki user's email address::

  <libravatar user="foo"/>


Supported attributes
====================
``email``
  Email address to show avatar of. Required (if no user is given)

``user``
  MediaWiki user name of an existing MediaWiki user having an attached email address.

``size``
  Size in pixels (avatars are quadratic)

``default``
  Default icon type when no avatar has been found.
  Options: 404, mm, identicon, monsterid, wavatar, retro

``algorithm``
  Email hashing algorithm. md5 or sha256

``alt``
  "alt" attribute value for the image tag

``title``
  "title" attribute value for the image tag

``style``
  "style" attribute value for the image tag

``class``
  "class" attribute value for the image tag


Global configuration variables
==============================
- ``$wgLibravatarSize``
- ``$wgLibravatarDefault``
- ``$wgLibravatarAlgorithm``



Installation
============
Phar
----
Copy the ``mediawiki-libravatar-0.2.0.phar`` file into your MediaWiki
``extensions/`` directory and add the following to your
``LocalSettings.php``::

  require_once "$IP/extensions/mediawiki-libravatar-0.2.0.phar";

That's it, it works now. All dependencies are included in the ``.phar`` file.

If you get a white page, then you're `probably running Suhosin`__.

__ http://cweiske.de/tagebuch/suhosin-phar.htm


Normal installation
-------------------

1. Run $ pear install Services_Libravatar-alpha
2. Copy this extension's ``Libravatar.php``, ``Libravatar.body.php``
   and ``Libravatar.i18n.php`` into the directory
   ``$mediawiki/extensions/Libravatar/``
3. Edit ``LocalSettings.php``::

    require_once "$IP/extensions/Libravatar/Libravatar.php";


Dependencies
============
Requires the `Services_Libravatar`__ package from PEAR.

__ http://pear.php.net/package/Services_Libravatar


Author
======

Christian Weiske, christian.weiske@netresearch.de
Philipp Spitzer, philipp.spitzer@winterrodeln.org
