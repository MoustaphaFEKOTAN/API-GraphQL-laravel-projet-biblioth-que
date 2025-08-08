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

        static::creating(function ($categorie) {
            if (empty($livre->slug)) {
                $categorie->slug = (string) Str::uuid();
            }
        });
    }

    public function livres() {
    return $this->hasMany(Livre::class);
}

}
