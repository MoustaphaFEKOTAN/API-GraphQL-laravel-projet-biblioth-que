<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;


use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

class UserMutator
{
    protected $creator;

    public function __construct(CreateNewUser $creator)
    {
        $this->creator = $creator;
    }

    public function register($_, array $args)
    {

        //Appel de fortify
        $user = $this->creator->create($args);


        return $user;
    }



public function login($_, array $args)
{
    // Validation manuelle
    $validator = Validator::make($args, [
        'email' => 'required|email',
        'password' => 'required|string',
        'remember_me' => 'boolean',
    ]);

    if ($validator->fails()) {
        throw new ValidationException($validator);
    }

    $email = $args['email'];
    $ip = request()->ip(); // On récupère l’IP via request()
    $key = 'login:' . $email . '|' . $ip;

    // Vérifie les tentatives
    if (RateLimiter::tooManyAttempts($key, 5)) {
        $seconds = RateLimiter::availableIn($key);
        throw ValidationException::withMessages([
            'email' => ['Trop de tentatives. Réessayez dans ' . $seconds . ' secondes.'],
        ]);
    }

    $user = User::where('email', $email)->first();

    // Vérifie identifiants
    if (! $user || ! Hash::check($args['password'], $user->password)) {
        RateLimiter::hit($key, 60);
        throw ValidationException::withMessages([
            'email' => ['Identifiants invalides.'],
        ]);
    }

    RateLimiter::clear($key);

    // Création du token
    $token = $user->createToken(
        'auth_token',
        [],
        now()->addDays(!empty($args['remember_me']) ? 30 : 1)
    )->plainTextToken;

    $login = [ 
        'access_token' => $token,
        'user' => $user,];

    // Retourne exactement ce que le type GraphQL attend
    return $login ;
}

}
