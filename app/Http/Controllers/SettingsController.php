<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', User::class);
        $company = app('currentCompany');
        $users = User::orderBy('name')->get();
        return view('settings.index', compact('company', 'users'));
    }

    public function updateCompany(Request $request)
    {
        $this->authorize('update', app('currentCompany'));

        $company = app('currentCompany');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'vat_number' => 'nullable|string|max:20',
            'fiscal_code' => 'nullable|string|max:20',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($company->logo) Storage::disk('public')->delete($company->logo);
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $company->update($validated);
        return back()->with('success', 'Dati azienda aggiornati.');
    }

    public function updateAlertSettings(Request $request)
    {
        $this->authorize('update', app('currentCompany'));
        $company = app('currentCompany');

        $validated = $request->validate([
            'alert_days_before_expiry' => 'required|integer|min:1|max:365',
            'alert_emails' => 'nullable|string',
        ]);

        $emails = array_filter(array_map('trim', explode(',', $validated['alert_emails'] ?? '')));
        $settings = $company->settings ?? [];
        $settings['alert_days_before_expiry'] = (int) $validated['alert_days_before_expiry'];
        $settings['alert_emails'] = array_values($emails);
        $company->update(['settings' => $settings]);

        return back()->with('success', 'Impostazioni alert salvate.');
    }

    public function createUser()
    {
        $this->authorize('create', User::class);
        return view('settings.users.create');
    }

    public function storeUser(Request $request)
    {
        $this->authorize('create', User::class);
        $company = app('currentCompany');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,manager,warehouse,readonly',
        ], [
            'name.required' => 'Il nome è obbligatorio.',
            'email.required' => 'L\'email è obbligatoria.',
            'email.unique' => 'Questa email è già in uso.',
            'password.min' => 'La password deve avere almeno 8 caratteri.',
            'password.confirmed' => 'Le password non coincidono.',
        ]);

        User::create([
            'company_id' => $company->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => true,
        ]);

        return redirect()->route('settings.index')->with('success', 'Utente creato con successo.');
    }

    public function editUser(User $user)
    {
        $this->authorize('update', $user);
        return view('settings.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|in:admin,manager,warehouse,readonly',
            'is_active' => 'boolean',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'name' => $validated['name'],
            'role' => $validated['role'],
            'is_active' => $request->boolean('is_active'),
        ];

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);
        return redirect()->route('settings.index')->with('success', 'Utente aggiornato.');
    }

    public function destroyUser(User $user)
    {
        $this->authorize('delete', $user);
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Non puoi eliminare il tuo stesso account.');
        }
        $user->delete();
        return redirect()->route('settings.index')->with('success', 'Utente eliminato.');
    }
}
