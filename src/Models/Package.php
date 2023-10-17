<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    public static $rules = [
        'vendor' => 'required|unique:packages,vendor,NULL,id,name,:name',
        'name' => 'required|unique:packages,name,NULL,id,vendor,:vendor',
    ];
}
