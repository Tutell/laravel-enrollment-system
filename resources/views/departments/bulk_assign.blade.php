@extends('layouts.master')

@section('title','Bulk Assign Teachers')
@section('page-title','Bulk Assign Teachers')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Bulk Assign Teachers to Department</h1>
    <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
  </div>

  <form method="POST" action="{{ route('admin.departments.bulk.post') }}" class="content-wrapper">
    @csrf
    <div class="mb-3">
      <label class="form-label">Department</label>
      <select class="form-select" name="department_id" required>
        <option value="">— Select —</option>
        @foreach($departments as $d)
          <option value="{{ $d->department_ID }}">{{ $d->name }}</option>
        @endforeach
      </select>
      @error('department_id')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>

    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th style="width:36px"><input type="checkbox" id="checkAll"></th>
            <th>Name</th>
            <th>Email</th>
            <th>Current Department</th>
          </tr>
        </thead>
        <tbody>
          @foreach($teachers as $t)
            <tr>
              <td><input type="checkbox" name="teacher_ids[]" value="{{ $t->teacher_ID }}"></td>
              <td>{{ $t->last_name }}, {{ $t->first_name }}</td>
              <td>{{ optional($t->account)->Email ?? '—' }}</td>
              <td>{{ optional($t->department)->name ?? ($t->department ?? '—') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="mt-2">
      {{ $teachers->links() }}
    </div>
    <div class="d-flex gap-2 mt-3">
      <button class="btn btn-primary" type="submit">Assign Selected</button>
      <button class="btn btn-outline-secondary" type="reset">Reset</button>
    </div>
  </form>
</div>
<script>
document.getElementById('checkAll')?.addEventListener('change', function(e) {
  document.querySelectorAll('input[name="teacher_ids[]"]').forEach(cb => cb.checked = e.target.checked);
});
</script>
@endsection
