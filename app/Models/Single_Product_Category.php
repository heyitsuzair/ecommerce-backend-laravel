<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Single_Product_Category extends Model
{
    use HasFactory;

    protected $table = 'single_prod_categories';

    public $timestamps = false;

    protected $with = 'populate';

    function populate()
    {
        return   $this->belongsTo(Category::class, 'category', 'id');
    }
}