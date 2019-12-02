


# IQ-swagger

IQ-swagger is a package which converts docblocks into a user-friendly visual api-view (swagger)

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. See deployment for notes on how to deploy the project on a live system.

### Prerequisites

What things you need to install the software and how to install them

```
- laravel project (5.5 >)
- PHP 7 >
```

### Installing

Because we are running the package in bitbucket as private project you need to have a SSH key.

To add IQ-swagger to your laravel project you need to add the following lines to the composer.json file:
```php
    "require": {
        "Dennis1804/iq-swagger": "dev-default",
    },
"repositories": [ { "type": "vcs", "url": "https://github.com/dennis1804/iq-swagger" } ]

```
Next you want to run the `composer update` command to download the project into the vendor folder.
Then you need to open your `config/app.php` file and add the Serviceprovider for the package.
```
Dennis1804\IqSwagger\SwaggerServiceProvider::class,
```
Next to that it's recommended to add the JWT-auth package. this will provide an auth-token based login for your API.
It will also work with the swagger documentation which requires a token to make most api-calls.

now that the code is ready, you can add the javascripts to your public folder with: `php artisan vendor:publish --provider=SwaggerServiceProvider`

## php artisan iq:swagger

The package also comes with an artisan command which has to be executed every time you have edited a docblock.
this command will read all the docblocks and format them to a swagger.json file.
all you have to do is execute the following command:
`php artisan iq:swagger`
This will read all the routes which have `/api` in it.


## The docblock

The docblock is almost the normal docblock you would write (in sublime type: doc_f) except there is one change.
because swagger needs to know if the input is required or not you need to add a boolean in the inputs like so:
```php
    /**
     * function authenticate
     *
     * @consumes multipart/form-data
     *
     * @param string email The email-address of the user true
     * @param string password the users password true
     * 
     * @return json
     * @author Dennis
     **/
```
We will go from top to bottom with the docblock and explain what the package does with the lines.

###function
`function authenticate` just declares that the name of the function is "authenticate".


 

### Consumes
```
* @consumes multipart/form-data
```
Consumes is a custom added function which can identify what type of data the application needs.
The following types are available.
```
"multipart/form-data",                  (POST)
"application/x-www-form-urlencoded"     (GET)
```

### params
in the `@param` attribute you can identify what content you expect from the `@consumes`
*like the example, the method was post; and it requires an string: `email` and a string: `password`.*
```
 * @param string email The email-address of the user true
 * @param string password the users password true
```
The param has a couple of attributes;
```
Identifier                      @param
Type                            string
Name                            email
Description                     The email-address of the user
Required                        true

```
#### Type
There are multiple types available to choose from;
The most important ones:
```

    string
    number
    integer
    boolean
    array
    object
    file

```
#### Name
This is just a name you can give the input-field.
#### Required
Boolean, true or false. indicates if the element is required or not.
#### Description
A short-description of the element.


### url
This is a customized `@param`, it uses the same attributes the only difference is that it identifies the url - parameters.
if we have a route like this:
```php   
 Route::post('data/{checklistId}',  'Api\V1\Checklist\ChecklistController@pushData')    ->name('api.v1.checklist.pushData');
```
you can add a url param:
```
@url number checklistId required the checklist id 
```
now can fill the url-param in the swagger-page.


### return
identifies what type of content you return.
can either be one of these;
```
"application/xml"
"application/json"

```
### author
your name.


## accessing the documentation:
you can just go to : `http://your-domain.dev/api` after you executed the `php artisan iq:swagger` command.

