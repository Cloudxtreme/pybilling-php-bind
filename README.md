# pybilling-php-bind
RESTful API client for PyBilling (PHP)

Usage info in /tests.


# Running tests

PHPUnit is rquired
$ curl https://phar.phpunit.de/phpunit.phar -o phpunit.phar
$ chmod +x phpunit.phar
$ mv phpunit.phar /usr/local/bin/phpunit

Start the development server
$ ./manage.py runserver

Run tests
$ phpunit /path/to/folder/with/tests
