<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user extends Model
{
    use HasFactory;

    public $timestamps = false;
    
    protected $fillable = ['name','email','provider', 'provider_id'];

    public function admin(){
        return $this->belongsTo(User::class);
    }
}
