<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Single_Product_Color extends Model
{
    use HasFactory;

    protected $table = 'single_prod_colors';

    public $timestamps = false;
}