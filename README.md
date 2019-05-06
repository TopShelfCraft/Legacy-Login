# Legacy Login

_Seamless legacy authentication for CraftCMS_

by Michael Rog  
[https://topshelfcraft.com](https://topshelfcraft.com)



### TL;DR.

The _Legacy Login_ plugin provides a way to authenticate users from a legacy system into your Craft CMS site.

The plugin replaces the normal `login` form action. If a submitted `loginName`/`password` fails Craft's native authentication (i.e. the legacy user doesn't yet exist in the new Craft site), the plugin checks the legacy system(s) and tries to authenticate a user from there. If a matching legacy user is found and authenticated, the plugin imports the User into Craft and logs into the newly created account.

* * *



### What legacy systems are supported?

Drivers are provided for authenticating legacy users from:

- Craft CMS 3.x
- Craft CMS 2.x
- ExpressionEngine 2.x
- WordPress
- BigCommerce (Self-hosted)
- Wellspring


## Installation

1. From your project directory, use Composer to require the plugin package:

   ```
   composer require topshelfcraft/legacy-login
   ```

2. In the Control Panel, go to Settings → Plugins and click the “Install” button for Legacy Login.

3. Finally, add the _Legacy Login_ form to your login template. The template follows the same design as Craft's native login form, except the form action should point to the _LegacyLoginController_ rather than Craft's native _UsersController_:

```twig
<form method="post" accept-charset="UTF-8">

	{{ csrfInput() }}
	<input type="hidden" name="action" value="legacy-login/login">

	<label for="loginName">Username or email</label>
	<input id="loginName" type="text" name="loginName" value="{{ loginName ?? '' }}">

	<label for="password">Password</label>
	<input id="password" type="password" name="password">

	<label>
		<input type="checkbox" name="rememberMe" value="1">
		Remember me
	</label>

	<input type="submit" value="Login">

	{% if errorMessage is defined %}
		<p>{{ errorMessage }}</p>
	{% endif %}
	
</form>
```

_Note: Legacy Login is also available for installation via the Craft CMS Plugin Store._



### Configuration

To customize the plugin's behavior, add a `legacy-login.php` file to your Craft config directory. (You can use `plugins/legacylogin/config.php` as a template.) The file should return an array; Like Craft's own General Configs, the _Legacy Login_ config supports Craft's [Multi-Environment Configs](https://craftcms.com/docs/multi-environment-configs) syntax.

The following settings are available:

#### `handlers`

An _array_ defining the legacy authentication handlers.
 
Each handler takes the following options:
 
##### `type`

`'Craft3'`, `'Craft2'`, `'EE2'`, `'BigCommerce'`, `'Wellspring'`, `'WordPress'`, or a custom (fully qualified) class name.

Default: `'Craft3'`

##### `createNewUser`

A _boolean_ which determines whether to create a new Craft user if a matching one doesn't already exist in the system. (If `false`, only current Users can be logged in via legacy handlers. Authentication for legacy users that don't match a User in the current system will fail whether the loginName/password are correct or not.)

Default: `true`

##### `setPassword`

A _boolean_ which determines whether to set the password of a matched/created Craft user to match the legacy password.

Default: `true`

##### `requirePasswordReset`

A _boolean_ which determines whether to set the _Require Password Reset_ flag on a matched/created Craft user, i.e. requiring them to change their password upon their _next_ login.

Default: `false`

##### `userTable`

For database-backed handlers: the name of the table from which legacy user data should be queried.

##### `db`

For database-backed handlers: an array of database config options, following the same template as Craft's own [Database Connection Settings](https://docs.craftcms.com/v3/config/db-settings.html).



### What are the system requirements?

Craft 3.0+ and PHP 7.1+



### I found a bug.

I'm not surprised... _Legacy Login_ is still in development. Please open a GitHub Issue, submit a PR, or just email me to let me know.



* * *

#### Contributors:

  - Plugin development: [Michael Rog](http://michaelrog.com) / @michaelrog
  - Craft 2 plugin - WordPress and Wellspring drivers: [Aaron Waldon](https://www.causingeffect.com) / @aaronwaldon
  - Craft 3 plugin - initial Craft 3 port: [TJ Draper](https://buzzingpixel.com/) / @buzzingpixel
