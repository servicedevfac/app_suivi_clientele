<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prospect extends Model
{
    use SoftDeletes;
    protected $fillable = ['commercial_id', 'source_id', 'campagne_id', 'filiale_id', 'nom', 'prenom', 'email', 'telephone', 'entreprise', 'profession', 'adresse', 'ville', 'statut', 'besoin', 'commentaire', 'date_contact', 'prochain_rappel'];
    protected $casts = ['date_contact' => 'datetime', 'prochain_rappel' => 'datetime'];

    public function commercial() { return $this->belongsTo(User::class, 'commercial_id'); }
    public function source() { return $this->belongsTo(Source::class); }
    public function campagne() { return $this->belongsTo(Campagne::class); }
    public function filiale() { return $this->belongsTo(Filiale::class); }
    
    public function histories() { return $this->hasMany(ProspectHistory::class); }
    public function relances() { return $this->hasMany(Relance::class); }
    public function client() { return $this->hasOne(Client::class); }
    public function tasks() { return $this->hasMany(Task::class); }
}