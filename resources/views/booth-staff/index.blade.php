@extends('layouts.master')
@section('title', 'Booth Staff')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Booth Staff</h1>
        <a href="{{ url('/admin/booth-staff/create') }}"
           class="btn btn-primary"> Add booth staff</a>
    </div>
    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Booth type</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->username }}</td>
                    <td>{{ $item->booth_type }}</td>
                    <td>
                        @if ($item->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ url('/admin/booth-staff/' . $item->id . '/edit') }}"
                           class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form action="{{ url('/admin/booth-staff/' . $item->id) }}"
                              method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"
                                    onClick="return confirm('Delete this booth staff?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5"
                        class="text-center text-muted">No booth staff yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
