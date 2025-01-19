<style>
    .image-upload-container {
    width: 100%;
    text-align: center;
}

#imageUpload {
    margin-bottom: 10px;
}

#imagePreview {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
}

.image-container {
    position: relative;
    display: inline-block;
}

.image-container img {
    width: 100px;
    height: 100px;
    object-fit: cover;
}

.remove-image {
    position: absolute;
    top: 5px;
    right: 5px;
    background-color: red;
    color: white;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    padding: 2px 6px;
}

</style>
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">{{ __('Add Venue') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form class="modal-form create" action="{{ route('admin.storeVenue') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="">{{ __('Name') . '*' }}</label>
                        <input type="text" class="form-control" name="name" placeholder="Enter Venue Name">
                        <p id="err_name" class="mt-1 mb-0 text-danger em"></p>
                    </div>
                    <input type="hidden" class="form-control" name="organizer_id" value="33" placeholder="Enter Venue Name">


                    <div class="form-group">
                        <label for="">{{ __('Type') . '*' }}</label>
                        <select name="type" class="form-control">
                            <option selected disabled>{{ __('Select a Type') }}</option>
                            @foreach($venueTypeData as $data)
                            <option value="{{$data->id}}">{{$data->type}}</option>
                            @endforeach
                        </select>
                        <p id="err_language_id" class="mt-1 mb-0 text-danger em"></p>
                    </div>
                    <div class="form-group">
                        <label for="">{{ __('category') . '*' }}</label>
                        <select name="category" class="form-control">
                            <option selected disabled>{{ __('Select a Category') }}</option>
                            @foreach($venuCategoryData as $categoryData)
                            <option value="{{$categoryData->id}}">{{$categoryData->name}}</option>
                            @endforeach
                        </select>
                        <p id="err_language_id" class="mt-1 mb-0 text-danger em"></p>
                    </div>
                    <div class="form-group">
                        <label for="">{{ __('Description') . '*' }}</label>
                        <input type="text" class="form-control" name="description" placeholder="Enter Description">
                        <p id="err_name" class="mt-1 mb-0 text-danger em"></p>
                    </div>
                    <div class="form-group">
                        <label for="">{{ __('Price') . '*' }}</label>
                        <input type="text" class="form-control" name="price" placeholder="Enter Venue price">
                        <p id="err_name" class="mt-1 mb-0 text-danger em"></p>
                    </div>
                    <div class="form-group">
                        <label for="">{{ __('Veg Price') . '*' }}</label>
                        <input type="text" class="form-control" name="veg_price" placeholder="Enter food Veg price">
                        <p id="err_name" class="mt-1 mb-0 text-danger em"></p>
                    </div>
                    <div class="form-group">
                        <label for="">{{ __('NonVeg Price') . '*' }}</label>
                        <input type="text" class="form-control" name="non_veg_price" placeholder="Enter food Veg price">
                        <p id="err_name" class="mt-1 mb-0 text-danger em"></p>
                    </div>
                    <div class="form-group">
                        <label for="">{{ __('Capacity') . '*' }}</label>
                        <input type="text" class="form-control" name="capacity" placeholder="Enter Total People Capacity">
                        <p id="err_name" class="mt-1 mb-0 text-danger em"></p>
                    </div>
                    <div class="form-group">
                        <label for="">{{ __('Total Rooms') . '*' }}</label>
                        <input type="text" class="form-control" name="total_rooms" placeholder="Enter Total Room Avaliable">
                        <p id="err_name" class="mt-1 mb-0 text-danger em"></p>
                    </div>
                    <div class="form-group">
                        <label for="freeServices">{{ __('Free Services Select') . '*' }}</label>
                        <div>
                            <input type="checkbox" id="service1" name="wifi" value="1" class="form-control">
                            <label for="service1">WiFi</label>
                        </div>
                        <div>
                            <input type="checkbox" id="service2" name="parking" value="1" class="form-control">
                            <label for="service2">Parking</label>
                        </div>
                        <div>
                            <input type="checkbox" id="service3" name="ac" value="1" class="form-control">
                            <label for="service3">Ac</label>
                        </div>
                        <div>
                            <input type="checkbox" id="service4" name="decoration" value="1" class="form-control">
                            <label for="service4">Decoration</label>
                        </div>
                        <div>
                            <input type="checkbox" id="service5" name="bar" value="1" class="form-control">
                            <label for="service5">Bar</label>
                        </div>
                        <p id="err_name" class="mt-1 mb-0 text-danger em"></p>
                    </div>


                    <div class="form-group">
                        <label for="">{{ __('Add Location') . '*' }}</label>
                        <input type="text" class="form-control" name="location" placeholder="Enter Area Location">
                        <p id="err_name" class="mt-1 mb-0 text-danger em"></p>
                    </div>
                    <div class="form-group">
                        <label for="images">{{ __('Images') . '*' }}</label>
                        <input type="file" id="imageUpload" name="images[]" class="form-control" multiple>
                        <div id="imagePreview" class="mt-2"></div>
                        <p id="imageInfo"></p>
                        <p id="err_images" class="mt-1 mb-0 text-danger em"></p>
                    </div>
                    <div class="form-group">
                        <label for="">{{ __('Cancellation Policy') . '*' }}</label>
                        <select name="cancellation_policy" class="form-control">
                            <option selected disabled>{{ __('Select a Category') }}</option>
                            <option value="Non-refundable">Non-refundable</option>
                            <option value="Refundable">Refundable</option>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('imageUpload');
        const imagePreview = document.getElementById('imagePreview');
        const imageInfo = document.getElementById('imageInfo');
        let filesArray = [];

        fileInput.addEventListener('change', function(event) {
            const files = Array.from(event.target.files);
            filesArray = filesArray.concat(files);
            updateImagePreview(filesArray);
            updateImageInfo(filesArray);
        });

        function updateImagePreview(files) {
            imagePreview.innerHTML = '';
            files.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imageContainer = document.createElement('div');
                    imageContainer.classList.add('image-container');

                    const img = document.createElement('img');
                    img.src = e.target.result;

                    const removeButton = document.createElement('button');
                    removeButton.classList.add('remove-image');
                    removeButton.innerText = 'X';
                    removeButton.addEventListener('click', () => {
                        filesArray.splice(index, 1);
                        updateImagePreview(filesArray);
                        updateImageInfo(filesArray);
                    });

                    imageContainer.appendChild(img);
                    imageContainer.appendChild(removeButton);
                    imagePreview.appendChild(imageContainer);
                };
                reader.readAsDataURL(file);
            });
        }

        function updateImageInfo(files) {
            if (files.length > 0) {
                imageInfo.innerText = `Total Images: ${files.length}`;
            } else {
                imageInfo.innerText = '';
            }
        }
    });
</script>