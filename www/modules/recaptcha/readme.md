#  Kohana_reCAPTCHA


## License:

[The Unlicense](http://unlicense.org) (aka: public domain)

## Changelog:
### v.0.1 : February 23rd, 2011
* is_valid() method returned object. Method now has $type parameter and will accept 'object' or defaults to 'bool' and will return ﻿ReCaptchaResponse or boolean value respectivly.

### v.0.1 : February 17th, 2011
* Kohana_reCAPTCHA is now in the Public Domain

##### Thanks:

emptyhammond
### v0.1 : February 23rd, 2011 - Bug fixes
### v0.1 : February 17th, 2011 - Initial release

## Summary:

Kohana_reCAPCTHA is a module that facilitates the use of the reCAPTCHA library as a Kohana module.

## Instructions:
* A default recaptcha.php config file is included but you MUST change the public and private key values for the plugin to work.
* Create an instance of the Kohana Recaptcha object by calling Recaptcha::instance().
	* You can pass a string to the instance method to switch between different config settings, the default is ... default.
* There are only two methods:
	* get_html([$﻿publickey = NULL]) - pass your public key optionally if you want to use a different one from the config file you initialized the Recaptcha object with.
	* ﻿is_valid($_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"][, $privatekey = NULL, $type = 'bool']) - you can override your config private key value here. You must pass the values from the 'recaptcha_challenge_field' and 'recaptcha_response_field' inputs. Will return a boolean value by default. To return the ﻿ReCaptchaResponse object set type parameter to 'object'.
* The recaptchalib.php file bundled with the plugin is v1.11
* N.B. Please familiarise yourself with http://code.google.com/apis/recaptcha/docs/php.html

## Releases

Watch the [current tickets](http://github.com/emptyhammond/kohana_recaptcha/issues) to view the areas of active development.