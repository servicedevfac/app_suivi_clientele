<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProspectHistory extends Model
{
    protected $fillable = ['prospect_id', 'user_id', 'action', 'description', 'ancien_statut', 'nouveau_statut'];
    public function prospect() { return $this->belongsTo(Prospect::class); }
    public function user() { return $this->belongsTo(User::class); }
}