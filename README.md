# FirePHP support in Laravel 5

This Laravel 5 package brings FirePHP to Laravel 5.  By default it will log to your FireBug Console Database query information and log events.

## Pre-Installation

Before installing this package, you should make sure you have FirePHP Firefox addon installed and your Firebug Net panel enabled.

[Install Firebug](https://addons.mozilla.org/en-US/firefox/addon/firebug/)
[Install FirePHP](https://addons.mozilla.org/en-US/firefox/addon/firephp/)

## Installation

Add the GitHub repository to your composer file:
	"repositories": [
		{
			"type": "vcs",
			"url": "https://github.com/HolgerW1/laravel4-firephp"
		}
	]


Add `p3in/firephp` to your composer requirements:

	"require-dev": {
		"p3in/firephp": "dev-laravel5"
	}

Next, update Composer from the Terminal:

	$ composer update --dev

Once the package is installed, open your `config/app.php` configuration file and locate the providers key. Add the following line to the end.

	'P3in\Firephp\FirephpServiceProvider',

Finally, publish the default configuration (it will end up in app/config/packages/p3in/firephp/config.php):

	$ php artisan config:publish p3in/firephp


## Usage

You can use it in a number of ways.  

	// Use the built in Log system
	Log::info('This is some useful information.');
	Log::warning('Something could be going wrong.');
	Log::error('Something is really going wrong.');

	// Use the Firebug methods.
	FB::group('testGRoup');
		FB::info('This is some useful information.');
		FB::warn('Something could be going wrong.');
		FB::error('Something is really going wrong.');
	FB::groupEnd();

	// Or a combination of both.
	FB::group('testGRoup');
		Log::info('This is some useful information.');
		Log::warning('Something could be going wrong.');
		Log::error('Something is really going wrong.');
	FB::groupEnd();

Remember, by default all Logged events will be sent to your FireBug console.

Enjoy!  Please feel free to provide any feedback or feature requests.
