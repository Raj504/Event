<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmation</title>
</head>
<body>
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
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
                <p><strong>Email: <a href="mailto:info@event.apnademand.com">apnademand@gmail.com</a>, <a href="mailto:info@event.apnademand.com">info@event.apnademand.com</a></strong></p>
            </td>
        </tr>
    </table>
    <h1>Booking Confirmation</h1>
    <p>Thank you for booking with us! Here are your booking details:</p>
    <ul>
        <li><strong>Booking ID:</strong> {{ $booking->booking_id }}</li>
        <li><strong>Venue:</strong> {{ $booking->venue->name }}</li>
        <li><strong>Start Date:</strong> {{ $booking->start_date }}</li>
        <li><strong>End Date:</strong> {{ $booking->end_date }}</li>
        <li><strong>Amount:</strong> {{ $booking->amount }}</li>
    </ul>
    <p>We look forward to serving you!</p>
</body>
</html>
