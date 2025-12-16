@extends('layouts.master')

@section('title', 'Add Student')

@section('page-title', 'Add New Student')

@section('content')
<!-- Flash Messages -->
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Validation Error!</strong> Please fix the following issues:
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-body">
        <form action="{{ route('students.store') }}" method="POST">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">First name</label>
                    <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}">
                    @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Last name</label>
                    <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}">
                    @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email address</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Contact number</label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Account</label>
                    <select name="account_id" class="form-select @error('account_id') is-invalid @enderror">
                        <option value="">Select an account</option>
                        @foreach(($availableAccounts ?? []) as $id => $label)
                            <option value="{{ $id }}" {{ old('account_id') == $id ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('account_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Grade Level <span class="text-danger">*</span></label>
                    <select id="gradeLevel" name="grade_level" class="form-select @error('grade_level') is-invalid @enderror" onchange="updateSections()">
                        <option value="">Select grade level first</option>
                        @foreach(($gradeLevels ?? []) as $grade)
                            <option value="{{ $grade }}" {{ old('grade_level') == $grade ? 'selected' : '' }}>Grade {{ $grade }}</option>
                        @endforeach
                    </select>
                    @error('grade_level') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Section <span class="text-danger">*</span></label>
                    <select id="sectionSelect" name="section_id" class="form-select @error('section_id') is-invalid @enderror">
                        <option value="">Select section</option>
                        @foreach(($sections ?? []) as $id => $name)
                            <option value="{{ $id }}" data-grade="{{ substr($name, 6, 1) }}" {{ old('section_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('section_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                        <option value="">Select</option>
                        <option value="Male" {{ old('gender')==='Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender')==='Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender')==='Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Birthdate</label>
                    <input type="date" name="birthdate" class="form-control @error('birthdate') is-invalid @enderror" value="{{ old('birthdate') }}">
                    @error('birthdate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-8">
                    <label class="form-label">Home address</label>
                    <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address') }}">
                    @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">LRN</label>
                    <input type="text"
                           name="lrn"
                           class="form-control @error('lrn') is-invalid @enderror"
                           value="{{ old('lrn') }}"
                           maxlength="12">
                    @error('lrn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">Create student</button>
                    <a href="{{ route('students.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
 

// Function to update sections based on selected grade level
function updateSections() {
    const gradeLevel = document.getElementById('gradeLevel').value;
    const sectionSelect = document.getElementById('sectionSelect');
    
    if (!gradeLevel) {
        // When no grade is selected, do not show any specific sections
        sectionSelect.innerHTML = '<option value="">Select section (optional)</option>';
        return;
    }

    // Fetch sections for the selected grade level
    fetch('/students/grade/' + gradeLevel + '/sections')
        .then(response => response.json())
        .then(data => {
            sectionSelect.innerHTML = '<option value="">Select section (optional)</option>';
            Object.entries(data).forEach(([id, name]) => {
                const option = document.createElement('option');
                option.value = id;
                option.textContent = name;
                sectionSelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error fetching sections:', error));
}

// Initialize sections filter on page load if grade level was previously selected
window.addEventListener('DOMContentLoaded', function() {
    const gradeLevel = document.getElementById('gradeLevel').value;
    if (gradeLevel) {
        updateSections();
    }
});
</script>
@endsection
