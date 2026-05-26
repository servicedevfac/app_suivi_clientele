<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vente extends Model
{
    use SoftDeletes;
    protected $fillable = ['client_id', 'produit_id', 'commercial_id', 'filiale_id', 'montant', 'quantite', 'reduction', 'statut', 'date_vente'];
    protected $casts = ['date_vente' => 'datetime'];

    public function client() { return $this->belongsTo(Client::class); }
    public function produit() { return $this->belongsTo(Produit::class); }
    public function commercial() { return $this->belongsTo(User::class, 'commercial_id'); }
    public function filiale() { return $this->belongsTo(Filiale::class); }
} 