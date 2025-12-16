@extends('layouts.master')

@section('title','System Branding')
@section('page-title','System Branding')
@section('page-description','Customize system name, messages, and school identity')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">System Branding</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif
        <form action="{{ route('admin.branding.update') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">System Name</label>
                    <input type="text" name="system_name" class="form-control" value="{{ old('system_name', $branding->system_name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">School Name</label>
                    <input type="text" name="school_name" class="form-control" value="{{ old('school_name', $branding->school_name) }}">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Welcome Message</label>
                    <input type="text" name="welcome_message" class="form-control" value="{{ old('welcome_message', $branding->welcome_message) }}" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Subtext / Description</label>
                    <textarea name="subtext" class="form-control wys">{{ old('subtext', $branding->subtext) }}</textarea>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Mission Statement</label>
                    <textarea name="mission" class="form-control wys">{{ old('mission', $branding->mission) }}</textarea>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Vision Statement</label>
                    <textarea name="vision" class="form-control wys">{{ old('vision', $branding->vision) }}</textarea>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Core Values / Goals</label>
                    <textarea name="core_values" class="form-control wys">{{ old('core_values', $branding->core_values) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">School Logo</label>
                    <input type="file" name="logo" class="form-control">
                    @if($branding->logo_path)
                        <div class="mt-2">
                            <img src="{{ $branding->logo_path }}" alt="Logo" style="height:64px;">
                        </div>
                    @endif
                </div>
            </div>
            <div class="mt-3 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@toast-ui/editor@3.2.2/dist/toastui-editor.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@toast-ui/editor@3.2.2/dist/toastui-editor-all.min.js"></script>
<script>
    (function(){
        const areas = document.querySelectorAll('.wys');
        areas.forEach(function(area){
            const wrapper = document.createElement('div');
            area.parentNode.insertBefore(wrapper, area);
            wrapper.appendChild(area);
            const ed = new toastui.Editor({
                el: wrapper,
                height: '200px',
                initialEditType: 'wysiwyg',
                previewStyle: 'vertical',
                initialValue: area.value || '',
            });
            const hidden = area;
            hidden.style.display = 'none';
            ed.on('change', function(){
                hidden.value = ed.getMarkdown();
            });
        });
    })();
</script>
@endpush

