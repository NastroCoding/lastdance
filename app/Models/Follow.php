<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    use HasFactory;

    protected $table = 'follow';
    protected $guarded = [];

    public function users () {
        return $this->hasMany(User::class);
    }
}
