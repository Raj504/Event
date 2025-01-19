@extends('frontend.layout')
@section('pageHeading')
  @if (!empty($pageHeading))
    {{ $pageHeading->customer_booking_page_title ?? __('Event Bookings') }}
  @else
    {{ __('Event Bookings') }}
  @endif
@endsection
@section('hero-section')

<style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px auto;
            font-family: Arial, sans-serif;
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:hover {background-color: #f5f5f5;}
        .total {
            font-weight: bold;
            text-align: right;
        }
        .payment-status {
            color: green;
            font-weight: bold;
        }
        .go-back {
            color: orange;
            text-decoration: none;
            font-weight: bold;
            float: right;
            margin: 10px;
        }
    </style>




<section>
    <div class="container">
        <div>
            <h2><b>Bookings details</b></h2>
        </div>
        <table>
        
            <tr>
                <th>BOOKING ID</th>
                <td>{{$booking->booking_id ?? 'none'}}</td>
            </tr>
            <tr>
                <th>Venue Name</th>
                <td>{{$booking->venue->name ?? 'none'}}</td>
            </tr>
            <tr>
                <th>Venue Description</th>
                <td>{{$booking->description ?? 'none'}}</td>
            </tr>
           <tr>
              <th>Veg Guest</th>
              <td>{{ is_array($booking->guests) ? $booking->guests['veg_guests'] ?? 'none' : $booking->guests }}</td>
              </tr>
             <tr>
              <th>Non-Veg Guest</th>
              <td>{{ is_array($booking->guests) ? $booking->guests['non_veg_guests'] ?? 'none' : $booking->guests }}</td>
             </tr>

                <th>Amount</th>
                <td>{{$booking->amount ?? 'none'}}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>{{$booking->status ?? "none"}}</td>
            </tr>
            <tr>
               
            </tr>
           
        </table>
    </div>
</section>
@endsection