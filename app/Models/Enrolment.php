<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrolment extends Model
{
    use HasFactory;
    public function en_d()
    {
      return $this->hasMany(EnrolmentD::class);
    }
}
