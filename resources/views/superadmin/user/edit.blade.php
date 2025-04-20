@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="main-content-table">
    <section class="section">
        <div class="margin-content">
            <div class="container-sm">
                <div class="section-header">
                    <h1>Edit User</h1>
                </div>

                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="section-body">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <form action="{{ route('user.update', $user->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="form-group mb-3">
                                    <label for="name">Nama</label>
                                    <input type="text" class="form-control" name="name" id="name" placeholder="Nama Lengkap" value="{{ old('name', $user->name) }}" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="password">Password <small>(Biarkan kosong jika tidak ingin mengubah)</small></label>
                                    <input type="password" class="form-control" name="password" id="password" placeholder="Password Baru">
                                </div>

                                <div class="form-group mb-4">
                                    <label for="role">Role</label>
                                    <select name="role" id="role" class="form-control" required>
                                        <option value="superadmin" {{ old('role', $user->role) === 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                                        <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>User</option>
                                    </select>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('user.index') }}" class="btn btn-secondary">
                                        Back
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        Update User
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
