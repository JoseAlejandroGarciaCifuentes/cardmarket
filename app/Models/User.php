<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user extends Model
{
    use HasFactory;

    public function card(){
        return $this->hasMany(Card::class, "sellings")->withPivot('quantity','total_price');
    }
}
