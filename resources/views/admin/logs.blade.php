@extends('layouts.master')

@section('title','Account Management – Logs')
@section('page-title','Account Management')
@section('page-description','Teacher login/logout activity')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.accounts.index') }}">Manage Accounts</a></li>
    <li class="breadcrumb-item active">Logs</li>
@endsection

@section('page-actions')
    <a href="{{ route('admin.logs.index', ['sort' => $sort === 'asc' ? 'desc' : 'asc']) }}" class="btn btn-outline-secondary">
        <i class="bi bi-sort-down me-1"></i> Sort {{ $sort === 'asc' ? 'Newest First' : 'Oldest First' }}
    </a>
@endsection

@section('content')
<div class="card border-0 shadow">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h6 mb-0 fw-semibold text-primary">Teacher Access Logs</h2>
            <span class="text-muted small">Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>Teacher</th>
                        <th>Action</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td>{{ optional($log->created_at)->format('Y-m-d H:i:s') }}</td>
                        <td>
                            @if($log->teacher)
                                {{ $log->teacher->last_name }}, {{ $log->teacher->first_name }} (ID: {{ $log->teacher->teacher_ID }})
                            @else
                                {{ optional($log->account)->Username }} (Account ID: {{ $log->account_ID }})
                            @endif
                        </td>
                        <td>{{ $log->action }}</td>
                        <td>{{ $log->ip_address ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-muted">No logs available.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $logs->links() }}
        </div>
    </div>
@endsection

