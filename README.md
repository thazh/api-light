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

```php
<?php

require_once 'vendor/autoload.php';

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

$obj = new \Thazh\ApiLight\RestApi($options);

$output_data = "Hi, " . $obj->request['name'] . "! Your roll no is " . $obj->request['rollno'];

$obj->code = 200;
$obj->data = $output_data;

$obj->print();
```
