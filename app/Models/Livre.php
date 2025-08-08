<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Livre extends Model
{
    use HasFactory;

     protected $fillable = [
        'titre', 'description', 'date_sortie',
        
    ];

    public function categorie() {
    return $this->belongsTo(Categorie::class);
}

public function auteur() {
    return $this->belongsTo(User::class);
}

}
