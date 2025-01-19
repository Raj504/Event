@extends('frontend.layout')

@section('content')
    <section class="payment-section pt-50 pb-50">
        <div class="container">
            <div class="payment-details">
                <h2>{{ __('Pay Installment') }}</h2>

              

                <form class="form" action="{{ route('ticket.booking', [$booking->id, 'type' => 'guest']) }}" method="POST"
                enctype="multipart/form-data" id="payment-form">
                @csrf
				


                <label for="emi-amount">{{ __('EMI Amount') }}</label>
				<span class="amount">{{ $booking->currencySymbol }} {{ $booking->emi_amount }}</span>
				


                @php
				$grandTotal = Session::get('grand_total');
				@endphp
                <input type="hidden" name="grand_total" id="grand-total" value="{{ $grandTotal }}">
				<div class="form-group">
					<!-- <button type="button" id="emi-button" class="btn btn-primary mt-3">{{ __('Pay via EMI') }}</button> -->
					
					
					<select name="gateway" id="payment">
						<option value="">{{ __('Select a payment method') }}</option>
						@foreach ($online_gateways as $online_gateway)
						<option value="{{ $online_gateway->keyword }}"
						{{ $online_gateway->keyword == old('gateway') ? 'selected' : '' }}>
						{{ __("$online_gateway->name") }}</option>
						@endforeach
						@foreach ($offline_gateways as $offline_gateway)
						<option value="{{ $offline_gateway->id }}"
						{{ $offline_gateway->id == old('gateway') ? 'selected' : '' }}>
						{{ __("$offline_gateway->name") }}</option>
						@endforeach
					</select>
					<div id="couponReload">

						@error('gateway')
						<p class="text-danger">{{ $message }}</p>
						@enderror()
						@if (Session::has('currency_error'))
						<p class="text-danger">{{ Session::get('currency_error') }}</p>
						@endif
					</div>
					<div id="stripe-element" class="mb-2">
						<!-- A Stripe Element will be inserted here. -->
					</div>
					<!-- Used to display form errors -->
					<div id="stripe-errors" role="alert" class="mb-2"></div>
					@foreach ($offline_gateways as $offlineGateway)
					<div class="@if ($errors->has('attachment') && request()->session()->get('gatewayId') == $offlineGateway->id) d-block @else d-none @endif offline-gateway-info"
						id="{{ 'offline-gateway-' . $offlineGateway->id }}">
						@if (!is_null($offlineGateway->short_description))
						<div class="form-group mb-4">
							<label>{{ __('Description') }}</label>
							<p>{{ $offlineGateway->short_description }}</p>
						</div>
						@endif
						@if (!is_null($offlineGateway->instructions))
						<div class="form-group mb-4">
							<label>{{ __('Instructions') }}</label>
							<div class="summernote-content">
								{!! $offlineGateway->instructions !!}
							</div>
						</div>
						@endif
						@if ($offlineGateway->has_attachment == 1)
						<div class="form-group mb-4">
							<label>{{ __('Attachment') . '*' }}</label>
							<br>
							<input type="file" name="attachment">
							@error('attachment')
							<p class="text-danger mt-1">{{ $message }}</p>
							@enderror
							<p></p>
						</div>
						@endif
					</div>
					@endforeach
					<button type="submit" class="theme-btn w-100">{{ __('Proceed to Pay') }}</button>
                  
					
                
            </div>
            </form>
                </div>
    </section>
@endsection

@section('custom-script')
<script src="https://js.stripe.com/v3/"></script>
<script type="text/javascript">
	let url = "{{ route('apply-coupon') }}";
	let stripe_key = "{{ $stripe_key }}";
</script>
<script src="{{ asset('assets/front/js/event_checkout.js') }}"></script>
@endsection
