<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BloodType extends Model
{
    //
    //Relacion unoa muchos con Patient
    public function patients()
    {
        return $this->hasMany(Patient::class, 'blood_type_id', 'id');
    }
}
