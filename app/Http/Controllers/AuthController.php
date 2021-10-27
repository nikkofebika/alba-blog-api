<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class AuthController extends Controller
{
	public function register(Request $request){
		$validator = Validator::make($request->all(), [
			'name' => 'required',
			'email' => 'required|email|unique:users',
			'password' => 'required'
		]);

		if ($validator->fails()) {
			return response()->json([
				'success' => false,
				'message' => 'Invalid input',
				'data' => $validator->errors(),
			], 400);
		}

		$user = User::create([
			'name' => $request->input('name'),
			'email' => $request->input('email'),
			'password' => Hash::make($request->input('password')),
		]);

		if ($user) {
			return response()->json([
				'success' => true,
				'message' => 'User registered succesfully',
				'data' => $user,
			], 201);
		}

		return response()->json([
			'success' => false,
			'message' => 'Failed to register',
			'data' => null,
		], 400);
	}

	public function login(Request $request){
		$validator = Validator::make($request->all(), [
			'email' => 'required|email',
			'password' => 'required'
		]);

		if ($validator->fails()) {
			return response()->json([
				'success' => false,
				'message' => 'Invalid input',
				'data' => $validator->errors(),
			], 400);
		}

		$user = User::where("email", $request->input("email"))->first();

		if (Hash::check($request->input("password"), $user->password)) {
			$token = base64_encode(Str::random(40));
			$user->update([
				'api_token' => $token
			]);

			return response()->json([
				'success' => true,
				'message' => 'User authenticated succesfully',
				'data' => [
					'user' => $user,
					'token' => $token,
				],
			], 200);
		}

		return response()->json([
			'success' => false,
			'message' => "Credentials don't match",
			'data' => null,
		], 400);
	}
}
