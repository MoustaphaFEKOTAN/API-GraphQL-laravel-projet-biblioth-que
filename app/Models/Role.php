<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class Role extends Model
{
    use HasFactory;

     protected $fillable = [
        'nom',
    ];

       protected static function boot()
    {
        parent::boot();

        static::creating(function ($role) {
            if (empty($role->slug)) {
                $role->slug = (string) Str::uuid();
            }
        });
    }

    public function users()
{
    return $this->hasMany(User::class);
}

}
