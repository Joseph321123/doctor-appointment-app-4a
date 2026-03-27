<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'user_id',
        'blood_type_id',
        'birth_date',
        'allergies',
        'chronic_conditions',
        'surgical_history',
        'family_history',
        'observations',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    // Relacion uno a uno inversa
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relacion uno a uno inversa
    public function bloodType()
    {
        return $this->belongsTo(BloodType::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }
}
