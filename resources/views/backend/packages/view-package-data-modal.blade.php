<div class="modal-header">
	<h5 class="modal-title" id="exampleModalLongTitle">View Package</h5>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
	<span aria-hidden="true">&times;</span>
	</button>
</div>
<div class="modal-body">
	<div class="row">
	    <div class="col-md-12">
	        <label>Package Name : <b><?=$package->packages_name;?></b></label>
	        <br>
	        <label>Package Amount : <b>INR <?=$package->packages_amount;?></b></label>
	    </div>
	    <div class="col-md-12">
	        <hr>
	    </div>
	    <div class="col-md-12">
	        <label>Package Meta : </label>
	        <br>
            <?php $cnt = 1; if($packageMetas){ foreach($packageMetas as $packageMeta){ ?>
                <label><b><?=$packageMeta->packages_meta_value;?></b></label><br>
            <?php } } ?>
	    </div>
	</div>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
</div>