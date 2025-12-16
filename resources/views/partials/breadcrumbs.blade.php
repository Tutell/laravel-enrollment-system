<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
    @foreach(($items ?? []) as $item)
      @if(isset($item['url']))
        <li class="breadcrumb-item"><a href="{{ $item['url'] }}">{{ $item['label'] }}</a></li>
      @else
        <li class="breadcrumb-item active" aria-current="page">{{ $item['label'] }}</li>
      @endif
    @endforeach
  </ol>
</nav>

