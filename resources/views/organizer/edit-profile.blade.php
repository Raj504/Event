@extends('organizer.layout')

@section('content')
    <div class="page-header">
        <h4 class="page-title">{{ __('Edit Profile') }}</h4>
        <ul class="breadcrumbs">
            <li class="nav-home">
                <a href="{{ route('organizer.dashboard') }}">
                    <i class="flaticon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ __('Edit Profile') }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card-title">{{ __('Edit Profile') }}</div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row ">
                        <div class="col-lg-8 mx-auto">
                            <div class="alert alert-danger pb-1 dis-none" id="eventErrors">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <ul></ul>
                            </div>
@php
    $organizer = Auth::guard('organizer')->user();
    $organizer_info = \App\Models\OrganizerInfo::where('organizer_id', $organizer->id)->first();
@endphp


                            <form id="eventForm" action="{{ route('organizer.update_profile') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf

                                <div class="row">
                                    {{-- Profile Photo --}}
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">{{ __('Photo') . '*' }}</label><br>
                                            <div class="thumb-preview">
                                                @if (Auth::guard('organizer')->user()->photo)
                                                    <img src="{{ asset('assets/admin/img/organizer-photo/' . Auth::guard('organizer')->user()->photo) }}"
                                                        alt="..." class="uploaded-img">
                                                @else
                                                    <img src="{{ asset('assets/admin/img/noimage.jpg') }}" alt="..."
                                                        class="uploaded-img">
                                                @endif
                                            </div>
                                            <div class="mt-3">
                                                <div role="button" class="btn btn-primary btn-sm upload-btn">
                                                    {{ __('Choose Photo') }}
                                                    <input type="file" class="img-input" name="photo">
                                                </div>
                                                @error('photo')
                                                    <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                                                @enderror
                                                <p class="mt-1 mb-0 text-warning em">{{ __('Image Size 300x300') }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Contact Info --}}
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>{{ __('Email') . '*' }}</label>
                                            <input type="email" class="form-control" name="email"
                                                value="{{ Auth::guard('organizer')->user()->email }}">
                                            @error('email')
                                                <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>{{ __('Phone') }}</label>
                                            <input type="tel" class="form-control" name="phone"
                                                value="{{ Auth::guard('organizer')->user()->phone }}">
                                            @error('phone')
                                                <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>{{ __('Username') . '*' }}</label>
                                            <input type="text" class="form-control" name="username"
                                                value="{{ Auth::guard('organizer')->user()->username }}">
                                            @error('username')
                                                <p class="mt-2 mb-0 text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Social Media --}}
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>{{ __('Facebook') }}</label>
                                            <input type="text" class="form-control" name="facebook"
                                                value="{{ Auth::guard('organizer')->user()->facebook }}">
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>{{ __('Twitter') }}</label>
                                            <input type="text" class="form-control" name="twitter"
                                                value="{{ Auth::guard('organizer')->user()->twitter }}">
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>{{ __('Linkedin') }}</label>
                                            <input type="text" class="form-control" name="linkedin"
                                                value="{{ Auth::guard('organizer')->user()->linkedin }}">
                                        </div>
                                    </div>

                                    {{-- Basic Info --}}
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>{{ __('Name') . '*' }}</label>
                                            <input type="text" class="form-control" name="name"
                                               value="{{ $organizer_info->name ?? '' }}" placeholder="Enter Your Full Name">
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>{{ __('Designation') }}</label>
                                            <input type="text" class="form-control" name="designation"  value="{{ $organizer_info->designation ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>{{ __('Country') }}</label>
                                            <input type="text" class="form-control" name="country" value="{{ $organizer_info->country ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>{{ __('City') }}</label>
                                            <input type="text" class="form-control" name="city" value="{{ $organizer_info->city ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>{{ __('State') }}</label>
                                            <input type="text" class="form-control" name="state" value="{{ $organizer_info->state ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>{{ __('Zip Code') }}</label>
                                            <input type="text" class="form-control" name="zip_code" value="{{ $organizer_info->zip_code ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>{{ __('Address') }}</label>
                                            <textarea name="address" class="form-control">{{ $organizer_info->address ?? '' }}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>{{ __('Details') }}</label>
                                            <textarea name="details" class="form-control" rows="5">{{ $organizer_info->details ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </form>


                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-12 text-center">
                            <button type="submit" id="EventSubmit" class="btn btn-success">
                                {{ __('Update') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
