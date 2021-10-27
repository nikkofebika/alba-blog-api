<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class TagController extends Controller
{
	public function index($id = null)
	{
		if ($id != null) {
			$tag = Tag::where('id', $id)->first();
		} else {
			$tag = Tag::all();
		}

		if ($tag) {
			return $this->sendResponse('tag found successfully', $tag, 201);
		}

		return $this->sendError('tag not found');
	}

	public function store(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'title' => 'required|unique:categories',
		]);

		if ($validator->fails()) {
			return $this->sendError('Invalid input', $validator->errors());
		}

		$tag = Tag::create([
			'title' => $request->input('title'),
			'seo_title' => Str::slug($request->input('title'), '-'),
		]);

		if ($tag) {
			return $this->sendResponse('tag created succesfully', $tag, 201);
		}

		return $this->sendError('Failed to create tag');
	}

	public function update(Request $request, $id)
	{
		$validator = Validator::make($request->all(), [
			'title' => 'required|unique:categories,title,'.$id,
		]);

		if ($validator->fails()) {
			return $this->sendError('Invalid input', $validator->errors());
		}

		$tag = Tag::where('id', $id)->first();

		if ($tag) {
			$tag->title = $request->input('title');
			$tag->seo_title = Str::slug($request->input('title'), '-');
			$tag->save();

			return $this->sendResponse('tag updated successfully', $tag);
		}

		return $this->sendError('tag not found');
	}

	public function destroy($id)
	{
		$tag = Tag::where('id', $id)->first();

		if ($tag) {
			$tag->delete();
			return $this->sendResponse('tag deleted successfully');
		}

		return $this->sendError('tag not found');
	}
}
