@extends('layouts.master')

@section('title','Manage Accounts')
@section('page-title','Account Management')
@section('page-header','Account Management')
@section('page-description','Create, edit, and manage user accounts')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Manage Accounts</li>
@endsection

@section('page-actions')
    @if(auth()->check() && auth()->user()->role === 'admin')
    <a href="{{ route('admin.logs.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-card-list me-1"></i> View Logs
    </a>
    @endif
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h6 mb-0 fw-semibold text-primary">Accounts</h2>
            <a href="{{ route('admin.accounts.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> New Account
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($accounts as $account)
                    <tr>
                        <td>{{ $account->account_ID }}</td>
                        <td>{{ $account->Username }}</td>
                        <td>{{ $account->Email ?? '—' }}</td>
                        <td>{{ $account->role }}</td>
                        <td>
                            @if($account->status === 'active')
                                <span class="badge bg-success">Active</span>
                            @elseif($account->status === 'disabled')
                                <span class="badge bg-danger">Disabled</span>
                            @else
                                <span class="badge bg-warning text-dark">Pending</span>
                            @endif
                        </td>
                        <td class="text-nowrap">
                            <a href="{{ route('admin.accounts.edit', $account) }}" class="btn btn-sm btn-outline-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.accounts.destroy', $account) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this account?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-muted">No accounts found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $accounts->links() }}
        </div>
    </div>
@endsection
