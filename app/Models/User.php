<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;

class user extends Model implements AuthenticatableContract 
{
    use HasFactory;
    use Authenticatable;

    public $timestamps = false;
    
    protected $fillable = ['name','email','provider', 'provider_id'];

    public function admin(){
        return $this->belongsTo(User::class);
    }
}
