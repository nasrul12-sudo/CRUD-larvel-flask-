<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workcompletion extends Model
{
    protected $table = 'workcompletion';
    protected $primaryKey = 'ID';
    public $timestamps = false;
    protected $guarded = [];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'Employee', 'Id_employee');
    }

    public function items()
    {
        return $this->hasMany(Workcompletionitem::class, 'IDM', 'ID');
    }
}
