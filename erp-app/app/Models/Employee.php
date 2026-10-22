<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employee';
    protected $primaryKey = 'Id_employee';
    public $timestamps = false;
    protected $guarded = [];

    public function workallocations()
    {
        return $this->hasMany(Workallocation::class, 'Employee', 'Id_employee');
    }
}
