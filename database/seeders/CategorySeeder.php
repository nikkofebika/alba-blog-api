<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$data = [
    		[
    			'title' => 'Nasional',
    			'seo_title' => 'nasional',
    		],
    		[
    			'title' => 'Ekonomi',
    			'seo_title' => 'ekonomi',
    		],
    		[
    			'title' => 'Olahraga',
    			'seo_title' => 'olahraga',
    		],
    	];

    	foreach ($data as $d) {
    		Category::create($d);
    	}
    }
}
