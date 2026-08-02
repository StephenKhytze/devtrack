<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MaintenanceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('username')->get();
        return view('users.index', compact('users'));
    }

    private function passwordRules(bool $required): array
    {
        $rules = [
            $required ? 'required' : 'nullable',
            'string',
            'min:8',
            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9]).+$/',
        ];

        return $rules;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username'    => 'required|string|max:255|unique:users,username',
            'password'    => $this->passwordRules(required: true),
            'access_type' => ['required', Rule::in(['admin', 'staff'])],
        ], [
            'password.regex' => 'Password must include an uppercase letter, a lowercase letter, and a special character.',
        ]);

        $user = User::create([
            'username'    => $validated['username'],
            'password'    => Hash::make($validated['password']),
            'access_type' => $validated['access_type'],
        ]);

        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'username'    => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'password'    => $this->passwordRules(required: false),
            'access_type' => ['required', Rule::in(['admin', 'staff'])],
        ], [
            'password.regex' => 'Password must include an uppercase letter, a lowercase letter, and a special character.',
        ]);

        if ($user->id === auth()->id() && $validated['access_type'] !== 'admin') {
            return response()->json(['error' => 'You cannot remove your own admin access.'], 422);
        }

        $user->username    = $validated['username'];
        $user->access_type = $validated['access_type'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return response()->json($user);
    }

    public function destroy(Request $request, User $user)
    {
        // Guard: can't delete yourself
        if ($user->id === auth()->id()) {
            return response()->json(['error' => 'You cannot delete your own account.'], 422);
        }

        $logCount = MaintenanceLog::where('performed_by', $user->id)->count();

        if ($logCount > 0 && !$request->boolean('confirmed')) {
            return response()->json([
                'requiresConfirmation' => true,
                'count' => $logCount,
            ]);
        }

        $user->delete();

        return response()->json(['success' => true]);
    }
}
