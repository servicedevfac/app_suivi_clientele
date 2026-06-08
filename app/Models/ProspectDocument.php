<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProspectDocument extends Model
{
    protected $fillable = ['prospect_id', 'nom_fichier', 'chemin_fichier', 'type_mime', 'taille'];

    public function prospect()
    {
        return $this->belongsTo(Prospect::class);
    }
}
