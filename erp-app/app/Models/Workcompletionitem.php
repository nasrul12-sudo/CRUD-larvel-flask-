<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workcompletionitem extends Model
{
    protected $table = 'workcompletionitem';
    public $timestamps = false;
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class, 'FG', 'Id_product');
    }
}
