<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;
    protected $fillable = ['prospect_id', 'commercial_id', 'filiale_id', 'nom', 'prenom', 'email', 'telephone', 'adresse', 'ville', 'entreprise', 'statut', 'date_conversion'];
    protected $casts = ['date_conversion' => 'datetime'];
    
    public function prospect() { return $this->belongsTo(Prospect::class); }
    public function commercial() { return $this->belongsTo(User::class, 'commercial_id'); }
    public function filiale() { return $this->belongsTo(Filiale::class); }
    public function ventes() { return $this->hasMany(Vente::class); }

    public function getNomCompletAttribute(): string
    {
        return trim(($this->prenom ?? '') . ' ' . ($this->nom ?? ''));
    }
}