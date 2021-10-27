<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index($id = null)
    {
        if ($id != null) {
            $user = User::where('id', $id)->first();
        } else {
            $user = User::all();
        }

        if ($user) {
            return $this->sendResponse('User found successfully', $user, 201);
        }

        return $this->sendError('User not found');
    }
}
