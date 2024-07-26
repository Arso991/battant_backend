<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'image', 'price', 'country', 'address', 'startDate', 'endDate'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
