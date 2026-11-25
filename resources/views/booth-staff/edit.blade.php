@extends('layouts.master')
@section('title', 'Edit Booth Staff')
@section('content')
    <h1 class="h3 mb-3">Edit {{ $item->username }}</h1>
    <form method="POST" action="{{ url('/admin/booth-staff/' . $item->id) }}"
          class="col-md-6">
          @csrf
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" value="{{ $item->username }}"
                   class="form-control" disabled>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">New passwords</label>
            <input type="password" id="password" name="password"
                   class="form-control @error('password') is-invalid @enderror">
            <div class="form-text">Leave blank to keep the current password.</div>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="booth_type" class="form-label">Booth type</label>
            <select id="booth_type" name="booth_type"
                    class="form-select @error('booth_type') is-invalid @enderror">
                @foreach (['game', 'food', 'merchandise'] as $type)
                    <option value="{{ $type }}"
                        @selected(old('booth_type', $item->booth_type) === $type)>{{ $type }}
                    </option>
                @endforeach
            </select>
            @error('booth_type')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-check mb-3">
            <input type="checkbox" id="is_active" name="is_active" value="1"
                   class="form-check-input"
                   @checked($errors->any() ? old('is_active') : $item->is_active)>
            <label for="is_active" class="form-check-label">Active</label>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ url('/admin/booth-staff') }}" class="btn btn-link">Cancel</a>
    </form>
@endsection
