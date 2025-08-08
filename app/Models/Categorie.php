<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Categorie extends Model
{
    use HasFactory;

     protected $fillable = [
        'nom',
        'slug',
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

    public function livres() {
    return $this->hasMany(Livre::class);
}

}
