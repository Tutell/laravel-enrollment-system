<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountAudit;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function create()
    {
        return view('admin.accounts.create');
    }

    public function index()
    {
        $accounts = Account::with('roles')->paginate(20);
        $roles = Role::pluck('role_name', 'role_ID')->toArray();

        return view('admin.accounts.index', compact('accounts', 'roles'));
    }

    public function edit(Account $account)
    {
        $roles = Role::pluck('role_name', 'role_ID')->toArray();

        return view('admin.accounts.edit', compact('account', 'roles'));
    }

    public function update(Request $request, Account $account)
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'min:3', 'max:255', 'unique:accounts,Username,'.$account->account_ID.',account_ID'],
            'email' => ['nullable', 'email', 'unique:accounts,Email,'.$account->account_ID.',account_ID'],
            'role' => ['required', 'string', 'in:student,teacher,admin,parent,faculty'],
            'status' => ['required', 'string', 'in:active,disabled'],
            'password' => ['nullable', 'string', 'min:6'],
            'roles' => ['array'],
        ]);

        $changes = [];

        $updates = [
            'Username' => $data['username'],
            'Email' => $data['email'] ?? null,
            'role' => $data['role'],
            'status' => $data['status'],
        ];

        foreach ($updates as $key => $value) {
            if ($account->{$key} != $value) {
                $changes[$key] = ['old' => $account->{$key}, 'new' => $value];
            }
        }

        if (! empty($data['password'])) {
            $updates['Password_Hash'] = Hash::make($data['password']);
            $changes['Password_Hash'] = ['old' => null, 'new' => 'updated'];
        }

        $account->update($updates);

        if (isset($data['roles'])) {
            $account->roles()->sync($data['roles']);
        }

        if (! empty($changes)) {
            AccountAudit::create([
                'actor_account_ID' => (Auth::user() ? Auth::user()->getAuthIdentifier() : null),
                'target_account_ID' => $account->account_ID,
                'action' => 'updated',
                'changes' => json_encode($changes),
            ]);
        }

        return redirect()->route('admin.accounts.index')->with('success', 'Account updated');
    }

    public function destroy(Account $account)
    {
        AccountAudit::create([
            'actor_account_ID' => (Auth::user() ? Auth::user()->getAuthIdentifier() : null),
            'target_account_ID' => $account->account_ID,
            'action' => 'deleted',
            'changes' => null,
        ]);

        $account->delete();

        return redirect()->route('admin.accounts.index')->with('success', 'Account deleted');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'min:3', 'max:255', 'unique:accounts,Username'],
            'email' => ['nullable', 'email', 'unique:accounts,Email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'string', 'in:student,teacher,admin,parent,faculty'],
            'status' => ['required', 'string', 'in:active,disabled,pending'],
        ]);

        $account = Account::create([
            'Username' => $data['username'],
            'Email' => $data['email'] ?? null,
            'Password_Hash' => Hash::make($data['password']),
            'role' => $data['role'] === 'faculty' ? 'teacher' : ($data['role'] === 'parent' ? 'student' : $data['role']),
            'status' => $data['status'],
        ]);

        $role = Role::firstOrCreate(['role_name' => $data['role']]);
        if (method_exists($account, 'roles')) {
            $account->roles()->syncWithoutDetaching([$role->getKey()]);
        }

        AccountAudit::create([
            'actor_account_ID' => (Auth::user() ? Auth::user()->getAuthIdentifier() : null),
            'target_account_ID' => $account->account_ID,
            'action' => 'created',
            'changes' => json_encode($data),
        ]);

        return redirect()->route('admin.accounts.index')->with('success', 'Account created');
    }
}
