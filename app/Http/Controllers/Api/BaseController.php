<?php

namespace App\Http\Controllers\Api;

use Response;

class BaseController extends Controller
{
    public function errorNotFound($message = '') {
        $result = array(
            'status'    => 'error',
            'message'   => $message,
        );
        return Response::json($result, 404);
    }

    public function validationError() {
        $result = array(
            'status'    => 'error',
            'message'   => 'input validation error',
        );
        return Response::json($result, 300);
    }

    public function respond($data, $message = 'success') {
        $result = array(
            'status'    => '200 OK',
            'message'   => $message,
            'data'      => $data
        );
        return Response::json($result, 200);
    }
}
