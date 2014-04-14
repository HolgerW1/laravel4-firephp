# FirePHP support in Laravel 4

This Laravel 4 package brings FirePHP to Laravel 4.  By default it will log to your FireBug Console Database query information and log events.


## Installation

Install the package through Composer.

	"require-dev": {
		"p3in/firephp": "dev-master"
	}

Next, update Composer from the Terminal:

	composer update --dev

Once this operation completes, the final step is to add the service provider. Open `app/config/app.php`, and add a new item to the providers array.

	'P3in\Firephp\FirephpServiceProvider',

## Usage

You can use it in a number of ways.  

	// Use the built in Log system
	Log::info('This is some useful information.');
	Log::warning('Something could be going wrong.');
	Log::error('Something is really going wrong.');

	// Use the Firebug methods.
	FB::group('testGRoup');
		FB::info('This is some useful information.');
		FB::warning('Something could be going wrong.');
		FB::error('Something is really going wrong.');
	FB::endGroup();

	// Or a combination of both.
	FB::group('testGRoup');
		Log::info('This is some useful information.');
		Log::warning('Something could be going wrong.');
		Log::error('Something is really going wrong.');
	FB::endGroup();

Remember, by default all Logged events will be sent to your FireBug console.

Enjoy!  Please feel free to provide any feedback or feature requests.