<div class="modal fade" id="editVenuModal_{{$data->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">

        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">{{ __('Edit Venue') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form class="modal-form create" action="{{ route('admin.editVenueService') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="">{{ __('Name') . '*' }}</label>
                        <input type="text" class="form-control" name="name" value=" {{$data->name}}" placeholder="Enter Venue Name">
                        <p id="err_name" class="mt-1 mb-0 text-danger em"></p>
                    </div>
                    <input type="hidden" name="data_id" value="{{ $data->id }}">

                    <div class="form-group">
                        <label for="">{{ __('Description') . '*' }}</label>
                        <input type="text" class="form-control" name="description" value="{{$data->description}}" placeholder="Enter Description">
                        <p id="err_name" class="mt-1 mb-0 text-danger em"></p>
                    </div>
                    <div class="form-group">
                        <label for="">{{ __('Price') . '*' }}</label>
                        <input type="text" class="form-control" name="price" value="{{$data->price}}" placeholder="Enter Venue price">
                        <p id="err_name" class="mt-1 mb-0 text-danger em"></p>
                    </div>
               
                    <div class="form-group">
                        <label for="">{{ __('Image') . '*' }}</label>
                        <br>
                        <div class="thumb-preview">
                            <!-- <img src="{{ asset('assets/admin/img/noimage.jpg') }}" alt="..." class="uploaded-img"> -->
                            <img src="{{ asset('VenueServices/' . $data->image) }}" class="uploaded-img" alt="">
                        </div>

                        <div class="mt-3">
                            <div role="button" class="btn btn-primary btn-sm upload-btn">
                                {{ __('Choose Image') }}
                                <input type="file" class="img-input" name="image">
                            </div>
                            <p id="err_image" class="mt-1 mb-0 text-danger em"></p>
                        </div>
                    </div>

                    <!-- 
          <div class="form-group">
            <label for="">{{ __('Image') . '*' }}</label>
            <br>
            <div class="thumb-preview">
              <img src="{{ asset('assets/admin/img/noimage.jpg') }}" alt="..." class="uploaded-img">
            </div>

            <div class="mt-3">
              <div role="button" class="btn btn-primary btn-sm upload-btn">
                {{ __('Choose Image') }}
                <input type="file" class="img-input" name="image">
              </div>
              <p id="err_image" class="mt-1 mb-0 text-danger em"></p>
            </div>
          </div> -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                            {{ __('Close') }}
                        </button>
                        <button id="modalSubmit" type="submit" class="btn btn-primary btn-sm">
                            {{ __('Save') }}
                        </button>
                    </div>
                </form>
            </div>


        </div>
    </div>
</div>