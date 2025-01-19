<form method="POST" action="{{ route('organizer.packages.updatePackage') }}" id="form">
    @csrf
    <div class="modal-header">
    	<h5 class="modal-title" id="exampleModalLongTitle">Edit Package</h5>
    	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
    	<span aria-hidden="true">&times;</span>
    	</button>
    </div>
    <div class="modal-body">
    	<div class="row">
    	    <div class="col-md-12">
				<div class="row gy-4">
					<div class="col-xxl-12 col-md-12 mb-3">
						<div>
							<label for="basiInput" class="form-label">Package Name <span class="required">*</span></label>
							<input type="text" class="form-control number" placeholder="Package Name" name="package_name" autocomplete="off" value="<?=$package->packages_name;?>" required>
						</div>
					</div>
					<div class="col-xxl-12 col-md-12 mb-3">
						<div>
							<label for="basiInput" class="form-label">Packages Amount <small style="font-weight: bold !important; color: red;">(price in INR/ per person)</small><span class="required">*</span></label>
							<input type="text" class="form-control number" placeholder="Packages Amount" name="package_amount" autocomplete="off" value="<?=$package->packages_amount;?>" required>
						</div>
					</div>
				</div>
    	    </div>
    	    <div class="col-md-12">
    	        <hr>
    	    </div>
    	    <div class="col-md-12">
    	        <div class="row gy-4">
    	            <div class="col-md-12">
        	            <label for="basiInput" class="form-label">Package Meta <span class="required">*</span></label>
    	            </div>
                    <?php if($packageMetas){ foreach($packageMetas as $packageMeta){ ?>
                        <div class="col-md-12 packageMetaMainDiv">
                            <div class="row">
                                <div class="col-xxl-9 col-md-9 mb-3">
            						<div>
            							<label for="basiInput" class="form-label">Meta Value <span class="required">*</span></label>
            							<input type="hidden" class="form-control" placeholder="Meta Value" name="meta_id[]" autocomplete="off" value="<?=$packageMeta->packages_meta_id;?>" required>
            							<input type="text" class="form-control" placeholder="Meta Value" name="meta_value[]" autocomplete="off" value="<?=$packageMeta->packages_meta_value;?>" required>
            						</div>
            					</div>
            					<div class="col-xxl-3 col-md-3" style="align-content: center;">
                					<div class="input-group">
                						<button style="font-size: 12px" class="btn btn-danger removePackageMeta" onclick="remove('<?=$packageMeta->packages_meta_id;?>', '<?=url('/organizer/packages/removePackageData')?>')" type="button">Remove</button>
                					</div>
                				</div>
            				</div>
    					</div>
                    <?php } } ?>
                    
                    <div class="col-xxl-3 col-md-3 mb-3" style="align-content: end;">
						<div class="input-group">
							<button class="btn btn-primary addon-meta-button-edit w-100" type="button">Add</button>
						</div>
					</div>
    				<div class="col-lg-12">
				    	<div class="row">
        					<div class="col-xxl-12 col-md-12 mb-3">
        						<div class="addon-meta-edit"></div>
        					</div>
    					</div>
    				</div>
                </div>
    	    </div>
    	</div>
    </div>
    <div class="modal-footer">
        <input type="hidden" name="package_id" value="<?=$package->packages_id;?>">
        <button class="btn btn-sm btn-primary submit" type="submit">Update Package</button>
    	<button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>
    </div>
</form>