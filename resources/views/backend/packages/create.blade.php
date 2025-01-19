@extends('organizer.layout')
@section('content')
<style>
	.required{color: red;}
</style>
<div class="page-header">
	<h4 class="page-title">{{ __('Admin Profile') }}</h4>
	<ul class="breadcrumbs">
		<li class="nav-home">
			<a href="{{ route('admin.dashboard') }}">
			<i class="flaticon-home"></i>
			</a>
		</li>
		<li class="separator">
			<i class="flaticon-right-arrow"></i>
		</li>
		<li class="nav-item">
			<a href="#">{{ __('Packages') }}</a>
		</li>
	</ul>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="card-header">
				<div class="row">
					<div class="col-lg-12">
						<div class="card-title">{{ __('Add Packages') }}</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-5 mt-3">
					<div class="row">
						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<h4 class="card-title mb-0">Add Package</h4>
								</div>
								<!-- end card header -->
								<div class="card-body">
									<form method="POST" action="{{ route('organizer.packages.store') }}" id="form">
										@csrf
										<div class="row gy-4">
											<div class="col-xxl-12 col-md-12 mb-3">
												<div>
													<label for="basiInput" class="form-label">Package Name <span class="required">*</span></label>
													<input type="text" class="form-control number" placeholder="Package Name" name="package_name" autocomplete="off" required>
												</div>
											</div>
											<div class="col-xxl-12 col-md-12 mb-3">
												<div>
													<label for="basiInput" class="form-label">Packages Amount <small style="font-weight: bold !important; color: red;">(price in INR/ per person)</small><span class="required">*</span></label>
													<input type="text" class="form-control number" placeholder="Packages Amount" name="package_amount" autocomplete="off" required>
												</div>
											</div>
											<div class="col-lg-6">
												<div class="input-group">
													<button class="btn btn-sm btn-primary submit" type="submit">Save Package</button>
												</div>
											</div>
										</div>
									</form>
									<!--end col-->
								</div>
							</div>
						</div>
						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<h4 class="card-title mb-0">Add Packages Meta</h4>
								</div>
								<!-- end card header -->
								<div class="card-body">
									<form method="POST" action="{{ route('organizer.packages.storePackageMeta') }}" id="form_">
										@csrf
										<div class="row gy-4">
											<div class="col-xxl-12 col-md-12 mb-3">
												<div>
													<label for="basiInput" class="form-label">Packages Name <span class="required">*</span></label>
													<select class="form-control" placeholder="Packages Name" name="package_id" autocomplete="off" required>
														<option value="">Select Packages Name</option>
														<?php if($packages){ foreach($packages as $package){ ?>
														    <option value="<?=$package->packages_id;?>"><?=$package->packages_name;?></option>
														<?php } } ?>
													</select>
												</div>
											</div>
											<div class="col-xxl-9 col-md-9 mb-3">
												<div>
													<label for="basiInput" class="form-label">Meta Value <span class="required">*</span></label>
													<input type="text" class="form-control" placeholder="Meta Value" name="meta_value[]" autocomplete="off" required>
												</div>
											</div>
											<div class="col-xxl-3 col-md-3 mb-3" style="align-content: end;">
												<div class="input-group">
													<button class="btn btn-primary addon-meta-button w-100" type="button">Add</button>
												</div>
											</div>
											<div class="col-xxl-9 col-md-9 mb-3">
												<div class="addon-meta"></div>
											</div>
											<div class="col-lg-6">
												<div class="input-group">
													<button class="btn btn-sm btn-primary submit" type="submit">Save Packages Meta</button>
												</div>
											</div>
										</div>
									</form>
									<!--end col-->
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-7 mt-3">
					<div class="row">
						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<h4 class="card-title mb-0">Listing Packages</h4>
								</div>
								<!-- end card header -->
								<div class="card-body">
									<div class="row gy-4">
										<div class="col-xxl-12 col-md-12 mb-3">
											<div class="table-responsive">
                                                <table class="table table-striped">
                                                    <tr>
                                                        <th>S. No.</th>
                                                        <th>Package Name</th>
                                                        <th>Package Amount</th>
                                                        <th>Package Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                    <?php $cnt = 1; if($packages){ foreach($packages as $package){ ?>
                                                        <tr>
                                                            <td><?=$cnt++;?></td>
                                                            <td><?=$package->packages_name;?></td>
                                                            <td>INR <?=$package->packages_amount;?></td>
                                                            <td>
                                                                <?php if($package->packages_status===1){ ?>
                                                                    <button style="" class="btn btn-sm btn-primary" type="button">Active</button>
                                                                <?php }else{ ?>
                                                                    <button style="" class="btn btn-sm btn-danger" type="button">De-active</button>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <button class="btn btn-sm btn-primary" onclick="rendor_modal('<?=$package->packages_id;?>', '<?=url('/organizer/packages/viewPackageData')?>')" type="button">View</button>
                                                                <button class="btn btn-sm btn-primary" onclick="rendor_modal('<?=$package->packages_id;?>', '<?=url('/organizer/packages/editPackageData')?>')" type="button">Edit</button>
                                                                <button class="btn btn-sm btn-danger" type="button">Remove</button>
                                                            </td>
                                                        </tr>
                                                    <?php } } ?>
                                                </table>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content pasteHere">
			
		</div>
	</div>
</div>


@endsection
@section('script')
<script>
    $(document).on('click', '.addon-meta-button', function() {
		$('.addon-meta').append(
			`<div class="row mb-4">
				<div class="col-xxl-9 col-md-9">
					<div>
						<label for="basiInput" class="form-label">Meta Value <span class="required">*</span></label>
						<input type="text" class="form-control" placeholder="Meta Value" name="meta_value[]" autocomplete="off" required>
					</div>
				</div>
				<div class="col-xxl-3 col-md-3" style="align-content: end;">
					<div class="input-group">
						<button style="font-size: 12px" class="btn btn-danger remove" type="button">Remove</button>
					</div>
				</div>
			</div>`
		);
	});
	$(document).on('click', '.addon-meta-button-edit', function() {
		$('.addon-meta-edit').append(
			`<div class="row mb-4">
				<div class="col-xxl-9 col-md-9">
					<div>
						<label for="basiInput" class="form-label">Meta Value <span class="required">*</span></label>
						<input type="text" class="form-control" placeholder="Meta Value" name="meta_value_edit[]" autocomplete="off" required>
					</div>
				</div>
				<div class="col-xxl-3 col-md-3" style="align-content: end;">
					<div class="input-group">
						<button style="font-size: 12px" class="btn btn-danger remove" type="button">Remove</button>
					</div>
				</div>
			</div>`
		);
	});
	$(document).on('click', '.remove', function() {
		$(this).closest('.row').remove();
	});
</script>
<script>
    // rendor modal
    function rendor_modal(id, url){
        $('#modal').modal('show');
        $.ajax({
            url: url,
            type: "GET",
            cache: false,
            data : { id : id },
            beforeSend: function(){
                $('.pasteHere').html(`<div class="row">
                    <div class="col-md-12">
                        <div class="text-center mb-3 mt-3">
                            Loading...
                        </div>
                    </div>
                </div>`);
            },
            success: function(result){
                $('.pasteHere').html(result);
            },
            error: function(){
                $('#modal').modal('hide');
            },
        });
    }
</script>
<script>
    // remove
    function remove(id, url){
        $.ajax({
        	url: url,
        	type: "GET",
        	cache: false,
        	data : { id : id },
        	beforeSend: function(){},
        	success: function(result){
        	    
        	},
        	error: function(){},
        });
    }
</script>
<script>
    $(document).on('click', '.removePackageMeta', function() {
		$(this).closest('.packageMetaMainDiv').remove();
	});
</script>
@endsection
