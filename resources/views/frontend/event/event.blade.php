@extends('frontend.layout')
@section('pageHeading')
  @if (!empty($pageHeading))
    {{ $pageHeading->event_page_title ?? __('Events') }}
  @else
    {{ __('Events') }}
  @endif
@endsection

@php
  $metaKeywords = !empty($seo->meta_keyword_event) ? $seo->meta_keyword_event : '';
  $metaDescription = !empty($seo->meta_description_event) ? $seo->meta_description_event : '';
@endphp
@section('meta-keywords', "{{ $metaKeywords }}")
@section('meta-description', "$metaDescription")

@section('hero-section')
  <!-- Page Banner Start -->
  <section class="page-banner overlay pt-120 pb-125 rpt-90 rpb-95 lazy"
    data-bg="{{ asset('assets/admin/img/' . $basicInfo->breadcrumb) }}">
    <div class="container">
      <div class="banner-inner">
        <h2 class="page-title">
          @if (!empty($pageHeading))
            {{ $pageHeading->event_page_title ?? __('Events') }}
          @else
            {{ __('Events') }}
          @endif
        </h2>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('index') }}">{{ __('Home') }}</a></li>
            <li class="breadcrumb-item active">
              @if (!empty($pageHeading))
                {{ $pageHeading->event_page_title ?? __('Events') }}
              @else
                {{ __('Events') }}
              @endif
            </li>
          </ol>
        </nav>
      </div>
    </div>
  </section>
  <!-- Page Banner End -->
@endsection
@section('content')
  <!-- Event Page Start -->
  <section class="event-page-section py-120 rpy-100">
    <div class="container container-custom">
      <div class="row">
        <!-- <div class="col-lg-3">
          <div class="sidebar rmb-75">
            <div class="widget widget-search">
              <form action="{{ route('events') }}">

                <input type="text" name="search-input"
                  value="{{ !empty(request()->input('search-input')) ? request()->input('search-input') : '' }}"
                  placeholder="{{ __('Search') }}.....">
                @if (request()->filled('category'))
                  <input type="hidden" id="category-id" name="category"
                    value="{{ !empty(request()->input('category')) ? request()->input('category') : '' }}">
                @endif
                @if (request()->filled('event'))
                  <input type="hidden" name="event"
                    value="{{ !empty(request()->input('event')) ? request()->input('event') : '' }}">
                @endif
                @if (request()->filled('min'))
                  <input type="hidden" name="min"
                    value="{{ !empty(request()->input('min')) ? request()->input('min') : '' }}">
                @endif

                @if (request()->filled('max'))
                  <input type="hidden" name="max"
                    value="{{ !empty(request()->input('max')) ? request()->input('max') : '' }}">
                @endif

                @if (request()->filled('location'))
                  <input type="hidden" name="location"
                    value="{{ !empty(request()->input('location')) ? request()->input('location') : '' }}">
                @endif

                @if (request()->filled('dated'))
                  <input type="hidden" name="dates"
                    value="{{ !empty(request()->input('dates')) ? request()->input('dates') : '' }}">
                @endif
                <button type="submit" class="fa fa-search event-search-button"></button>
              </form>
            </div>


           
            <div class="widget widget-dropdown">
              <div class="form-group">
                <label for="">{{ __('Filter by Date') }}</label>
                <input type="text" placeholder="{{ __('Start/End Date') }}"
                  @if (request()->input('dates') && request()->input('dates')) value="{{ request()->input('dates') }}" @endif name="daterange" />
              </div>
            </div>
           


            <div class="widget widget-search">
              <form action="{{ route('events') }}">

                @if (request()->filled('search-input'))
                  <input type="hidden" name="search-input"
                    value="{{ !empty(request()->input('search-input')) ? request()->input('search-input') : '' }}">
                @endif

                @if (request()->filled('category'))
                  <input type="hidden" id="category-id" name="category"
                    value="{{ !empty(request()->input('category')) ? request()->input('category') : '' }}">
                @endif

                @if (request()->filled('event'))
                  <input type="hidden" name="event"
                    value="{{ !empty(request()->input('event')) ? request()->input('event') : '' }}">
                @endif

                <input type="text" name="location"
                  value="{{ !empty(request()->input('location')) ? request()->input('location') : '' }}"
                  placeholder="{{ __('Enter Location') }}">

                @if (request()->filled('dates'))
                  <input type="hidden" name="dates"
                    value="{{ !empty(request()->input('dates')) ? request()->input('dates') : '' }}">
                @endif

                @if (request()->filled('min'))
                  <input type="hidden" name="min"
                    value="{{ !empty(request()->input('min')) ? request()->input('min') : '' }}">
                @endif

                @if (request()->filled('max'))
                  <input type="hidden" name="max"
                    value="{{ !empty(request()->input('max')) ? request()->input('max') : '' }}">
                @endif
                <button type="submit" class="fa fa-search  event-search-button"></button>
              </form>
            </div>
            <div class="widget widget-cagegory">
              <h5 class="widget-title">{{ __('Category') }}</h5>
              <form action="{{ route('events') }}" id="catForm">
                @if (request()->filled('search-input'))
                  <input type="hidden" name="search-input"
                    value="{{ !empty(request()->input('search-input')) ? request()->input('search-input') : '' }}">
                @endif

                <select id="category" name="category" class="widget-select">
                  <option disabled>{{ __('Select a Category') }}</option>
                  <option value="">{{ __('All') }}</option>
                  @foreach ($information['categories'] as $item)
                    <option {{ request()->input('category') == $item->slug ? 'selected' : '' }}
                      value="{{ $item->slug }}">{{ $item->name }}</option>
                  @endforeach
                </select>
                

                @if (request()->filled('location'))
                  <input type="hidden" name="location"
                    value="{{ !empty(request()->input('location')) ? request()->input('location') : '' }}">
                @endif

                @if (request()->filled('event'))
                  <input type="hidden" name="event"
                    value="{{ !empty(request()->input('event')) ? request()->input('event') : '' }}">
                @endif

                @if (request()->filled('min'))
                  <input type="hidden" name="min"
                    value="{{ !empty(request()->input('min')) ? request()->input('min') : '' }}">
                @endif

                @if (request()->filled('max'))
                  <input type="hidden" name="max"
                    value="{{ !empty(request()->input('max')) ? request()->input('max') : '' }}">
                @endif

                @if (request()->filled('dates'))
                  <input type="hidden" name="dates"
                    value="{{ !empty(request()->input('dates')) ? request()->input('dates') : '' }}">
                @endif
              </form>
            </div>
            <div class="widget widget-location">
              <h5 class="widget-title">{{ __('Events') }}</h5>
              <div class="widget-radio">
                <div class="custom-control custom-radio">
                  <input type="radio" class="custom-control-input"
                    {{ request()->input('event') == 'online' ? 'checked' : '' }} value="online" name="event"
                    id="radio1">
                  <label class="custom-control-label" for="radio1">{{ __('Online Events') }}</label>
                </div>
                <div class="custom-control custom-radio">
                  <input type="radio" class="custom-control-input" value="venue"
                    {{ request()->input('event') == 'venue' ? 'checked' : '' }} name="event" id="radio2">
                  <label class="custom-control-label" for="radio2">{{ __('Venue Events') }}</label>
                </div>
              </div>
            </div>


            <div class="widget price-filter-widget">
              <h5 class="widget-title">{{ __('Price Filter') }}</h5>
              <div class="price-slider-range" id="range-slider"></div>
              <div class="price-btn">
                <input type="text" dir="ltr" id="price" value="{{ request()->input('min') }}" readonly>
                <button class="theme-btn" id="slider_submit">{{ __('Price Filter') }}</button>
              </div>
            </div>
            @if (!empty(showAd(2)))
              <div class="text-center mt-4 side-img">
                {!! showAd(2) !!}
              </div>
            @endif
          </div>
        </div> -->
        <div class="col-lg-12 m-auto">
          <div class="event-page-content">
          @if (count($information['events']) > 0)
            @foreach ($information['events'] as $event)
              @php
                // Fetch the ticket based on event type
                if ($event->event_type == 'online') {
                  $ticket = App\Models\Event\Ticket::where('event_id', $event->id)
                    ->orderBy('price', 'asc')
                    ->first();
                } else {
                  $ticket = App\Models\Event\Ticket::where([['event_id', $event->id], ['price', '!=', null]])
                    ->orderBy('price', 'asc')
                    ->first();
                  if (empty($ticket)) {
                    $ticket = App\Models\Event\Ticket::where([['event_id', $event->id], ['f_price', '!=', null]])
                      ->orderBy('price', 'asc')
                      ->first();
                  }
                }
                
                // Determine early bird discount if applicable
                $calculate_price = $ticket ? $ticket->price : null;
                if ($ticket && $ticket->early_bird_discount == 'enable') {
                  $discount_date = Carbon\Carbon::parse($ticket->early_bird_discount_date . $ticket->early_bird_discount_time);
                  if ($ticket->early_bird_discount_type == 'fixed' && !$discount_date->isPast()) {
                    $calculate_price = $ticket->price - $ticket->early_bird_discount_amount;
                  } elseif ($ticket->early_bird_discount_type == 'percentage' && !$discount_date->isPast()) {
                    $p_price = ($ticket->price * $ticket->early_bird_discount_amount) / 100;
                    $calculate_price = $ticket->price - $p_price;
                  }
                }

               

              @endphp
              
              <div class="row hotel-card">
                <div class="col-lg-4 col-12 d-flex justify-content-center align-items-center">
                  <div class="w-100">
                    <a href="{{ route('event.details', [$event->slug, $event->id]) }}">
                      <img class="lazy hotel-image w-100"
                        data-src="{{ asset('assets/admin/img/event/thumbnail/' . $event->thumbnail) }}"
                        alt="Event" style="max-height:250px;">
                    </a>
                  </div>
                </div>
                <div class="col-lg-5 col-12 pt-2">
                  <div class="ms-3 flex-grow-1">
                    <h5>
                      <a href="{{ route('event.details', [$event->slug, $event->id]) }}">
                        @if (strlen($event->title) > 70)
                          {{ mb_substr($event->title, 0, 70) . '...' }}
                        @else
                          {{ $event->title }}
                        @endif
                      </a>
                    </h5>
                    <!-- <ul class="time-info" dir="ltr">
                      @php
                        if ($event->date_type == 'multiple') {
                            $event_date = eventLatestDates($event->id);
                            $date = strtotime(@$event_date->start_date);
                        } else {
                            $date = strtotime($event->start_date);
                        }
                      @endphp
                      <li>
                        <i class="far fa-calendar-alt"></i>
                        <span>{{ \Carbon\Carbon::parse($date)->timezone($websiteInfo->timezone)->translatedFormat('d M') }}</span>
                      </li>
                      <li>
                        <i class="far fa-hourglass"></i>
                        <span title="Event Duration">
                          {{ $event->date_type == 'multiple' ? @$event_date->duration : $event->duration }}
                        </span>
                      </li>
                      <li>
                        <i class="far fa-clock"></i>
                        <span>
                          @php $start_time = strtotime($event->start_time); @endphp
                          {{ \Carbon\Carbon::parse($start_time)->timezone($websiteInfo->timezone)->translatedFormat('h:s A') }}
                        </span>
                      </li>
                    </ul> -->
                    @php $desc = strip_tags($event->description); @endphp
                    <p class="event-description">
                      {{ strlen($desc) > 100 ? mb_substr($desc, 0, 100) . '....' : $desc }}
                    </p>
                    <div class="amenities">
                      @if ($event->event_type == 'venue')
                        <span><i class="fas fa-map-marker-alt"></i> {{ $event->city }}, {{ $event->country }}</span>
                      @else
                        <span><i class="fas fa-map-marker-alt"></i> Online</span>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-lg-3 col-12">
                  <div class="text-end pricing-side">
                    @if ($ticket)
                    <div class="event-price">
    <div class="d-flex justify-content-center align-items-center price-discount">
        <span class="price-value h4 fw-bold me-2">
            {{ $calculate_price ? symbolPrice($calculate_price) : __('Free') }}
        </span>
        @if ($ticket->early_bird_discount == 'enable' && !$discount_date->isPast())
            <span class="original-price text-muted text-decoration-line-through me-2">
               <del> {{ symbolPrice($ticket->price) }} </del>
            </span>
        @endif
    </div>
   
    @if(request('checkin_date') || request('checkout_date'))
    <div class="searched-dates mt-2">
        <i class="fas fa-calendar-alt me-1"></i>
        <span class="text-muted">
            ({{ request('checkin_date') ? \Carbon\Carbon::parse(request('checkin_date'))->format('d M Y') : '' }} 
            - 
            {{ request('checkout_date') ? \Carbon\Carbon::parse(request('checkout_date'))->format('d M Y') : '' }})
        </span>
    </div>
   
    @foreach($events as $event)
    <div class="event">

        <p>Location: {{ $event->city }}, {{ $event->state }}, {{ $event->country }}</p>
        <p>Booked On: {{ $event->book_date ? \Carbon\Carbon::parse($event->book_date)->format('F j, Y') : 'Not Booked' }}</p>
        
        <!-- Check for unavailability based on session check-in and check-out dates -->
        @php
            $checkinDate = session('checkin_date') ? \Carbon\Carbon::parse(session('checkin_date')) : null;
            $checkoutDate = session('checkout_date') ? \Carbon\Carbon::parse(session('checkout_date')) : null;
            $bookingDate = $event->book_date ? \Carbon\Carbon::parse($event->book_date) : null;
            $isUnavailable = false;
            if ($checkinDate && $checkoutDate && $bookingDate) {
                $isUnavailable = $bookingDate->between($checkinDate, $checkoutDate);
            }
        @endphp

        @if($isUnavailable)
            <div class="text-danger mt-2">
                Unavailable
            </div>
        @endif
    </div>
@endforeach


@endif

</div>

                    @endif
                    <div class="event-button">
                      @if (Auth::guard('customer')->check())
                        @php $checkWishList = checkWishList($event->id, Auth::guard('customer')->user()->id); @endphp
                      @else
                        @php $checkWishList = false; @endphp
                      @endif
                      <!-- <a href="{{ $checkWishList ? route('remove.wishlist', $event->id) : route('addto.wishlist', $event->id) }}"
                        class="availability-btn wishlist-btn {{ $checkWishList ? 'bg-success' : '' }}">
                        <i class="far fa-bookmark"></i> {{ $checkWishList ? __('Remove from Wishlist') : __('Add to Wishlist') }}
                      </a> -->
                     

                         @php
                            // Parse event booking date if it exists
                            $eventBookingDate = $event->book_date ? \Carbon\Carbon::parse($event->book_date) : null;

                            // Parse user-selected check-in and check-out dates if they exist
                            $checkinDate = request('checkin_date') ? \Carbon\Carbon::parse(request('checkin_date')) : null;
                            $checkoutDate = request('checkout_date') ? \Carbon\Carbon::parse(request('checkout_date')) : null;

                            // Initialize the availability check (assuming the event date and user dates are set)
                            $isUnavailable = false;

                            // Check if both booking date and user-selected dates are valid
                            if ($eventBookingDate && $checkinDate && $checkoutDate) {
                                // Check if event's booking date is within the selected range
                                $isUnavailable = $eventBookingDate->between($checkinDate, $checkoutDate);
                            }
                        @endphp  

                        <!-- Availability Button -->
                        <a href="{{ $isUnavailable ? 'javascript:void(0)' : route('event.details', [$event->slug, $event->id]) }}" 
                          class="availability-btn {{ $isUnavailable ? 'bg-danger text-white' : '' }}">
                            {{ $isUnavailable ? __('Unavailable') : __('See Availability') }}
                        </a>
                    

                    </div>
                  </div>
                </div>
              </div>
            @endforeach
            @else
              <div class="col-lg-12">
                  <div class="no-event-wrapper text-center py-5">
                      <h3 class="display-5 text-muted">{{ __('No Event Found') }}</h3>
                      <p class="lead text-secondary">We couldn't find any events at the moment. Please check back later or go back to the homepage.</p>
                      <!-- <a href="{{ url('/') }}" class="btn btn-lg btn-primary mt-3">Go to Home</a> -->
                  </div>
              </div>
          @endif

            <!-- <ul class="pagination flex-wrap pt-10">
              {{ $information['events']->links() }}
            </ul>
            @if (!empty(showAd(3)))
              <div class="text-center mt-4">
                {!! showAd(3) !!}
              </div>
            @endif -->
            <!-- <ul class="pagination flex-wrap pt-10">
                <li class="page-item">
                    <a href="{{ url('/') }}" class="btn btn-primary">Go to Home</a>
                </li>
            </ul> -->

            @if (!empty(showAd(3)))
              <div class="ad-wrapper text-center mt-5">
                  <a href="{{ url('/') }}" class="btn btn-lg btn-primary">Go to Home</a>
              </div>
          @endif
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Event Page End -->

  <form id="filtersForm" class="d-none" action="{{ route('events') }}" method="GET">
    <input type="hidden" id="category-id" name="category"
      value="{{ !empty(request()->input('category')) ? request()->input('category') : '' }}">

    <input type="hidden" id="event" name="event"
      value="{{ !empty(request()->input('event')) ? request()->input('event') : '' }}">

    <input type="hidden" id="min-id" name="min"
      value="{{ !empty(request()->input('min')) ? request()->input('min') : '' }}">

    <input type="hidden" id="max-id" name="max"
      value="{{ !empty(request()->input('max')) ? request()->input('max') : '' }}">

    <input type="hidden" name="search-input"
      value="{{ !empty(request()->input('search-input')) ? request()->input('search-input') : '' }}">
    <input type="hidden" name="location"
      value="{{ !empty(request()->input('location')) ? request()->input('location') : '' }}">

    <input type="hidden" id="dates-id" name="dates"
      value="{{ !empty(request()->input('dates')) ? request()->input('dates') : '' }}">

    <button type="submit" id="submitBtn"></button>
  </form>
@endsection

@section('custom-script')
  <script type="text/javascript" src="{{ asset('assets/front/js/moment.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/front/js/daterangepicker.min.js') }}"></script>

  <script>
    let min_price = {!! htmlspecialchars($information['min']) !!};
    let max_price = {!! htmlspecialchars($information['max']) !!};
    let symbol = "{!! htmlspecialchars($basicInfo->base_currency_symbol) !!}";
    let position = "{!! htmlspecialchars($basicInfo->base_currency_symbol_position) !!}";
    let curr_min = {!! !empty(request()->input('min')) ? htmlspecialchars(request()->input('min')) : 5 !!};
    let curr_max = {!! !empty(request()->input('max')) ? htmlspecialchars(request()->input('max')) : 800 !!};
  </script>

  <script src="{{ asset('assets/front/js/custom_script.js') }}"></script>
@endsection
