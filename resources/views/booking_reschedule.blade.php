<!DOCTYPE html>
<html>
<head>
</head>
<body>
    <div>Dear Customer,</div>
    <br/>
    <div>We hope this message finds you well. For some reason, your scheduled move order for {{ $mailData['booking_exact_date']}} has been rescheduled for the following day, {{ $mailData['booking_reschedule_date']}}.</div>
    <br/>
    <div>Our driver will be in touch with you to discuss the best timing for the rescheduled move order.</div>
    <br/>
    <div>Please note that move order can only be rescheduled once. If this rescheduled time is missed, the move order will automatically be cancelled.</div>
    <br/>
    <br/>
    <div>Best regards,</div>
    <br/>
    <div>Pack&Go “</div>
</body>
</html>