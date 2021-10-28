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
		$data = [];
		if ($id != null) {
			$tag = Tag::where('id', $id)->first();
			if (!$tag) {
				return $this->sendError('Tag not found');
			}
			$data = $tag->toArray();
			$data['total_posts'] = count($tag->posts);
		} else {
			$tags = Tag::all();
			if (count($tags) > 0) {
				foreach ($tags as $tag) {
					$data[$tag->id] = $tag->toArray();
					$data[$tag->id]['total_posts'] = count($tag->posts);
				}
			}
		}

		if (count($data) > 0) {
			return $this->sendResponse('Tag found successfully', $data, 201);
		}

		return $this->sendError('Tag not found');
	}

	public function store(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'title' => 'required|unique:tags',
		]);

		if ($validator->fails()) {
			return $this->sendError('Invalid input', $validator->errors());
		}

		$tag = Tag::create([
			'title' => $request->input('title'),
			'seo_title' => Str::slug($request->input('title'), '-'),
		]);

		if ($tag) {
			return $this->sendResponse('Tag created succesfully', $tag, 201);
		}

		return $this->sendError('Failed to create tag');
	}

	public function update(Request $request, $id)
	{
		$validator = Validator::make($request->all(), [
			'title' => 'required|unique:tags,title,'.$id,
		]);

		if ($validator->fails()) {
			return $this->sendError('Invalid input', $validator->errors());
		}

		$tag = Tag::where('id', $id)->first();

		if ($tag) {
			$tag->title = $request->input('title');
			$tag->seo_title = Str::slug($request->input('title'), '-');
			$tag->save();

			return $this->sendResponse('Tag updated successfully', $tag);
		}

		return $this->sendError('Tag not found');
	}

	public function destroy($id)
	{
		$tag = Tag::where('id', $id)->first();

		if ($tag) {
			$tag->delete();
			return $this->sendResponse('Tag deleted successfully');
		}

		return $this->sendError('Tag not found');
	}
}
