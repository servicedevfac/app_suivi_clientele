<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produit extends Model
{
    use SoftDeletes;
    protected $fillable = ['filiale_id', 'nom', 'description', 'prix', 'type', 'statut'];
    public function filiale() { return $this->belongsTo(Filiale::class); }
    public function ventes() { return $this->hasMany(Vente::class); }
}