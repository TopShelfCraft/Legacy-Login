# Legacy Login

a plugin for Craft CMS
by Michael Rog
[http://topshelfcraft.com](http://topshelfcraft.com)



### TL;DR.

The _Legacy Login_ plugin provides a way to authenticate users from a legacy system into your Craft CMS site.

The plugin intercepts your login form action. If it fails Craft's native authentication (i.e. the legacy user doesn't yet exist in the new Craft site), the plugin checks the legacy system's table and tries to authenticate a user from there.

Drivers are provided for authenticating legacy users from:

- ExpressionEngine 2.x
- BigCommerce (Self-hosted)

In the future, I'd like to add drives for:

- WordPress



### What are the system requirements?

Craft 2.6+ and PHP 5.4+



### I found a bug.

I'm not surprised... _Legacy Login_ is still in development. Please open a GitHub Issue, submit a PR, or just email me to let me know.




* * *

#### Contributors:

  - Plugin development: [Michael Rog](http://michaelrog.com) / @michaelrog