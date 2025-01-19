@extends('frontend.layout')
@section('content')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<section class="page-banner overlay pt-120 pb-125 rpt-90 rpb-95 lazy"
  data-bg="https://event.apnademand.com/assets/admin/img/62d5204681dc2.jpg">
  <div class="container">
    <div class="banner-inner">
      <h2 class="page-title">
        Our Venue
      </h2>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="https://event.apnademand.com">Home</a></li>
          <li class="breadcrumb-item active">
            Our Venues
          </li>
        </ol>
      </nav>
    </div>
  </div>
</section>
<!-- Page Banner End -->

<!-- Event Page Start -->
<section class="event-page-section py-120 rpy-100">
  <div class="container container-custom">
    <div class="row">
      <div class="col-lg-3">
        <div class="sidebar rmb-75">
          <div class="widget widget-search">
            <form action="{{ route('venues.search') }}" method="get" >

              <input type="text" name="slug" placeholder="Search.....">

              <button type="submit" class="fa fa-search event-search-button"></button>
            </form>
          </div>

          <div class="widget widget-dropdown">
            <div class="form-group">
              <label for="">Filter by Date</label><br>
            
              <form action="{{route('venues.search.date')}}" method="post">
                @csrf
              <label>start date</label>
              <input type="date" placeholder="Start/End Date" name="startDate" required>
              <label>end date</label>
              <input type="date" name="endDate" required />
              <br> <br>
              <button type="submit" class="event-search-button theme-btn">Search</button>
              </form>


            </div>
          </div>

          <div class="widget widget-search">
            <form action="https://event.apnademand.com/events">




              <input type="text" name="location" value="" placeholder="Enter Location">



              <button type="submit" class="fa fa-search  event-search-button"></button>
            </form>
          </div>
          <div class="widget widget-cagegory">
            <h5 class="widget-title">Category</h5>
            <form action="https://event.apnademand.com/events" id="catForm">

              <select id="category" name="category" class="widget-select">
                <option disabled="">Select a Category</option>
                <option value="">All</option>
                <option value="wedding">Wedding</option>
                <option value="business">Business</option>
                <option value="career">Career</option>
                <option value="conference">Conference</option>
                <option value="wadding">Wadding</option>
              </select>






            </form>
          </div>
          <div class="widget widget-location">
            <h5 class="widget-title">Events</h5>
            <div class="widget-radio">
              <div class="custom-control custom-radio">
                <input type="radio" class="custom-control-input" value="online" name="event" id="radio1">
                <label class="custom-control-label" for="radio1">Online Events</label>
              </div>
              <div class="custom-control custom-radio">
                <input type="radio" class="custom-control-input" value="venue" name="event" id="radio2">
                <label class="custom-control-label" for="radio2">Venue Events</label>
              </div>
            </div>
          </div>


          <div class="widget price-filter-widget">
            <h5 class="widget-title">Price Filter</h5>
            <div class="price-slider-range" id="range-slider"></div>
            <div class="price-btn">
              <input type="text" dir="ltr" id="price" value="" readonly="">
              <button class="theme-btn" id="slider_submit">Price Filter</button>
            </div>
          </div>
          <div class="text-center mt-4">
            <a href="https://www.getrentequip.com/" target="_blank" onclick="adView(11)">
              <img data-src="https://event.apnademand.com/assets/admin/img/advertisements/64577e5f10164.png"
                src="images/64577e5f10164.png" class="lazy" alt="advertisement"
                style="width: 300px; max-height: 600px;max-width: 100%;">
            </a>
          </div>
        </div>
      </div>
      <div class="col-lg-9">
        <div class="event-page-content">
          <div class="row">
            @foreach($venueData as $data)

            
            <div class="col-sm-6 col-xl-4">
              <div class="event-item">
                <div class="event-image">
                  <a href="{{url('venue/details/'.$data['venue']->id)}}">
                  <img class="lazy"
                    data-src="{{ isset($data['first_image']) && !empty($data['first_image']) ? asset('venue_images/' . $data['first_image']) : asset('default-image.jpg') }}"
                    alt="Event">

                  </a>
                </div>
                <div class="event-content">
                  <ul class="time-info" dir="ltr">
                    <li>
                      <i class="far fa-calendar-alt"></i>
                      <span>
                        17 Jan
                      </span>
                    </li>

                    <li>
                      <i class="far fa-hourglass"></i>
                      <span title="Event Duration">
                        12h
                      </span>
                    </li>
                    <li>
                      <i class="far fa-clock"></i>
                      <span>
                        10:00 AM
                      </span>
                    </li>
                  </ul>
                  <a href="https://event.apnademand.com/organizer/details/23/organizer"
                    class="organizer">By�&nbsp;�&nbsp;organizer</a>
                  <h5>
                    <a href="https://event.apnademand.com/event/designer-carrier-conference/105">
                    @if($data['venue'])
                        {{ $data['venue']->name }}
                    @else
                        <p>Venue not found</p>
                    @endif                    </a>
                  </h5>

                  <p class="event-description">
                      @if(isset($data['venue']) && $data['venue'])
                          {{ $data['venue']->description }}
                      @else
                          Description not available
                      @endif
                  </p>
                

                  <div class="price-remain">
                    <div class="location">
                      <i class="fas fa-map-marker-alt"></i>
                      <span>
                      
                        @if(isset($data['venue']) && $data['venue'])
                            {{ $data['venue']->location }}
                        @else
                            Location not available
                        @endif


                      </span>
                    </div>
                    <span>
                      <span class="price">
                      @if(isset($data['venue']) && $data['venue'])
                          {{ $data['venue']->price }}
                      @else
                          Price not available
                      @endif<strong>*</strong>
                      </span>
                    </span>
                  </div>
                </div>
                <a href="https://event.apnademand.com/addto/wishlist/105" class="wishlist-btn ">
                  <i class="far fa-bookmark"></i>
                </a>
              </div>
            </div>
            @endforeach
            {{-- <div class="col-sm-6 col-xl-4">
              <div class="event-item">
                <div class="event-image">
                  <a href="https://event.apnademand.com/event/grand-night-party/103">
                    <img class="lazy"
                      data-src="https://event.apnademand.com/assets/admin/img/event/thumbnail/1683438918.png"
                      alt="Event">
                  </a>
                </div>
                <div class="event-content">
                  <ul class="time-info" dir="ltr">
                    <li>
                      <i class="far fa-calendar-alt"></i>
                      <span>
                        27 Jan
                      </span>
                    </li>

                    <li>
                      <i class="far fa-hourglass"></i>
                      <span title="Event Duration">
                        2h 11m
                      </span>
                    </li>
                    <li>
                      <i class="far fa-clock"></i>
                      <span>
                        09:00 AM
                      </span>
                    </li>
                  </ul>
                  <h5>
                    <a href="https://event.apnademand.com/event/grand-night-party/103">
                      Grand night party
                    </a>
                  </h5>

                  <p class="event-description">Lorem ipsum is a pseudo-Latin text used in web design, typography,
                    layout, and printing in place of ....
                  </p>

                  <div class="price-remain">
                    <div class="location">
                      <i class="fas fa-map-marker-alt"></i>
                      <span>
                        Wilton
                        , United States
                      </span>
                    </div>
                    <span>
                      <span class="price" dir="ltr">



                        ₹95
                        <span>
                          <del>
                            ₹100
                          </del>
                        </span>
                        <strong>*</strong>
                      </span>
                    </span>
                  </div>
                </div>
                <a href="https://event.apnademand.com/addto/wishlist/103" class="wishlist-btn ">
                  <i class="far fa-bookmark"></i>
                </a>
              </div>
            </div>
            <div class="col-sm-6 col-xl-4">
              <div class="event-item">
                <div class="event-image">
                  <a href="https://event.apnademand.com/event/sports-grand-opening/102">
                    <img class="lazy"
                      data-src="https://event.apnademand.com/assets/admin/img/event/thumbnail/1683437890.png"
                      alt="Event">
                  </a>
                </div>
                <div class="event-content">
                  <ul class="time-info" dir="ltr">
                    <li>
                      <i class="far fa-calendar-alt"></i>
                      <span>
                        25 Jan
                      </span>
                    </li>

                    <li>
                      <i class="far fa-hourglass"></i>
                      <span title="Event Duration">
                        2h
                      </span>
                    </li>
                    <li>
                      <i class="far fa-clock"></i>
                      <span>
                        10:00 AM
                      </span>
                    </li>
                  </ul>
                  <a href="https://event.apnademand.com/organizer/details/25/ambrose"
                    class="organizer">By�&nbsp;�&nbsp;ambrose</a>
                  <h5>
                    <a href="https://event.apnademand.com/event/sports-grand-opening/102">
                      Sports grand opening
                    </a>
                  </h5>

                  <p class="event-description">Lorem ipsum is a pseudo-Latin text used in web design, typography,
                    layout, and printing in place of ....
                  </p>

                  <div class="price-remain">
                    <div class="location">
                      <i class="fas fa-map-marker-alt"></i>
                      <span>
                        Wayland
                        , United States
                      </span>
                    </div>
                    <span>
                      <span class="price" dir="ltr">
                        ₹20
                        <strong></strong>
                      </span>
                    </span>
                  </div>
                </div>
                <a href="https://event.apnademand.com/addto/wishlist/102" class="wishlist-btn ">
                  <i class="far fa-bookmark"></i>
                </a>
              </div>
            </div>
            <div class="col-sm-6 col-xl-4">
              <div class="event-item">
                <div class="event-image">
                  <a href="https://event.apnademand.com/event/motivation-for-online-business/101">
                    <img class="lazy"
                      data-src="https://event.apnademand.com/assets/admin/img/event/thumbnail/1683436339.png"
                      alt="Event">
                  </a>
                </div>
                <div class="event-content">
                  <ul class="time-info" dir="ltr">
                    <li>
                      <i class="far fa-calendar-alt"></i>
                      <span>
                        30 Jan
                      </span>
                    </li>

                    <li>
                      <i class="far fa-hourglass"></i>
                      <span title="Event Duration">
                        5h 58m
                      </span>
                    </li>
                    <li>
                      <i class="far fa-clock"></i>
                      <span>
                        11:00 AM
                      </span>
                    </li>
                  </ul>
                  <a href="https://event.apnademand.com/organizer/details/23/organizer"
                    class="organizer">By�&nbsp;�&nbsp;organizer</a>
                  <h5>
                    <a href="https://event.apnademand.com/event/motivation-for-online-business/101">
                      Motivation for online business
                    </a>
                  </h5>

                  <p class="event-description">Lorem ipsum is a pseudo-Latin text used in web design, typography,
                    layout, and printing in place of ....
                  </p>

                  <div class="price-remain">
                    <div class="location">
                      <i class="fas fa-map-marker-alt"></i>
                      <span>Online</span>
                    </div>
                    <span>
                      <span class="price">Free</span>
                    </span>
                  </div>
                </div>
                <a href="https://event.apnademand.com/addto/wishlist/101" class="wishlist-btn ">
                  <i class="far fa-bookmark"></i>
                </a>
              </div>
            </div>
            <div class="col-sm-6 col-xl-4">
              <div class="event-item">
                <div class="event-image">
                  <a href="https://event.apnademand.com/event/player-draft-2023/100">
                    <img class="lazy"
                      data-src="https://event.apnademand.com/assets/admin/img/event/thumbnail/1683373446.png"
                      alt="Event">
                  </a>
                </div>
                <div class="event-content">
                  <ul class="time-info" dir="ltr">
                    <li>
                      <i class="far fa-calendar-alt"></i>
                      <span>
                        25 Feb
                      </span>
                    </li>

                    <li>
                      <i class="far fa-hourglass"></i>
                      <span title="Event Duration">
                        18h 20m
                      </span>
                    </li>
                    <li>
                      <i class="far fa-clock"></i>
                      <span>
                        05:00 PM
                      </span>
                    </li>
                  </ul>
                  <a href="https://event.apnademand.com/organizer/details/23/organizer"
                    class="organizer">By�&nbsp;�&nbsp;organizer</a>
                  <h5>
                    <a href="https://event.apnademand.com/event/player-draft-2023/100">
                      Player draft 2023
                    </a>
                  </h5>

                  <p class="event-description">Lorem Ipsum is simply dummy text of the printing and typesetting
                    industry. Lorem Ipsum has been the ....
                  </p>

                  <div class="price-remain">
                    <div class="location">
                      <i class="fas fa-map-marker-alt"></i>
                      <span>
                        Lumberton
                        , United States
                      </span>
                    </div>
                    <span>
                      <span class="price">
                        Free
                        <strong></strong>
                      </span>
                    </span>
                  </div>
                </div>
                <a href="https://event.apnademand.com/addto/wishlist/100" class="wishlist-btn ">
                  <i class="far fa-bookmark"></i>
                </a>
              </div>
            </div>
            <div class="col-sm-6 col-xl-4">
              <div class="event-item">
                <div class="event-image">
                  <a href="https://event.apnademand.com/event/journalist-conference/94">
                    <img class="lazy"
                      data-src="https://event.apnademand.com/assets/admin/img/event/thumbnail/1683372521.png"
                      alt="Event">
                  </a>
                </div>
                <div class="event-content">
                  <ul class="time-info" dir="ltr">
                    <li>
                      <i class="far fa-calendar-alt"></i>
                      <span>
                        18 Jan
                      </span>
                    </li>

                    <li>
                      <i class="far fa-hourglass"></i>
                      <span title="Event Duration">
                        2d 3h
                      </span>
                    </li>
                    <li>
                      <i class="far fa-clock"></i>
                      <span>
                        04:07 PM
                      </span>
                    </li>
                  </ul>
                  <a href="https://event.apnademand.com/organizer/details/23/organizer"
                    class="organizer">By�&nbsp;�&nbsp;organizer</a>
                  <h5>
                    <a href="https://event.apnademand.com/event/journalist-conference/94">
                      Journalist Conference
                    </a>
                  </h5>

                  <p class="event-description">Lorem Ipsum is simply dummy text of the printing and typesetting
                    industry. Lorem Ipsum has been the ....
                  </p>

                  <div class="price-remain">
                    <div class="location">
                      <i class="fas fa-map-marker-alt"></i>
                      <span>
                        Sydney
                        , Australia
                      </span>
                    </div>
                    <span>
                      <span class="price" dir="ltr">
                        ₹20
                        <strong></strong>
                      </span>
                    </span>
                  </div>
                </div>
                <a href="https://event.apnademand.com/addto/wishlist/94" class="wishlist-btn ">
                  <i class="far fa-bookmark"></i>
                </a>
              </div>
            </div>
            <div class="col-sm-6 col-xl-4">
              <div class="event-item">
                <div class="event-image">
                  <a href="https://event.apnademand.com/event/design-research-by-australia/93">
                    <img class="lazy"
                      data-src="https://event.apnademand.com/assets/admin/img/event/thumbnail/1683371808.png"
                      alt="Event">
                  </a>
                </div>
                <div class="event-content">
                  <ul class="time-info" dir="ltr">
                    <li>
                      <i class="far fa-calendar-alt"></i>
                      <span>
                        20 Jan
                      </span>
                    </li>

                    <li>
                      <i class="far fa-hourglass"></i>
                      <span title="Event Duration">
                        2d
                      </span>
                    </li>
                    <li>
                      <i class="far fa-clock"></i>
                      <span>
                        05:00 PM
                      </span>
                    </li>
                  </ul>
                  <h5>
                    <a href="https://event.apnademand.com/event/design-research-by-australia/93">
                      Design Research by Australia
                    </a>
                  </h5>

                  <p class="event-description">Lorem Ipsum is simply dummy text of the printing and typesetting
                    industry. Lorem Ipsum has been the ....
                  </p>

                  <div class="price-remain">
                    <div class="location">
                      <i class="fas fa-map-marker-alt"></i>
                      <span>
                        Brisbane
                        , Brisbane
                      </span>
                    </div>
                    <span>
                      <span class="price" dir="ltr">
                        <span class="price">
                          ₹18
                          <span><del>
                              ₹20
                            </del></span>
                          <strong></strong>
                        </span>
                      </span>
                    </span>
                  </div>
                </div>
                <a href="https://event.apnademand.com/addto/wishlist/93" class="wishlist-btn ">
                  <i class="far fa-bookmark"></i>
                </a>
              </div>
            </div>
            <div class="col-sm-6 col-xl-4">
              <div class="event-item">
                <div class="event-image">
                  <a href="https://event.apnademand.com/event/small-business-ideas/92">
                    <img class="lazy"
                      data-src="https://event.apnademand.com/assets/admin/img/event/thumbnail/1683370978.png"
                      alt="Event">
                  </a>
                </div>
                <div class="event-content">
                  <ul class="time-info" dir="ltr">
                    <li>
                      <i class="far fa-calendar-alt"></i>
                      <span>
                        15 Jan
                      </span>
                    </li>

                    <li>
                      <i class="far fa-hourglass"></i>
                      <span title="Event Duration">
                        3d
                      </span>
                    </li>
                    <li>
                      <i class="far fa-clock"></i>
                      <span>
                        04:00 PM
                      </span>
                    </li>
                  </ul>
                  <a href="https://event.apnademand.com/organizer/details/23/organizer"
                    class="organizer">By�&nbsp;�&nbsp;organizer</a>
                  <h5>
                    <a href="https://event.apnademand.com/event/small-business-ideas/92">
                      Small Business Ideas
                    </a>
                  </h5>

                  <p class="event-description">Lorem Ipsum is simply dummy text of the printing and typesetting
                    industry. Lorem Ipsum has been the ....
                  </p>

                  <div class="price-remain">
                    <div class="location">
                      <i class="fas fa-map-marker-alt"></i>
                      <span>Online</span>
                    </div>
                    <span>
                      <span class="price" dir="ltr">

                        ₹100

                      </span>
                    </span>
                  </div>
                </div>
                <a href="https://event.apnademand.com/addto/wishlist/92" class="wishlist-btn ">
                  <i class="far fa-bookmark"></i>
                </a>
              </div>
            </div>
            <div class="col-sm-6 col-xl-4">
              <div class="event-item">
                <div class="event-image">
                  <a href="https://event.apnademand.com/event/decoration-of-the-marriage/91">
                    <img class="lazy"
                      data-src="https://event.apnademand.com/assets/admin/img/event/thumbnail/1683370360.png"
                      alt="Event">
                  </a>
                </div>
                <div class="event-content">
                  <ul class="time-info" dir="ltr">
                    <li>
                      <i class="far fa-calendar-alt"></i>
                      <span>
                        18 Jan
                      </span>
                    </li>

                    <li>
                      <i class="far fa-hourglass"></i>
                      <span title="Event Duration">
                        2d 1m
                      </span>
                    </li>
                    <li>
                      <i class="far fa-clock"></i>
                      <span>
                        04:00 PM
                      </span>
                    </li>
                  </ul>
                  <a href="https://event.apnademand.com/organizer/details/25/ambrose"
                    class="organizer">By�&nbsp;�&nbsp;ambrose</a>
                  <h5>
                    <a href="https://event.apnademand.com/event/decoration-of-the-marriage/91">
                      Decoration of the marriage
                    </a>
                  </h5>

                  <p class="event-description">Lorem Ipsum is simply dummy text of the printing and typesetting
                    industry. Lorem Ipsum has been the ....
                  </p>

                  <div class="price-remain">
                    <div class="location">
                      <i class="fas fa-map-marker-alt"></i>
                      <span>
                        Nyora
                        , Australia
                      </span>
                    </div>
                    <span>
                      <span class="price" dir="ltr">


                        ₹90
                        <strong></strong>
                      </span>
                    </span>
                  </div>
                </div>
                <a href="https://event.apnademand.com/addto/wishlist/91" class="wishlist-btn ">
                  <i class="far fa-bookmark"></i>
                </a>
              </div>
            </div> --}}
          </div>
          <ul class="pagination flex-wrap pt-10">

          </ul>
          <div class="text-center mt-4">
            <a href="http://example.com/" target="_blank" onclick="adView(14)">
              <img data-src="https://event.apnademand.com/assets/admin/img/advertisements/64577dfdeb19e.png"
                src="images/64577dfdeb19e.png" class="lazy" alt="advertisement"
                style="width: 728px; max-height: 90px;max-width: 100%;">
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- Event Page End -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
  $(document).ready(function() {
      $('#daterange').daterangepicker({
          opens: 'right', // options are 'left', 'center', 'right', and 'auto'
          autoUpdateInput: false,
          locale: {
              cancelLabel: 'Clear',
              format: 'YYYY-MM-DD'
          }
      }, function(start, end, label) {
          console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
      });

      $('#daterange').on('apply.daterangepicker', function(ev, picker) {
          $(this).val(picker.startDate.format('YYYY-MM-DD') + ' / ' + picker.endDate.format('YYYY-MM-DD'));
      });

      $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
          $(this).val('');
      });
  });
</script>


<form id="filtersForm" class="d-none" action="https://event.apnademand.com/events" method="GET">
  <input type="hidden" id="category-id" name="category" value="">

  <input type="hidden" id="event" name="event" value="">

  <input type="hidden" id="min-id" name="min" value="">

  <input type="hidden" id="max-id" name="max" value="">

  <input type="hidden" name="search-input" value="">
  <input type="hidden" name="location" value="">

  <input type="hidden" id="dates-id" name="dates" value="">

  <button type="submit" id="submitBtn"></button>
</form>
@endsection