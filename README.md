# Touristando-API

This API was developed for the purpose of providing information for the [Touristando](https://github.com/MatheusCrispim/Touristando) App

## Dependencies

- PHP >= 7.1.3
- MySQL 5.7 
- [Laravel 5.7 dependencies](https://laravel.com/docs/5.7/installation)
- Composer >= 1.6.5
- npm >= 6.7.0

### Installing
 
Clone and navigate to the root folder of this project

OS X & Linux:

```
composer install
```
* Create a database in MySQL
```
cp .env.example .env
```
```
Open the .env and set the database settings in the following fields:

DB_HOST = database Host
DB_DATABASE = database Name
DB_USERNAME = database user
DB_PASSWORD = database Password
```
```
php artisan key:generate
```
```
php artisan migrate
```

```
If you want to run this project in production, open .env and set the values for the following fields:

APP_ENV = production
APP_DEBUG = false 
```

### Running

```
php artisan serve
```

## Built With

* [Laravel](https://laravel.com/) - Serve-side framework used
* [Composer](https://getcomposer.org/) - Dependency manager for PHP
