@extends('organizer.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Create Banquat Hall') }}</h4>
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
        <a href="#">{{ __('Banquat Hall') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title d-inline-block">{{ __('Add Event') }}</div>
          <a class="btn btn-info btn-sm float-right d-inline-block"
            href="{{ route('organizer.event_management.event', ['language' => $defaultLang->code]) }}">
            <span class="btn-label">
              <i class="fas fa-backward"></i>
            </span>
            {{ __('Back') }}
          </a>
        </div>
        <div class="card-body">
          <div class="row">
              <form action="{{ route('banquat.store') }}" method="POST" enctype="multipart/form-data">
                  <div class="col-lg-12 offset-lg-2">
                    <div class="alert alert-danger pb-1 dis-none" id="eventErrors">
                      <button type="button" class="close" data-dismiss="alert">×</button>
                      <ul></ul>
                    </div>
                    <div class="col-lg-12">
                      <label for="" class="mb-2"><strong>{{ __('Gallery Images') }} **</strong></label>
                      {{-- <form action="{{ route('organizer.event.imagesstore') }}" id="my-dropzone" enctype="multipart/formdata" class="dropzone create">
                        @csrf --}}
                        <div class="fallback">
                          <input name="images[]" type="file" multiple />
                        </div>
                      {{-- </form> --}}
                      <div class=" mb-0" id="errpreimg">
                          
                      </div>
                      <p class="text-warning">{{ __('Image Size') . ' 1170x570' }}</p>
                    </div>

                      @csrf

                      <div class="form-group">
                        <label for="">{{ __('Thumbnail Image') . '*' }}</label>
                        <br>
                        <div class="thumb-preview">
                          <img src="{{ asset('assets/admin/img/noimage.jpg') }}" alt="..." class="uploaded-img">
                        </div>

                        <div class="mt-3">
                          <div role="button" class="btn btn-primary btn-sm upload-btn">
                            {{ __('Choose Image') }}
                            <input type="file" class="img-input" name="featured_image" required>
                          </div>
                        </div>
                        <p class="text-warning">{{ __('Image Size') . ' : 320x230' }}</p>
                      </div>



                      <div class="row" id="single_dates">
                        <div class="col-lg-12">
                          <div class="form-group">
                            <label>{{ __('Banquat Hall Name') . '*' }}</label> 
                            <input type="text" name="name" placeholder="Enter the Banquat Hall Name" class="form-control" required>
                          </div>
                        </div>
                      </div>

                      <div class="row ">
                        <div class="col-lg-4">
                          <div class="form-group">
                            <label for="">{{ __('Status') . '*' }}</label>
                            <select name="status" class="form-control">
                              <option selected disabled>{{ __('Select a Status') }}</option>
                              <option value="1">{{ __('Active') }}</option>
                              <option value="0">{{ __('Deactive') }}</option>
                            </select>
                          </div>
                        </div>
                        
                        <div class="col-lg-4">
                          <div class="form-group">
                            <label for="parking">{{ __('Parking Available') . '*' }}</label>
                              <input type="checkbox" id="parking" name="is_parking" value="1" class="form-control">
                          </div>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col">
                          <div class="form-group text-right">
                            <label>{{ __('Description') . '*' }}</label>
                            <textarea id="descriptionTmce" class="form-control summernote"
                              name="description" placeholder="{{ __('Enter Event Description') }}" data-height="300"></textarea>
                          </div>
                        </div>
                      </div>


                      {{-- <div class="row">
                        <div class="col-lg-12">
                          <div class="form-group text-right">
                            <label>{{ __('Refund Policy') }} *</label>
                            <textarea class="form-control" name="" rows="5"
                              placeholder="{{ __('Enter Refund Policy') }}"></textarea>
                          </div>
                        </div>
                      </div> --}}
                      <div class="card-footer">
                        <div class="row">
                          <div class="col-12 text-center">
                            <button type="submit" class="btn btn-success">
                              {{ __('Save') }}
                            </button>
                          </div>
                        </div>
                      </div>
                  </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('script')
  @php
    $languages = App\Models\Language::get();
  @endphp
  <script>
    let languages = "{{ $languages }}";
  </script>
  <script type="text/javascript" src="{{ asset('assets/admin/js/admin-partial.js') }}"></script>
  <script src="{{ asset('assets/admin/js/admin_dropzone.js') }}"></script>
@endsection

@section('variables')
  <script>
    "use strict";
    var storeUrl = "{{ route('organizer.event.imagesstore') }}";
    var removeUrl = "{{ route('organizer.event.imagermv') }}";
    var loadImgs = 0;
  </script>
@endsection
