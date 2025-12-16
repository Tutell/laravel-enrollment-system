@extends('layouts.master')

@section('title','Settings')
@section('page-title','Account Settings')

@section('content')
<div class="card">
    <div class="card-body">
        <p class="text-muted mb-3">These are read-only placeholders. Admin can update accounts in Manage Accounts.</p>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Notification Email</label>
                <input type="email" class="form-control" value="{{ $user->email }}" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label">Preferred Role</label>
                <input type="text" class="form-control" value="{{ ucfirst($user->role ?? 'user') }}" disabled>
            </div>
        </div>
    </div>
</div>
@endsection

