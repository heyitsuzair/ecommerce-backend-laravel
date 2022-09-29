<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Single_Product_Size extends Model
{
    use HasFactory;
    protected $table = 'single_prod_sizes';

    public $timestamps = false;
}