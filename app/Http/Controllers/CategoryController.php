<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index($id = null)
    {
        $data = [];
        if ($id != null) {
            $category = Category::where('id', $id)->first();
            if (!$category) {
                return $this->sendError('Category not found');
            }
            $data = $category->toArray();
            $data['total_posts'] = count($category->posts);
        } else {
            $categories = Category::all();
            if (count($categories) > 0) {
                foreach ($categories as $category) {
                    $data[$category->id] = $category->toArray();
                    $data[$category->id]['total_posts'] = count($category->posts);
                }
            }
        }

        if (count($data) > 0) {
            return $this->sendResponse('Category found successfully', $data);
        }

        return $this->sendError('Category not found');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|unique:categories',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Invalid input', $validator->errors());
        }

        $category = Category::create([
            'title' => $request->input('title'),
            'seo_title' => Str::slug($request->input('title'), '-'),
        ]);

        if ($category) {
            return $this->sendResponse('Category created succesfully', $category, 201);
        }

        return $this->sendError('Failed to create category');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|unique:categories,title,'.$id,
        ]);

        if ($validator->fails()) {
            return $this->sendError('Invalid input', $validator->errors());
        }

        $category = Category::where('id', $id)->first();

        if ($category) {
            $category->title = $request->input('title');
            $category->seo_title = Str::slug($request->input('title'), '-');
            $category->save();

            return $this->sendResponse('Category updated successfully', $category);
        }

        return $this->sendError('Category not found');
    }

    public function destroy($id)
    {
        $category = Category::where('id', $id)->first();

        if ($category) {
            $category->delete();
            return $this->sendResponse('Category deleted successfully');
        }

        return $this->sendError('Category not found');
    }
}
