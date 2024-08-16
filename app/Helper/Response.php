<?php

namespace App\Helper;

class Response {
	public function pagination($data, $dataKey){
        $response = [
            'status' => 'Success',
            'message' => 'Data berhasil ditampilkan',
            'data' => [
                $dataKey => $data->items(),
            ],
            'pagination' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ],
        ];

        return response()->json($response, 200);
    }

    public function index($data){
        $response = [
            'status' => 'Success',
            'message' => 'Data berhasil ditampilkan',
            'data' => $data
        ];
        return response()->json($response, 200);
    }

    public function show($data){
        $response = [
            'status' => 'Success',
            'message' => 'Data berhasil diambil',
            'data' => $data
        ];
        return response()->json($response, 200);
    }

    public function store($data){
        $response = [
            'status' => 'Success',
            'message' => 'Data berhasil ditambah',
            'data' => $data
        ];
        return response()->json($response, 200);
    }

    public function update($data){
        $response = [
            'status' => 'Success',
            'message' => 'Data berhasil diedit',
            'data' => $data
        ];
        return response()->json($response, 200);
    }

    public function destroy($data){
        $response = [
            'status' => 'Success',
            'message' => 'Data berhasil dihapus',
            'data' => $data
        ];
        return response()->json($response, 200);
    }

    public function empty($data = []){
        $response = [
            'status' => 'Success',
            'message' => 'Data berhasil ditampilkan tetapi kosong',
            'data' => $data
        ];
        return response()->json($response, 200);
    }

    public function notFound($data = []){
        $response = [
            'status' => 'Error',
            'message' => 'Data tidak ditemukan',
            'data' => $data
        ];
        return response()->json($response, 404);
    }

    public function storeError($data = []){
        $response = [
            'status' => 'Error',
            'message' => 'Error menambah data',
            'data' => $data
        ];
        return response()->json($response, 500);
    }

    public function updateError($data = []){
        $response = [
            'status' => 'Error',
            'message' => 'Error mengedit data',
            'data' => $data
        ];
        return response()->json($response, 500);
    }

    public function destroyError($data = []){
        $response = [
            'status' => 'Error',
            'message' => 'Error menghapus data',
            'data' => $data
        ];
        return response()->json($response, 500);
    }

    public function validationError($message, $data = []){
        $valArr = array();
        foreach ($message->toArray() as $key => $value) { 
            $errStr = $value[0];
            array_push($valArr, $errStr);
        }

        $errStrFinal = '';

        if(!empty($valArr)){
            $errStrFinal = implode(', ', $valArr);
        }

        $response = [
            'status' => 'Error',
            'message' => $errStrFinal,
            'data' => $data
        ];
        return response()->json($response, 400);
    }
}