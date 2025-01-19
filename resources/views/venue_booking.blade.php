<section class="container mt-65" style="margin-top:120px !important; padding:20px">
    <table id="dataTable" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td colspan="2" style="text-align: center;">
                <img style="max-width:300px" src="https://event.apnademand.com/system/web-assets/img/general/apnademand-event-logo.png" alt="Marriage Event" class="event-logo" style="width: 100%; border-radius: 10px; margin-bottom: 20px;">
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;">
                <h1 class="event-title" style="font-size: 36px; color: #333; font-weight: bold;"><strong>Apna Demand Event</strong></h1>
                <p><strong>70, MG Market, Patna City, Bihar – 800008</strong></p>
                <p><strong>Ph: 1234567890; 9876543210;</strong></p>
                <p><strong>Email: <a href="mailto:apnademand@gmail.com">apnademand@gmail.com</a>, <a href="mailto:contact@apnademand.com">contact@apnademand.com</a></strong></p>
            </td>
        </tr>

        <!-- User and Organizer Details Row -->

        <!-- Add a spacer row -->
        <tr>
            <td colspan="2" style="height: 20px;"></td>
        </tr>


        <tr>
            <td class="user-details" style="width: 50%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                <h3 style="margin-bottom: 15px; font-size: 18px; color: #333;">User Details</h3>
                <p style="font-size: 16px; color: #555;"><strong>Name:</strong> {{ $user['first_name'] ?? '' }}</p>
                <p style="font-size: 16px; color: #555;"><strong>Email:</strong> {{ $user['email'] ?? '' }}</p>
                <p style="font-size: 16px; color: #555;"><strong>Phone:</strong> {{ $user['contact_number'] ?? '' }}</p>
                <p style="font-size: 16px; color: #555;"><strong>Address:</strong> {{ $user['address'] ?? '' }}</p>
                <p style="font-size: 16px; color: #555;"><strong>Country & State:</strong> {{ $user['country'] ?? '' }}, {{ $user['state'] ?? '' }} , {{ $user['city'] ?? '' }}</p>
            </td>
            <td class="organizer-details" style="width: 50%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                <h3 style="margin-bottom: 15px; font-size: 18px; color: #333;">Organizer Details</h3>
                <p style="font-size: 16px; color: #555;"><strong>Name:</strong> {{ $organizer['name'] ?? '' }}</p>
                <p style="font-size: 16px; color: #555;"><strong>Phone:</strong> {{ $organizer['phone'] ?? '' }}</p>
                <p style="font-size: 16px; color: #555;"><strong>Email:</strong> {{ $organizer['email'] ?? '' }}</p>
                <p style="font-size: 16px; color: #555;"><strong>Address:</strong> {{ $organizer['address'] ?? '' }}</p>
                <p style="font-size: 16px; color: #555;"><strong>Country & State:</strong> {{ $organizer['country'] ?? '' }}, {{ $organizer['state'] ?? '' }} , {{ $organizer['city'] ?? '' }}</p>
            </td>
        </tr>

        <tr>
            <td colspan="2">
                <hr style="border: 0; border-top: 2px solid #0000; margin: 20px 0;">
                <table class="invoice-table" style="width: 100%; border-collapse: collapse; margin-bottom: 20px; border: 1px solid lightgrey;">
                    <tr>
                        <th style="padding: 12px 20px; text-align: left; background-color: #f7f7f7; color: #333; border: 1px solid lightgrey;">Invoice & Receipt No.</th>
                        <td style="padding: 12px 20px; text-align: left; background-color: #fff; color: #555; border: 1px solid lightgrey;">{{ $venueBooking['booking_id'] }}</td>
                    </tr>
                    <tr>
                        <th style="padding: 12px 20px; text-align: left; background-color: #f7f7f7; color: #333; border: 1px solid lightgrey;">Start Date</th>
                        <td style="padding: 12px 20px; text-align: left; background-color: #fff; color: #555; border: 1px solid lightgrey;">{{ \Carbon\Carbon::parse($venueBooking['start_date'])->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <th style="padding: 12px 20px; text-align: left; background-color: #f7f7f7; color: #333; border: 1px solid lightgrey;">End Date</th>
                        <td style="padding: 12px 20px; text-align: left; background-color: #fff; color: #555; border: 1px solid lightgrey;">{{ \Carbon\Carbon::parse($venueBooking['end_date'])->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <th style="padding: 12px 20px; text-align: left; background-color: #f7f7f7; color: #333; border: 1px solid lightgrey;">Payment Status</th>
                        <td style="padding: 12px 20px; text-align: left; background-color: #fff; color: #555; border: 1px solid lightgrey;">{{ $venueBooking['payment'] }}</td>
                    </tr>
                    <tr>
                        <th style="padding: 12px 20px; text-align: left; background-color: #f7f7f7; color: #333; border: 1px solid lightgrey;">Total Guest</th>
                        <td style="padding: 12px 20px; text-align: left; background-color: #fff; color: #555; border: 1px solid lightgrey;">
                            @if(is_array($guests) || $guests instanceof \Illuminate\Support\Collection)
                                @foreach($guests as $key => $guest)
                                    {{ ucwords(str_replace('_', ' ', $key)) }}: {{ $guest }}<br>
                                @endforeach
                            @elseif(is_object($guests))
                                @foreach($guests->toArray() as $key => $guest)
                                    {{ ucwords(str_replace('_', ' ', $key)) }}: {{ $guest }}<br>
                                @endforeach
                            @else
                                {{ ucwords(str_replace('_', ' ', $guests)) }}
                            @endif
                        </td>
                        
                    </tr>
                    <tr>
                        <th style="padding: 12px 20px; text-align: left; background-color: #f7f7f7; color: #333; border: 1px solid lightgrey;">Total Amount</th>
                        <td style="padding: 12px 20px; text-align: left; background-color: #fff; color: #555; border: 1px solid lightgrey;">{{ $venueBooking['amount'] }}</td>
                    </tr>
                    <tr>
                        <th style="padding: 12px 20px; text-align: left; background-color: #f7f7f7; color: #333; border: 1px solid lightgrey;">Amount Received</th>
                        <td style="padding: 12px 20px; text-align: left; background-color: #fff; color: #555; border: 1px solid lightgrey;">{{ $venueBooking['paid_amount'] }}</td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td colspan="2" class="text-right" style="font-size: 16px; color: #333; font-weight: 500;">
                <p>For Apna Demand Event</p>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;">
                <p><strong>This invoice is computer-generated and does not require a signature.</strong></p>
            </td>
        </tr>

        <tr>
            <td colspan="2" style="text-align: center;">
                <button class="btn-print" style="background-color: #007bff; color: #fff; font-size: 16px; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; transition: background-color 0.3s;" onclick="printInvoice()">Download</button><br><br>
            </td> 
        </tr>
    </table>
</section>

<script>
    // Function to trigger print
    function printInvoice() {
        var printContents = document.getElementById('dataTable').outerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
