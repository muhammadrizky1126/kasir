@extends('layouts.app')

@section('title', 'User')

@push('style')

@endpush

@section('content')
<div class="main-content-table">
    <section class="section">
        <div class="margin-content">
            <div class="container-sm">
                <div class="section-header">
                    <h1>User</h1>
                </div>
                <div class="section-body">
                    <div class="table-responsive">
                        <div class="row mb-3">
                            <div class="col-md-12 d-flex justify-content-between align-items-center">
                                <form action="{{ route('user.index') }}" method="GET" class="d-flex"
                                style="max-width: 100%%;">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control rounded"
                                    placeholder="Search">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary rounded ml-2" type="submit">Search</button>
                                    </div>
                                </div>
                            </form>
                            @if(Auth::user()->role == 'superadmin')
                            <a href="{{ route('user.create') }}" class="btn btn-success ml-2 p-2">
                                Create User
                            </a>
                            @endif
                        </div>
                        <table class="table table-bordered my-3" style="background-color: #f3f3f3">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $index => $item)
                                <tr>
                                    <td>{{ $users->firstItem() + $index }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->email }}</td>
                                    <td>{{ $item->role }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('user.edit', $item->id) }}" class="btn btn-primary">Edit</a>
                                        <form action="{{ route('user.destroy', $item->id) }}" method="POST" class="delete-form" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>                                
                        </table>
                        <div class="d-flex justify-content-center mt-3">
                            {{ $users->links() }}
                        </div>                            
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('form[action*="user"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "User will be permanently deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });

    @if(session('message'))
        Swal.fire({
            title: 'Success!',
            text: '{{ session('message') }}',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false,
        });
    @endif

    @if(session('error'))
        Swal.fire({
            title: 'Error!',
            text: '{{ session('error') }}',
            icon: 'error',
            timer: 3000,
            showConfirmButton: false,
        });
    @endif
</script>
@endpush
