******************************
Libravatar MediaWiki extension
******************************

Provides a ``<libravatar email="foo@example.org"/>`` tag for MediaWiki__
which displays the avatar image for a given email address,
using the Libravatar__ service.
It is federated and falls back to Gravatar if no Libravatar is found.

__ http://www.mediawiki.org/
__ https://www.libravatar.org/


Examples
========
Simple avatar::

  <libravatar email="foo@example.org"/>

Avatar with special size::

  <libravatar email="foo@example.org" size="32"/>

Image title::

  <libravatar email="foo@example.org" title="Foo's avatar"/>

Verbose notation::

  <libravatar default="monsterid">foo@example.org</libravtar>




Supported attributes
====================
``email``
  Email address to show avatar of. Required.

``size``
  Size in pixels (avatars are quadratic)

``default``
  Default icon type when no avatar has been found.
  Options: 404, mm, identicon, monsterid, wavatar, retro

``algorithm``
  Email hashing algorithm. md5 or sha256

``title``
  Title attribute value for the image tag


Global configuration variables
==============================
- ``$wgLibravatarSize``
- ``$wgLibravatarDefault``
- ``$wgLibravatarAlgorithm``


Dependencies
============
Requires the `Services_Libravatar`__ package from PEAR.

__ http://pear.php.net/package/Services_Libravatar


Author
======

Christian Weiske, christian.weiske@netresearch.de
