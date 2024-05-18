# ApiLight
A lightweight library is used to build a secure REST API in PHP.

Features
--------

The main features provided by this library are:

 * Easy to use
 * Support all the PHP versions
 * Required field validation
 * Basic authentication
 * Support multiple content types
 * Support all the request methods

Quick Start
-----------

Install the library using [composer](https://getcomposer.org):

    composer require thazh/api-light

A sample PHP API to validate and process the input data and print the response data
```php
<?php

require_once 'vendor/autoload.php';

use Thazh\ApiLight\RestApi;

$options = array(
    'content_type' => 'application/json',
    'request_method' => 'POST',
    'credentials' => array(
        'testuser' => 'TestPwd@123',
    ),
    'required_fields' => array(
        'rollno',
        'name',
    )
);

$obj = new RestApi($options);

/* Custom business logic */
$output_data = "Hi, " . $obj->request['name'] . "! Your roll no is " . $obj->request['rollno'];

/* Set code 200 for the success response */
$obj->code = 200;

/* Set API response data */
$obj->data = $output_data;

/* Print the API response data */
$obj->print();

```
Post parameters:
```json
{
    "rollno": 10001,
    "name": "Saravanan S"
}
```
The above sample PHP API outputs the below response
```json
{
    "code": 200,
    "status": "success",
    "data": "Hi, Saravanan S! Your roll no is 10001"
}
```
