# Legacy Login

_Seamless legacy user authentication for Craft CMS_

**A [Top Shelf Craft](https://topshelfcraft.com) creation**  
[Michael Rog](https://michaelrog.com), Proprietor



* * *



## TL;DR.

The _Legacy Login_ plugin provides a way to authenticate users from a legacy system into your Craft CMS site.

The `legacy-login/login` custom action stands in for Craft's native `login` form action. If a submitted `loginName`/`password` fails Craft's native authentication, the plugin checks the legacy system(s) and tries to authenticate a user from there. If a matching legacy user is found and authenticated, the plugin creates or updates the User in Craft and logs into the newly created/matched account.



## What legacy systems are supported?

Drivers are provided for authenticating legacy users from:

- Craft CMS 3.x
- Craft CMS 2.x
- ExpressionEngine 2.x

And, coming soon (as soon as they're ported from the Craft 2 version)...
- WordPress
- BigCommerce (Self-hosted)
- Wellspring



## Installation

1. From your project directory, use Composer to require the plugin package:

   ```
   composer require topshelfcraft/legacy-login
   ```
   
    _Note: Legacy Login is also available for installation via the Craft CMS Plugin Store._

2. In the Control Panel, go to **Settings → Plugins** and click the **“Install”** button for Legacy Login.

3. Finally, add the _Legacy Login_ form to your login template. The template follows the same design as Craft's native login form, except the form action should point to the _LegacyLoginController_ rather than Craft's native _UsersController_:

```twig
<form method="post" accept-charset="UTF-8">

    {{ csrfInput() }}
    {{ actionInput('legacy-login/login') }}

    <label for="loginName">Username or email</label>
    <input id="loginName" type="text" name="loginName" value="{{ loginName ?? '' }}">

    <label for="password">Password</label>
    <input id="password" type="password" name="password">

    <label>
        <input type="checkbox" name="rememberMe" checked>
        Remember me
    </label>

    <input type="submit" value="Login">

    {% if errorMessage ?? false %}
        {{ errorMessage }}
    {% endif %}

</form>
```



## Configuration

To customize the plugin's behavior, add a `legacy-login.php` file to your Craft config directory:

```php
<?php

return [
    'handlers' => [
        [
            'name' => "My Old EE Site",
            'type' => 'EE2',
            'db' => [ ... ],
        ]
    ]
];
``` 
The file should return an array, following the same format as Craft's own [Config files](https://craftcms.com/docs/3.x/config/).

(As with all Config files in Craft, the _Legacy Login_ config supports [Multi-Environment Configs](https://craftcms.com/docs/3.x/config/#multi-environment-configs) and other techniques for [Environmental Configuration](https://craftcms.com/docs/3.x/config/#environmental-configuration).)

#### `handlers`

The config file provides a list of Handler configurations.
 
Each Handler configuration *must* define a `name` and `type`.

Available options include:

##### `name`

What the handler should be called in Legacy Login records.
 
##### `type`

`'Craft3'`, `'Craft2'`, `'EE2'`, `'BigCommerce'`, `'Wellspring'`, `'WordPress'`, or a custom (fully qualified) class name.

Default: `'Craft3'`

##### `createUser`

Whether to create a new Craft user if a matching one doesn't already exist in the system.

(If `false`, only existing Users can be logged in via legacy handlers. Authentication for legacy users that don't match a User in the current system will fail, whether the loginName/password are correct or not.)

Default: `true`

##### `updateUser`

Whether to update the profile (including password) of a matched Craft user from the legacy data.

Default: `true`

##### `requirePasswordReset`

Whether to set the _Require Password Reset_ flag on a created/matched Craft user, i.e. requiring them to change their password upon their _next_ login.

Default: `false`

##### `maxLogins`

How many times a User may be authenticated using this handler.

Default: `1`

##### `table`

For database-type handlers: The name of the table from which legacy user data should be queried.

##### `db`

For database-type handlers: An array of database config options, following the same template as Craft's own [Database Connection Settings](https://craftcms.com/docs/3.x/config/#database-connection-settings).



## What are the system requirements?

Craft 3.5+ and PHP 7.1+



## I found a bug.

I'm not surprised... _Legacy Login_ is still in beta. Please open a GitHub Issue or submit a PR.



* * *

#### Contributors:

  - Plugin development: [Michael Rog](http://michaelrog.com) / @michaelrog
  - WordPress and Wellspring drivers for Craft 2: [Aaron Waldon](https://www.causingeffect.com) / @aaronwaldon
  - Initial port for Craft 3: [TJ Draper](https://buzzingpixel.com/) / @buzzingpixel
