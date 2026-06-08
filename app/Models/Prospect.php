<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prospect extends Model
{
    use SoftDeletes;
    protected $fillable = ['commercial_id', 'source_id', 'campagne_id', 'filiale_id', 'nom', 'prenom', 'email', 'telephone', 'entreprise', 'profession', 'adresse', 'ville', 'statut', 'besoin', 'montant_estime', 'probabilite', 'commentaire', 'date_contact', 'prochain_rappel', 'tags', 'score'];
    protected $casts = ['date_contact' => 'datetime', 'prochain_rappel' => 'datetime', 'tags' => 'array'];

    public function commercial() { return $this->belongsTo(User::class, 'commercial_id'); }
    public function source() { return $this->belongsTo(Source::class); }
    public function campagne() { return $this->belongsTo(Campagne::class); }
    public function filiale() { return $this->belongsTo(Filiale::class); }
    
    public function histories() { return $this->hasMany(ProspectHistory::class); }
    public function relances() { return $this->hasMany(Relance::class); }
    public function client() { return $this->hasOne(Client::class); }
    public function tasks() { return $this->hasMany(Task::class); }
    public function documents() { return $this->hasMany(ProspectDocument::class); }

    public function getNomCompletAttribute(): string
    {
        return trim(($this->prenom ?? '') . ' ' . ($this->nom ?? ''));
    }

    public function calculateScore(): int
    {
        $score = 0;
        // Points basés sur le statut
        switch ($this->statut) {
            case 'Nouveau': $score += 5; break;
            case 'Contacté': $score += 10; break;
            case 'Qualifié': $score += 20; break;
            case 'En négociation': $score += 40; break;
            case 'Gagné': $score += 100; break;
            case 'Perdu': $score = 0; return $score;
        }
        // Points basés sur les informations de contact (Profil complété)
        if (!empty($this->email)) $score += 5;
        if (!empty($this->telephone)) $score += 5;
        if (!empty($this->entreprise)) $score += 5;

        // Points basés sur le montant estimé
        if ($this->montant_estime > 10000) {
            $score += 20;
        } elseif ($this->montant_estime > 5000) {
            $score += 10;
        } elseif ($this->montant_estime > 1000) {
            $score += 5;
        }

        // Cap le score à 100 max (hors gagné)
        return min($score, 100);
    }
}