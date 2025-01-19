@extends('frontend.layout')
@section('pageHeading')
  @if (!empty($pageHeading))
    {{ $pageHeading->customer_booking_page_title ?? __('Event Bookings') }}
  @else
    {{ __('Event Bookings') }}
  @endif
@endsection
@section('hero-section')
  <!-- Page Banner Start -->
  <section class="page-banner overlay pt-120 pb-125 rpt-90 rpb-95 lazy"
    data-bg="{{ asset('assets/admin/img/' . $basicInfo->breadcrumb) }}">
    <div class="container">
      <div class="banner-inner">
        <h2 class="page-title">
          @if (!empty($pageHeading))
            {{ $pageHeading->customer_booking_page_title ?? __('Event Bookings') }}
          @else
            {{ __('Event Bookings') }}
          @endif
        </h2>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">
                @if (!empty($pageHeading))
                  {{ $pageHeading->customer_dashboard_page_title ?? __('Dashboard') }}
                @else
                  {{ __('Dashboard') }}
                @endif
              </a></li>
            <li class="breadcrumb-item active">
              @if (!empty($pageHeading))
                {{ $pageHeading->customer_booking_page_title ?? __('Event Bookings') }}
              @else
                {{ __('Event Bookings') }}
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
  <!--====== Start Dashboard Section ======-->
  <section class="user-dashbord">
    <div class="container">
      <div class="row">
        @includeIf('frontend.customer.partials.sidebar')
        <div class="col-lg-9">
          <div class="row">
            <div class="col-lg-12">
              <div class="user-profile-details">
                <div class="account-info">
                  <div class="title">
                    <h4>{{ __('Venue Bookings') }}</h4>
                  </div>
                  <div class="main-info">
                    <div class="main-table">
                      <div class="table table-responsive">
                        <table id="example" class="dataTables_wrapper dt-responsive table-striped dt-bootstrap4 w-100">
                          <thead>
                            <tr>
                              <th>{{ __('Booking Id') }}</th>
                              <th>{{ __('Booking Name') }}</th>
                              <th>{{ __('Venue Date') }}</th>
                              <th>{{ __('Venue Title') }}</th>
                         
                            </tr>
                          </thead>
                          <tbody>
                            @foreach ($bookingshow as $item)
                           

                              @if (!empty($item))
                                <tr>
                              
                                  <td>
                                    #{{ $item->booking_id }}
                                  </td>
                                  <td>{{ $item->venue->name ?? __('No Venue') }}</td>
                                  
                                  <td>{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('D, M d') }}
                                  </td>
                                  <td><a href="{{ route('venue.booking_details', $item->id) }}"
                                      class="btn ">{{ __('Details') }}</a></td>
                                 
                                </tr>
                              @endif
                            @endforeach
                          </tbody>
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
  </section>

@endsection
