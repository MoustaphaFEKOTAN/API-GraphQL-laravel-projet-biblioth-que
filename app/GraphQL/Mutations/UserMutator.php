<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;


use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;

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




    public function sendVerificationEmail($_, array $args, Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return ['message' => 'Email déjà vérifié.'];
        }

        $user->sendEmailVerificationNotification();

        return ['message' => 'Lien de vérification envoyé.'];
    }

    public function verifyEmail($_, array $args, Request $request)
    {
        // Ici, en GraphQL, tu devras passer les données de vérification (token, etc.) en args
        // Le EmailVerificationRequest est spécifique à HTTP, il faut simuler

        // Exemple simplifié : on suppose que le token et user sont corrects
        $request->fulfill();

        return ['message' => 'Email vérifié avec succès.'];
    }

    public function forgotPassword($_, array $args)
    {
        $validator = Validator::make($args, [
            'email' => ['required', 'email'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $status = Password::sendResetLink(
            ['email' => $args['email']]
        );

        if ($status === Password::RESET_LINK_SENT) {
            return ['message' => 'Lien de réinitialisation envoyé.'];
        }

        throw new \Exception('Impossible d\'envoyer le lien.');
    }

    public function resetPassword($_, array $args)
    {
        $validator = Validator::make($args, [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $status = Password::reset(
            $args,
            function ($user, $password) use ($args) {
                app(ResetUserPassword::class)->reset($user, $args);
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return ['message' => 'Mot de passe réinitialisé avec succès.'];
        }

        throw new \Exception(__($status));
    }

    public function changePassword($_, array $args, Request $request)
    {
        $user = $request->user();

        try {
            app(UpdateUserPassword::class)->update($user, $args);

            return ['message' => 'Mot de passe mis à jour avec succès.'];
        } catch (ValidationException $e) {
            throw new ValidationException($e->errors());
        }
    }




}
