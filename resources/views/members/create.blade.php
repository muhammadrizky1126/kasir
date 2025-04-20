@extends('layouts.app')

@section('title', 'Add Member')

@section('content')
<div class="main-content-table">
    <section class="section">
        <div class="margin-content">
            <div class="container-sm">
                <div class="section-header">
                    <h1>Add Member</h1>
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
                            <form action="{{ route('members.store') }}" method="POST">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" name="name" id="name" 
                                           placeholder="Enter Name" value="{{ old('name') }}" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="phone_number">Phone Number</label>
                                    <input type="text" class="form-control" name="phone_number" id="phone_number" 
                                           placeholder="Enter Phone Number" value="{{ old('phone_number') }}">
                                </div>

                                <div class="form-group mb-3">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" name="email" id="email" 
                                           placeholder="Enter Email" value="{{ old('email') }}">
                                </div>

                                <div class="form-group mb-3">
                                    <label for="address">Address</label>
                                    <textarea class="form-control" name="address" id="address" rows="3" 
                                              placeholder="Enter Address">{{ old('address') }}</textarea>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="date_of_birth">Date of Birth</label>
                                    <input type="date" class="form-control" name="date_of_birth" id="date_of_birth" 
                                           value="{{ old('date_of_birth') }}">
                                </div>

                                <div class="form-group mb-4">
                                    <label for="points">Points</label>
                                    <input type="number" class="form-control" name="points" id="points" 
                                           placeholder="Enter Points" value="{{ old('points', 0) }}" required>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('members.index') }}" class="btn btn-secondary">
                                        Back
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        Add Member
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
