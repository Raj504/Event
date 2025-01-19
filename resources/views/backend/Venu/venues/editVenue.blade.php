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
                <form class="modal-form create" action="{{ route('admin.editVenue') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="">{{ __('Name') . '*' }}</label>
                        <input type="text" class="form-control" name="name" value=" {{$data->name}}" placeholder="Enter Venue Name">
                        <p id="err_name" class="mt-1 mb-0 text-danger em"></p>
                    </div>
                    <input type="hidden" class="form-control" name="organizer_id" value="33">
                    <input type="hidden" name="data_id" value="{{ $data->id }}">



                    <div class="form-group">
                        <label for="">{{ __('Type') . '*' }}</label>
                        <select name="type" class="form-control">
                            <option selected disabled>{{ __('Select a Type') }}</option>
                            @foreach($venueTypeData as $dataType)

                            <!-- <option value="{{$dataType->id}}">{{$dataType->type}}</option> -->
                            <option value="{{ $dataType->id }}" {{ $data->type == $dataType->id ? 'selected' : '' }}>
                                {{ $dataType->type }}
                            </option>
                            @endforeach
                        </select>
                        <p id="err_language_id" class="mt-1 mb-0 text-danger em"></p>
                    </div>
                    <div class="form-group">
                        <label for="">{{ __('category') . '*' }}</label>
                        <select name="category" class="form-control">
                            <option selected disabled>{{ __('Select a Category') }}</option>
                            @foreach($venuCategoryData as $categoryData)
                            <option value="{{$categoryData->id}}" {{ $data->category == $categoryData->id ? 'selected' : '' }}>{{$categoryData->name}}</option>
                            @endforeach
                        </select>
                        <p id="err_language_id" class="mt-1 mb-0 text-danger em"></p>
                    </div>
                    <div class="form-group">
                        <label for="">{{ __('Description') . '*' }}</label>
                        <input type="text" class="form-control" name="description" value="{{$data->description}}" placeholder="Enter Description">
                        <p id="err_name" class="mt-1 mb-0 text-danger em"></p>
                    </div>
                    <div class="form-group">
                        <label for="">{{ __('Price') . '*' }}</label>
                        <input type="text" class="form-control" name="description" value="{{$data->price}}" placeholder="Enter Venue price">
                        <p id="err_name" class="mt-1 mb-0 text-danger em"></p>
                    </div>
                    <div class="form-group">
                        <label for="">{{ __('Veg Price') . '*' }}</label>
                        <input type="text" class="form-control" name="veg_price" value="{{$data->veg_price}}" placeholder="Enter food Veg price">
                        <p id="err_name" class="mt-1 mb-0 text-danger em"></p>
                    </div>
                    <div class="form-group">
                        <label for="">{{ __('NonVeg Price') . '*' }}</label>
                        <input type="text" class="form-control" name="non_veg_price" value="{{$data->non_veg_price}}" placeholder="Enter food Veg price">
                        <p id="err_name" class="mt-1 mb-0 text-danger em"></p>
                    </div>
                    <div class="form-group">
                        <label for="">{{ __('Capacity') . '*' }}</label>
                        <input type="text" class="form-control" name="capacity" value="{{$data->capacity}}" placeholder="Enter Total People Capacity">
                        <p id="err_name" class="mt-1 mb-0 text-danger em"></p>
                    </div>
                    <div class="form-group">
                        <label for="">{{ __('Total Rooms') . '*' }}</label>
                        <input type="text" class="form-control" name="total_rooms" value="{{$data->total_rooms}}" placeholder="Enter Total Room Avaliable">
                        <p id="err_name" class="mt-1 mb-0 text-danger em"></p>
                    </div>
                    <div class="form-group">
                        <label for="freeServices">{{ __('Free Services Select') . '*' }}</label>
                        <div>
                            <input type="checkbox" id="service1" name="wifi" value="{{$data->wifi}}" class="form-control" {{ $data->wifi == 1 ? 'checked' : '' }}>
                            <label for="service1">WiFi</label>
                        </div>
                        <div>
                            <input type="checkbox" id="service2" name="parking" value="1" class="form-control" {{ $data->parking == 1 ? 'checked' : '' }}>
                            <label for="service2">Parking</label>
                        </div>
                        <div>
                            <input type="checkbox" id="service3" name="ac" value="1" class="form-control" {{ $data->ac == 1 ? 'checked' : '' }}>
                            <label for="service3">Ac</label>
                        </div>
                        <div>
                            <input type="checkbox" id="service4" name="decoration" value="1" class="form-control" {{ $data->decoration == 1 ? 'checked' : '' }}>
                            <label for="service4">Decoration</label>
                        </div>
                        <div>
                            <input type="checkbox" id="service5" name="bar" value="1" class="form-control " {{ $data->bar == 1 ? 'checked' : '' }}>
                            <label for="service5">Bar</label>
                        </div>
                        <p id="err_name" class="mt-1 mb-0 text-danger em"></p>
                    </div>


                    <div class="form-group">
                        <label for="">{{ __('Add Location') . '*' }}</label>
                        <input type="text" class="form-control" name="location" value="{{$data->location}}" placeholder="Enter Area Location">
                        <p id="err_name" class="mt-1 mb-0 text-danger em"></p>
                    </div>
                    <div class="form-group">
                        <label for="">{{ __('Cancellation Policy') . '*' }}</label>
                        <select name="cancellation_policy" class="form-control">
                            <option value="" disabled>{{ __('Select a Cancellation Policy') }}</option>
                            <option value="Non-refundable" {{ $data->cancellation_policy == 'Non-refundable' ? 'selected' : '' }}>
                                {{ __('Non-refundable') }}
                            </option>
                            <option value="Refundable" {{ $data->cancellation_policy == 'Refundable' ? 'selected' : '' }}>
                                {{ __('Refundable') }}
                            </option>
                        </select>
                        <p id="err_language_id" class="mt-1 mb-0 text-danger em"></p>
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