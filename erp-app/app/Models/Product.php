<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'product';
    protected $primaryKey = 'Id_product';
    public $timestamps = false;
    protected $guarded = [];

    public function getCategoryNameAttribute(): string
    {
        return config('categories.' . $this->sub_category, $this->sub_category);
    }

    public function workallocationitems()
    {
        return $this->hasMany(Workallocationitem::class, 'FG', 'Id_product');
    }

    public function workcompletionitems()
    {
        return $this->hasMany(Workcompletionitem::class, 'FG', 'Id_product');
    }
}