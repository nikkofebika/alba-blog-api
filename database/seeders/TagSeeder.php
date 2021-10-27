<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
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
    			'title' => 'Covid 19',
    			'seo_title' => 'covid-19',
    		],
    		[
    			'title' => 'Vaksinasi',
    			'seo_title' => 'vaksinasi',
    		],
    		[
    			'title' => 'Champions League',
    			'seo_title' => 'champions-league',
    		],
    	];

    	foreach ($data as $d) {
    		Tag::create($d);
    	}
    }
}
