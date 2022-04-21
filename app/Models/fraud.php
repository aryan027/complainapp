<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class fraud extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
        'fraud',
        'address',
        'mobile_number',
        'attach_file'
    ];
}
