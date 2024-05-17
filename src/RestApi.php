<?php

namespace Thazh\ApiLight;

class RestApi
{

    public $code;
    public $status;
    public $message;
    public $data;
    public $content_type;
    public $request_method;
    public $credentials;
    public $required_fields;
    public $request;
    public $response;

    function __construct($options = array())
    {

        $this->code = 400;
        $this->status = 'failed';
        $this->message = '';
        $this->data = array();
        $this->request_method = isset($options['request_method']) ? $options['request_method'] : 'REQUEST';
        $this->content_type = isset($options['content_type']) ? $options['content_type'] : 'application/x-www-form-urlencoded';
        $this->credentials = isset($options['credentials']) ? $options['credentials'] : array();
        $this->required_fields = isset($options['required_fields']) ? $options['required_fields'] : array();

        $this->validate();
    }

    function checkReqMethod()
    {

        if ($_SERVER['REQUEST_METHOD'] != strtoupper($this->request_method)) {
            $this->message = 'Bad Request';
            $this->code = 400;
            $this->print();
        }
    }

    function checkReqContentType()
    {

        $valid = false;
        if (strtolower($this->content_type) == 'application/json') {
            $api_req_data_content = file_get_contents('php://input');
            if ($this->json_validator($api_req_data_content)) {
                $this->request = json_decode($api_req_data_content, true);
                $valid = true;
            } else {
                $this->message = 'Bad Request';
            }
        } else {
            if ($this->request_method == 'POST')
                $this->request = $_POST;
            else if ($this->request_method == 'GET')
                $this->request = $_GET;
            else if ($this->request_method == 'REQUEST')
                $this->request = $_REQUEST;
            $valid = true;
        }
        if (!$valid) {
            $this->code = 400;
            $this->print();
        }
    }

    function json_validator($data)
    {
        if (!empty($data)) {
            return is_string($data) && is_array(json_decode($data, true)) ? true : false;
        }
        return false;
    }

    function checkAuth()
    {

        $valid = false;
        $username = !empty($_SERVER['PHP_AUTH_USER']) ? trim($_SERVER['PHP_AUTH_USER']) : '';
        $password = !empty($_SERVER['PHP_AUTH_PW']) ? trim($_SERVER['PHP_AUTH_PW']) : '';

        if ($username != '' && $password != '') {
            if (isset($this->credentials[$username]) && $this->credentials[$username] == $password) {
                $valid = true;
            } else {
                $this->message = 'Authentication Failed';
            }
        } else {
            $this->message = 'Authentication Failed';
        }
        if (!$valid) {
            $this->code = 401;
            $this->print();
        }
    }

    function checkEmpty($input)
    {

        $missing = array();
        if (!empty($input)) {
            foreach ($input as $i) {
                if (!isset($this->request[$i]) || trim($this->request[$i]) == '') {
                    $missing[] = $i;
                }
            }
        }
        if (!empty($missing)) {
            if (count($missing) > 1)
                $this->message = "Parameters " . implode(', ', $missing) . " are empty";
            else
                $this->message = "Parameter " . implode(', ', $missing) . " is empty";
            $this->code = 422;
            $this->print();
        }
    }

    function validate()
    {

        if (isset($this->request_method) && !empty($this->request_method)) {
            $this->checkReqMethod();
        }
        $this->checkReqContentType();
        if (isset($this->credentials) && !empty($this->credentials)) {
            $this->checkAuth();
        }
        if (isset($this->required_fields) && !empty($this->required_fields)) {
            $this->checkEmpty($this->required_fields);
        }
    }

    function print()
    {

        if ($this->code == 200)
            $this->status = 'success';
        else
            $this->status = 'failed';
        $this->response['code'] = $this->code;
        $this->response['status'] = $this->status;
        if ($this->message != '')
            $this->response['message'] = $this->message;
        if (!empty($this->data))
            $this->response['data'] = $this->data;
        $json_response = json_encode($this->response);
        header('Content-type: application/json; charset=utf-8');
        http_response_code($this->code);
        echo $json_response;
        exit;
    }
}
