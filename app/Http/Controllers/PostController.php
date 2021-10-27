<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
	public function index($id = null)
	{
		if ($id != null) {
			$post = Post::where('id', $id)->first();
		} else {
			$post = Post::all();
		}

		if ($post) {
			return $this->sendResponse('Post found successfully', $post, 201);
		}

		return $this->sendError('Post not found');
	}

	public function store(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'title' => 'required|unique:categories',
		]);

		if ($validator->fails()) {
			return $this->sendError('Invalid input', $validator->errors());
		}

		$post = Post::create([
			'title' => $request->input('title'),
			'seo_title' => Str::slug($request->input('title'), '-'),
		]);

		if ($post) {
			return $this->sendResponse('Post created succesfully', $post, 201);
		}

		return $this->sendError('Failed to create post');
	}

	public function update(Request $request, $id)
	{
		$validator = Validator::make($request->all(), [
			'title' => 'required|unique:categories,title,'.$id,
		]);

		if ($validator->fails()) {
			return $this->sendError('Invalid input', $validator->errors());
		}

		$post = Post::where('id', $id)->first();

		if ($post) {
			$post->title = $request->input('title');
			$post->seo_title = Str::slug($request->input('title'), '-');
			$post->save();

			return $this->sendResponse('Post updated successfully', $post);
		}

		return $this->sendError('Post not found');
	}

	public function destroy($id)
	{
		$post = Post::where('id', $id)->first();

		if ($post) {
			$post->delete();
			return $this->sendResponse('Post deleted successfully');
		}

		return $this->sendError('Post not found');
	}
}
