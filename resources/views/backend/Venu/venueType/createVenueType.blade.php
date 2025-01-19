<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">{{ __('Add Venue Type') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form class="modal-form create" action="{{ route('admin.storeVenueType') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="">{{ __('Name') . '*' }}</label>
                        <input type="text" class="form-control" name="type" placeholder="Enter VenueType Name">
                        <p id="err_name" class="mt-1 mb-0 text-danger em"></p>
                    </div>
     
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