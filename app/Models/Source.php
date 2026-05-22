<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Source extends Model
{
    use SoftDeletes;
    protected $fillable = ['nom', 'description', 'statut'];
    public function prospects() { return $this->hasMany(Prospect::class); }
}