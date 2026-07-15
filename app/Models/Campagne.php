<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campagne extends Model
{
    use SoftDeletes;
    protected $fillable = ['filiale_id', 'nom', 'description', 'budget', 'date_debut', 'date_fin', 'statut'];
    protected $casts = ['date_debut' => 'date', 'date_fin' => 'date'];
    public function filiale() { return $this->belongsTo(Filiale::class); }
    public function prospects() { return $this->hasMany(Prospect::class); }
    public function publications() { return $this->hasMany(Publication::class); }
}