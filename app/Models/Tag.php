<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['title', 'seo_title'];

    public function posts() {
        return $this->hasMany(PostTag::class);
    }
}
