<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManipulatedData extends Model
{
    // use HasFactory;
    protected $fillable = [
        'name',
        'email',
        'booking_number',
        'book_date',
        'ahass_code',
        'ahass_name',
        'ahass_address',
        'ahass_contact',
        'ahass_distance',
        'motorcycle_ut_code',
        'motorcycle'
    ];
}
