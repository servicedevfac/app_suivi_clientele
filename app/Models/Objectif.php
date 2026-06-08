<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Objectif extends Model
{
    protected $fillable = ['commercial_id', 'mois', 'montant_cible'];

    public function commercial()
    {
        return $this->belongsTo(User::class, 'commercial_id');
    }
}
