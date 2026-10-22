<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workallocationitem extends Model
{
    protected $table = 'workallocationitem';
    public $timestamps = false;
    protected $guarded = [];
    // Tidak pakai primaryKey tunggal karena composite. Untuk operasi via Eloquent
    // kita pakai query manual di controller bila perlu.

    public function product()
    {
        return $this->belongsTo(Product::class, 'FG', 'Id_product');
    }
}
