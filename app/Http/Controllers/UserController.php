<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('roles')->paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = \Spatie\Permission\Models\Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $rawPassword = $request->filled('password') ? $request->password : \Illuminate\Support\Str::password(16);

        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'password' => \Illuminate\Support\Facades\Hash::make($rawPassword),
            'is_active' => $request->has('is_active'),
        ]);

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        // Génération du token de réinitialisation et envoi de l'email pour définir le mot de passe initial
        try {
            $token = \Illuminate\Support\Facades\Password::broker()->createToken($user);
            $resetUrl = route('password.reset', [
                'token' => $token,
                'email' => $user->email,
            ]);

            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\InitialPasswordMail($user, $resetUrl));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Erreur lors de l'envoi de l'email d'initialisation de mot de passe : " . $e->getMessage());
        }

        \App\Models\ActivityLog::log('Création utilisateur', 'Utilisateurs', "Création de l'utilisateur {$user->prenom} {$user->nom} (email: {$user->email}) avec les rôles : " . implode(', ', $request->roles ?? []));

        return redirect()->route('users.index')->with('success', 'Utilisateur créé avec succès et email de configuration du mot de passe envoyé.');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = \Spatie\Permission\Models\Role::all();
        $userRoles = $user->roles->pluck('name')->toArray();
        return view('users.edit', compact('user', 'roles', 'userRoles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $oldRoles = $user->getRoleNames()->toArray();
        $newRoles = $request->roles ?? [];
        sort($oldRoles);
        sort($newRoles);
        $rolesChanged = ($oldRoles !== $newRoles);

        $data = [
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'is_active' => $request->has('is_active'),
        ];

        if ($request->filled('password')) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $user->update($data);

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        } else {
            $user->syncRoles([]);
        }

        if ($rolesChanged) {
            \App\Models\ActivityLog::log('Changement de rôle', 'Utilisateurs', "Rôles de {$user->prenom} {$user->nom} modifiés de [" . implode(', ', $oldRoles) . "] à [" . implode(', ', $newRoles) . "]");
        }

        \App\Models\ActivityLog::log('Modification utilisateur', 'Utilisateurs', "Mise à jour des informations de l'utilisateur {$user->prenom} {$user->nom} (email: {$user->email}).");

        return redirect()->route('users.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }

    public function destroy(User $user)
    {
        \App\Models\ActivityLog::log('Suppression utilisateur', 'Utilisateurs', "Suppression de l'utilisateur {$user->prenom} {$user->nom} (email: {$user->email}).");

        $user->delete();
        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé avec succès.');
    }
}
