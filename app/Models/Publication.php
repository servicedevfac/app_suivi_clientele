<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Publication extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'campagne_id',
        'titre',
        'canal',
        'url_support',
        'budget',
        'date_publication',
        'statut'
    ];

    protected $casts = [
        'date_publication' => 'date',
        'budget' => 'decimal:2',
    ];

    /**
     * Campagne parente de la publication.
     */
    public function campagne()
    {
        return $this->belongsTo(Campagne::class);
    }

    /**
     * Prospects acquis via cette publication/ce moyen.
     */
    public function prospects()
    {
        return $this->hasMany(Prospect::class);
    }

    /**
     * Nombre de prospects convertis en clients pour cette publication.
     */
    public function getConversionsCountAttribute(): int
    {
        return $this->prospects()->has('client')->count();
    }

    /**
     * Taux de conversion (%) des prospects de cette publication.
     */
    public function getTauxConversionAttribute(): float
    {
        $total = $this->prospects()->count();
        if ($total === 0) {
            return 0.0;
        }
        return round(($this->conversions_count / $total) * 100, 1);
    }

    /**
     * Chiffre d'affaires total généré par les clients acquis via cette publication.
     */
    public function getChiffreAffairesAttribute(): float
    {
        // On somme les montants des ventes des clients issus des prospects de cette publication
        return (float) $this->prospects()->with('client.ventes')->get()->sum(function ($prospect) {
            return $prospect->client ? $prospect->client->ventes->sum('montant') : 0;
        });
    }
}
