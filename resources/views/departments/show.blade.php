@extends('layouts.master')

@section('title','Department')
@section('page-title','Department')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h1 class="h3 mb-1">{{ $department->name }}</h1>
      <div class="text-muted small">Teachers</div>
    </div>
    <form method="GET" class="d-flex gap-2">
      <input type="text" name="q" value="{{ $q }}" class="form-control form-control-sm" placeholder="Search teachers">
      <select name="sort" class="form-select form-select-sm">
        <option value="last_name" @selected($sort==='last_name')>Last name</option>
        <option value="first_name" @selected($sort==='first_name')>First name</option>
        <option value="contact_number" @selected($sort==='contact_number')>Contact</option>
      </select>
      <select name="dir" class="form-select form-select-sm">
        <option value="asc" @selected($dir==='asc')>Asc</option>
        <option value="desc" @selected($dir==='desc')>Desc</option>
      </select>
      <button class="btn btn-outline-primary btn-sm">Apply</button>
    </form>
  </div>

  <div class="table-responsive">
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Name</th>
          <th>Contact</th>
          <th>Email</th>
          <th>Teacher ID</th>
        </tr>
      </thead>
      <tbody>
        @forelse($teachers as $t)
          <tr>
            <td>{{ $t->last_name }}, {{ $t->first_name }}</td>
            <td>{{ $t->contact_number ?? '—' }}</td>
            <td>{{ optional($t->account)->Email ?? optional($t->account)->email ?? '—' }}</td>
            <td>{{ $t->teacher_ID }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="text-muted">No teachers assigned to this department.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-2">
    {{ $teachers->links() }}
  </div>
</div>
@endsection
