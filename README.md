# Legacy Login

_Seamless legacy authentication for CraftCMS_

by Michael Rog  
[http://topshelfcraft.com](http://topshelfcraft.com)



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


### Setup

First, copy the `legacylogin` folder into your Craft plugins directory.

Then, navitage to the _Settings > Plugins_ area of the control panel, and _Install_ the plugin.

The plugin creates a data table for each supported legacy system (i.e. `craft_legacylogin_data_ee2` for storing ExpressionEngine member data).

Import your legacy user data into the appropriate title. The schema should more-or-less match up; Some fields may be mismatched/skipped based on the version of the legacy system you're using, but the key fields (the username, id, email, and password hash) will be covered.

Finally, add the _Legacy Login_ form to your login template. The template follows the same design as Craft's native login form, except the form action should point to the _LegacyLoginController_ rather than Craft's native _UsersController_:

```twig
<form method="post" accept-charset="UTF-8">

	{{ getCsrfInput() }}
	<input type="hidden" name="action" value="legacy-login/login">

	<label for="loginName">Username or email</label>
	<input id="loginName" type="text" name="loginName" value="{{ craft.session.rememberedUsername }}">

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


### Configuration

To customize the plugin's behavior, add a `legacylogin.php` file to your Craft config directory. (You can use `plugins/legacylogin/config.php` as a template.) The file should return an array; Like Craft's own General Configs, the _Legacy Login_ config supports Craft's [Multi-Environment Configs](https://craftcms.com/docs/multi-environment-configs) syntax.

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



### What are the system requirements?

Craft 3.0+ and PHP 7.1+



### I found a bug.

I'm not surprised... _Legacy Login_ is still in development. Please open a GitHub Issue, submit a PR, or just email me to let me know.



* * *

#### Contributors:

  - Plugin development: [Michael Rog](http://michaelrog.com) / @michaelrog
  - Craft 2 plugin - WordPress and Wellspring drivers: [Aaron Waldon](https://www.causingeffect.com) / @aaronwaldon
  - Craft 3 plugin - initial port: [TJ Draper](https://buzzingpixel.com/) / @buzzingpixel
