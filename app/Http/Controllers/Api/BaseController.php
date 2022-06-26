<?php

namespace App\Http\Controllers\Api;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Response;
use stdClass;

class BaseController extends Controller
{
    public function errorNotFound($message = '') {
        $result = array(
            'status'    => 'error',
            'message'   => $message,
        );
        return Response::json($result, 404);
    }

    public function errorForbidden($message = '') {
        $result = array(
            'status'    => 'error',
            'message'   => $message,
        );
        return Response::json($result, 403);
    }

    public function validationError() {
        $result = array(
            'status'    => 'error',
            'message'   => 'Harap masukkan semua data yang dibutuhkan!',
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

    public function paginator($data,$per_page) {
        $data = $data->paginate($per_page);
        $result = new stdClass();
        
        $result->total = $data->total();
        $result->per_page = $data->perPage();
        $result->current_page = $data->currentPage();
        $result->last_page = $data->lastPage();
        $result->data = $data->toArray()['data'];
        
        return $result;
    }

    //Paginate Collection or Array
    public function paginateArray($items, $perPage, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        $data = new LengthAwarePaginator(array_values($items->forPage($page, $perPage)->toArray()), $items->count(), $perPage, $page, $options);

        $result = new stdClass();
        
        $result->total = $data->total();
        $result->per_page = $data->perPage();
        $result->current_page = $data->currentPage();
        $result->last_page = $data->lastPage();
        $result->data = $data->toArray()['data'];
        
        return $result;
    }
}
