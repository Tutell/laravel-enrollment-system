@extends('layouts.master')
@section('title','Payment Pending')
@section('content')
<div class="container">
  <div class="card shadow-sm">
    <div class="card-body">
      <h1 class="h5">Payment Initialized</h1>
      <p class="text-muted">Your payment intent was created. Please complete payment in the PayMongo sandbox flow. If you do not proceed, you can still pay over-the-counter.</p>
      <div class="mb-2"><strong>Reference:</strong> {{ $payment->reference }}</div>
      <a href="{{ route('dashboard.index') }}" class="btn btn-primary">Return to Dashboard</a>
    </div>
  </div>
</div>
@endsection

