<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Filiale extends Model
{
    use SoftDeletes;

    protected $fillable = ['nom', 'adresse', 'telephone', 'email', 'ville', 'pays', 'statut'];

    public function campagnes() { return $this->hasMany(Campagne::class); }
    public function produits() { return $this->hasMany(Produit::class); }
    public function prospects() { return $this->hasMany(Prospect::class); }
    public function clients() { return $this->hasMany(Client::class); }
    public function ventes() { return $this->hasMany(Vente::class); }
}