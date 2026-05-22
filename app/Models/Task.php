<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;
    protected $fillable = ['user_id', 'prospect_id', 'titre', 'description', 'priorite', 'date_limite', 'statut'];
    protected $casts = ['date_limite' => 'datetime'];
    public function user() { return $this->belongsTo(User::class); }
    public function prospect() { return $this->belongsTo(Prospect::class); }
}