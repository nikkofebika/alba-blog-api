<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function sendResponse($msg, $result = null, $code = 200) {
    	$res = [
            'success' => true,
            'message' => $msg,
        ];
        if($result != null){
            $res['data'] = $result;
        }
        return response()->json($res, $code);
    }

    public function sendError($msg, $result = null, $code = 400) {
    	$res = [
            'success' => false,
            'message' => $msg,
        ];
        if($result != null){
            $res['data'] = $result;
        }
        return response()->json($res, $code);
    }
}
