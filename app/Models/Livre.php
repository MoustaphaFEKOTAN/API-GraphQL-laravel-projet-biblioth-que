<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Livre extends Model
{
    use HasFactory;

     protected $fillable = [
        'titre', 'description', 'date_sortie','categorie_id','user_id',
        
    ];


     protected static function boot()
    {
        parent::boot();

        static::creating(function ($livre) {
            if (empty($livre->slug)) {
                $livre->slug = (string) Str::uuid();
            }
        });
    }
    public function categorie() {
    return $this->belongsTo(Categorie::class);
}

public function auteur() {
    return $this->belongsTo(User::class);
}

}
