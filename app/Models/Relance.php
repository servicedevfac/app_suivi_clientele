<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Relance extends Model
{
    use SoftDeletes;
    protected $fillable = ['prospect_id', 'commercial_id', 'date_relance', 'heure_relance', 'canal', 'commentaire', 'statut'];
    protected $casts = ['date_relance' => 'date', 'heure_relance' => 'datetime'];
    public function prospect() { return $this->belongsTo(Prospect::class); }
    public function commercial() { return $this->belongsTo(User::class, 'commercial_id'); }
}