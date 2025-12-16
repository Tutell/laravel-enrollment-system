@extends('layouts.master')
@section('title','Choose Payment Method')
@section('content')
<div class="container">
  <h1 class="h4 mb-3">Enrollment Payment (Optional)</h1>
  <p class="text-muted">You may pay over the counter at the cashier, or use online payment (GCash/Maya) via PayMongo sandbox.</p>
  <form method="POST" action="{{ route('payments.intent') }}" class="row g-3">
    @csrf
    <div class="col-md-4">
      <label class="form-label">Amount (PHP)</label>
      <input type="number" min="1" class="form-control" name="amount_php" value="500" required>
      <small class="text-muted">Example: 500. This will be converted to cents.</small>
    </div>
    <div class="col-md-4">
      <label class="form-label">Method</label>
      <select class="form-select" name="provider" required>
        <option value="gcash">GCash</option>
        <option value="paymaya">Maya</option>
      </select>
    </div>
    <div class="col-12">
      <button class="btn btn-primary" type="submit" onclick="convertAmount(event)">Proceed</button>
      <a href="#" class="btn btn-outline-secondary" onclick="history.back()">Back</a>
    </div>
  </form>
</div>
@push('scripts')
<script>
function convertAmount(e){
  const form = e.target.closest('form');
  const php = parseFloat(form.querySelector('[name="amount_php"]').value || '0');
  const cents = Math.round(php * 100);
  const hidden = document.createElement('input');
  hidden.type='hidden'; hidden.name='amount'; hidden.value=cents;
  form.appendChild(hidden);
}
</script>
@endpush
@endsection

