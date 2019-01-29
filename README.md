# Touristando-API

This API was developed for the purpose of providing information for the [Touristando](https://github.com/MatheusCrispim/Touristando)

## Dependencies

- PHP >= 7.1.3
- MySQL 5.7 
- [Laravel 5.7 dependencies](https://laravel.com/docs/5.7/installation)
- Composer >= 1.6.5

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
Open the .env and set the database settings in the following fields:
```
DB_HOST = database Host
DB_DATABASE = database Name
DB_USERNAME = database user
DB_PASSWORD = database Password
```
```
sudo php artisan key:generate
```
```
php artisan migrate
```
```
php artisan passport:install
```
```
php artisan storage:link
```
If you want to run this project in production, open .env and set the values for the following fields:
```
APP_ENV = production
APP_DEBUG = false 
```

### Running

```
php artisan serve
```

### User guide
* **User registration**

Before taking any action in the API, it is necessary to have a registered user. To create one, just send a POST request to the endpoint **/api/register** with the following structure:

```json
{
	"name": "Name",
	"email": "email@example.com",
	"password": "password123",
	"password_confirmation": "password123"
}
```

The expected response if the user is successfully registered is an authentication token with the following structure:

```json
{
    "token" "<Bearer Token>":
}
```

* **User login**

To perform actions on the API it is necessary that the user is authenticated. To do this, simply send a POST-type request to the **/api/login** endpoint with the following structure:

```json
{
	"email": "email@example.com",
	"password": "password123"
}
```
The expected response if the user authenticates successfully is an authentication token with the following structure:

```json
{
    "token" "<Bearer Token>":
}
```

***Note: To perform any of the following actions, the authentication token must be defined in the request header:***

```json
Authorization: Bearer <Token>
```

* **Attraction register**

To register a new attraction in the database, just send a POST request to the **/api/attractions** endpoint with the following structure

```json
{
	"name": "Name",
	"description": "descriptioon",
	"latitude": <latitude>, 
	"longitude": <longitude>,
	"image": "<Image Base64>"

}
```

* **Get attractions**

To get the attractions registered, simply send a GET request to one of the following endpoints:

```
/api/attractions - Return all registered attractions
/api/attractions/{id} - Returns the attraction that has the given id
/api/attractions/nearby/{latitude}/{longitude}/{radius} - Returns all nearby attractions of a latitude-longitude according to defined radius
```

* **Get attractions images** 

To get the images of a registered attraction, simply send a GET request to the following endpoit:

```
/api/attractions/{id}/images
```

* **Update attraction** 

To update the data of an attraction, simply send a PUT request to the endpoint / api / attractions / {id}, where possible any of the fields in the structure below:

```json
{
	"name": "Name",
	"description": "descriptioon",
	"latitude": <latitude>, 
	"longitude": <longitude>
}
```

* **Delete an attraction**

To delete an attraction, simply send a DELETE request to the following endpoint:
```
/api/attractions/{id}
```

* **Register a new image for an attraction**

To register a new image for an attraction, just send a POST request to the endpoint **/api/images** with the following structure:

```json
{
	"attraction_id": <Id attraction>,
    "image": "<Image Base64>"
}
```

* **Get details of an image**

To get details of an image, simply send a GET request to the following endpoint:

```
/api/images/{id}
```

* **Delete an image**

To delete an image, simply send a DELETE request to the following endpoint:

```
/api/images/{id}
```
* **Get user data**

To get the data of the logged in user, just send a GET request to the following endpoint:

```
/api/user
```

* **User logout**

To logout the user, just send a GET request to the following endpoint:

```
/api/logout
```

## Built With

* [Laravel](https://laravel.com/) - Serve-side framework used
* [Composer](https://getcomposer.org/) - Dependency manager for PHP
