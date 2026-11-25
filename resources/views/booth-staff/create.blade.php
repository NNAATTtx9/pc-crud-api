@extends('layouts.master')
@section('title', 'Add Booth Staff')
@section('content')
    <h1 class="h3 mb-3">Add Booth Staff</h1>
    <form method="POST" action="{{ url('/admin/booth-staff') }}" class="col-md-6">
        @csrf
        <div class="mb-3">
            <label for ='username' class="form-label">Username</label>
            <input type="text" id="username" name="username"
                   value="{{ old('username') }}"
                   class="form-control @error('username') is-invalid @enderror">
            @error('username')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password"
                   class="form-control @error('password') is-invalid @enderror">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="booth_type" class="form-label">Booth type</label>
            <select id="booth_type" name="booth_type"
                    class="form-select @error('booth_type') is-invalid @enderror">
                    <option value="">-- choose --</option>
                    @foreach (['game', 'food', 'merchandise'] as $type)
                        <option value="{{ $type }}"
                            @selected(old('booth_type') === $type)>{{ $type }}</option>
                @endforeach
            </select>
            @error('booth_type')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-check mb-3">
            <input type="checkbox" id="is_active" name="is_active" value="1"
                   class="form-check-input"
                   @checked($errors->any() ? old('is_active') : true)>
            <label for="is_active" class="form-check-label">Active</label>
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="{{ url('/admin/booth-staff') }}" class="btn btn-link">Cancel</a>
    </form>
@endsection
