<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PostTag;

class PostTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	for ($post_id=1; $post_id < 4; $post_id++) { 
    		for ($tag_id=1; $tag_id < 4; $tag_id++) { 
    			PostTag::create([
    				'post_id' => $post_id,
    				'tag_id' => $tag_id,
    			]);
    		}
    	}
    }
}
