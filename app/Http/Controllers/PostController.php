<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostTag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
	private function publicPath($path) {
		$path = str_replace('/', DIRECTORY_SEPARATOR, $path);
		return app()->basePath().DIRECTORY_SEPARATOR.'public'.$path;
	}
	public function index($id = null)
	{
		$data = [];
		if ($id != null) {
			$post = Post::where('id', $id)->first();
			if (!$post) {
				return $this->sendError('Post not found');
			}
			$data = $post->toArray();
			$data['image'] = url($post->image);
			$data['created_by'] = $post->user->name;
			$data['category'] = $post->category->title;

			if(count($post->tags) > 0){
				foreach ($post->tags as $tag) {
					$data['tags'][] = $tag->title;
				}
			}
		} else {
			$posts = Post::all();
			if (count($posts) > 0) {
				foreach ($posts as $post) {
					$data[$post->id] = $post->toArray();
					$data[$post->id]['image'] = url($post->image);
					$data[$post->id]['created_by'] = $post->user->name;
					$data[$post->id]['category'] = $post->category->title;

					if(count($post->tags) > 0){
						foreach ($post->tags as $tag) {
							$data[$post->id]['tags'][] = $tag->title;
						}
					}
				}
			}
		}

		if (count($data) > 0) {
			return $this->sendResponse('Post found successfully', $data, 201);
		}

		return $this->sendError('Post not found');
	}

	public function store(Request $request, $id = null)
	{
		$arrValidator = [];
		if ($id != null) {
			$post = Post::where('id', $id)->first();
			if (!$post) {
				return $this->sendError('Post not found');
			}
			$arrValidator = [
				'category_id' => 'required|numeric',
				'title' => 'required|unique:posts,title,'.$id,
				'description' => 'required',
				'image' => 'image|mimes:jpeg,png,jpg,svg|max:512',
			];
			$msg = 'Post updated succesfully';
			$errorMsg = 'Failed to create post';
			$code = 200;
		} else {
			$post = new Post;
			$arrValidator = [
				'category_id' => 'required|numeric',
				'title' => 'required|unique:posts',
				'description' => 'required',
				'image' => 'required|image|mimes:jpeg,png,jpg,svg|max:512',
			];
			$msg = 'Post created succesfully';
			$errorMsg = 'Failed to update post';
			$code = 201;
		}

		$validator = Validator::make($request->all(), $arrValidator);

		if ($validator->fails()) {
			return $this->sendError('Invalid input', $validator->errors());
		}

		$seo_title = Str::slug($request->input('title'), '-');
		$dir = '/images/';
		if (!file_exists($this->publicPath($dir))) {
			mkdir($this->publicPath($dir), 0777, true);
			chmod($this->publicPath($dir), 0777);
		}

		if ($request->has('image')) {
			if ($request->image->isValid()) {
				$imageName = $seo_title.'-'.time().'.'.$request->image->extension();
				$request->image->move($this->publicPath($dir), $imageName);
			}
		} else {
			$imgExtension = pathinfo($this->publicPath($post->image), PATHINFO_EXTENSION);
			$imageName = $seo_title.'-'.time().'.'.$imgExtension;
			rename($this->publicPath($post->image), $this->publicPath($dir.$imageName));
		}

		$post->user_id = $request->user()->id;
		$post->category_id = $request->input('category_id');
		$post->title = $request->input('title');
		$post->seo_title = $seo_title;
		$post->description = $request->input('description');
		$post->image = $dir.$imageName;

		if ($post->save()) {
			if ($request->has('tags') && count($request->input('tags')) > 0) {
				PostTag::where('post_id', $post->id)->delete();
				foreach ($request->input('tags') as $tag) {
					PostTag::create([
						'post_id' => $post->id,
						'tag_id' => $tag,
					]);
				}
			}
			$post['image'] = url($post->image);
			return $this->sendResponse($msg, $post, $code);
		}

		return $this->sendError($errorMsg);
	}

	// public function update(Request $request, $id)
	// {
	// 	dd($request->all());
	// 	$validator = Validator::make($request->all(), [
	// 		'category_id' => 'required|numeric',
	// 		'title' => 'required|unique:posts,title,'.$id,
	// 		'description' => 'required',
	// 		'image' => 'image|mimes:jpeg,png,jpg,svg|max:512',
	// 	]);

	// 	if ($validator->fails()) {
	// 		return $this->sendError('Invalid input', $validator->errors());
	// 	}

	// 	$post = Post::where('id', $id)->first();

	// 	if ($post) {
	// 		$seo_title = Str::slug($request->input('title'), '-');
	// 		if ($request->has('image')) {
	// 			if ($request->image->isValid()) {
	// 				$imageName = $seo_title.'-'.time().'.'.$request->image->extension();
	// 				$dir = '/images/';
	// 				if (!file_exists($this->publicPath($dir))) {
	// 					mkdir($this->publicPath($dir), 0777, true);
	// 					chmod($this->publicPath($dir), 0777);
	// 				}
	// 				$request->image->move($this->publicPath($dir), $imageName);
	// 			}
	// 		}

	// 		$post->user_id = $request->user()->id;
	// 		$post->category_id = $request->input('category_id');
	// 		$post->title = $request->input('title');
	// 		$post->seo_title = $seo_title;
	// 		$post->description = $request->input('description');
	// 		if ($request->has('image')) {
	// 			$post->image = $imageName;
	// 		}

	// 		if ($post->save()) {
	// 			return $this->sendResponse('Post updated succesfully', $post);
	// 		}
	// 	}

	// 	return $this->sendError('Failed to update post');
	// }

	public function destroy($id)
	{
		$post = Post::where('id', $id)->first();
		if (pathinfo($post->image, PATHINFO_FILENAME) !== 'sample') {
			unlink($this->publicPath($post->image));
		}

		if ($post) {
			$post->delete();
			return $this->sendResponse('Post deleted successfully');
		}

		return $this->sendError('Post not found');
	}
}
