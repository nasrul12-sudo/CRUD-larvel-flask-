<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'product';
    protected $primaryKey = 'Id_product';
    public $timestamps = false;
    protected $guarded = [];
}
