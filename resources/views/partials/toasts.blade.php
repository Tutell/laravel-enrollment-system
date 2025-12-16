<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
  @if(session('success'))
    <div class="toast align-items-center text-bg-success border-0 show">
      <div class="d-flex">
        <div class="toast-body">
          {{ session('success') }}
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
  @endif
  @if($errors->any())
    <div class="toast align-items-center text-bg-danger border-0 show mt-2">
      <div class="d-flex">
        <div class="toast-body">
          {{ $errors->first() }}
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
  @endif
</div>

