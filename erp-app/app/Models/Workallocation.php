<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workallocation extends Model
{
    protected $table = 'workallocation';
    protected $primaryKey = 'ID';
    public $timestamps = false;
    protected $guarded = [];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'Employee', 'Id_employee');
    }

    public function items()
    {
        return $this->hasMany(Workallocationitem::class, 'IDM', 'ID');
    }
}
