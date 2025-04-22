<!DOCTYPE html>
<html>
<head>
    <title>Facility Booked</title>
</head>
<body>
    <p>Hi </p>
    <p>Facility Was booked  </p>
    <div class="mb-10">
        <ul>
            {{-- <li>   <p >{{ $facility->facility->name }}</p></li> --}}
            <li>   <p >{{ $facility }}</p></li>
        </ul>
     
    </div>
    <div class="mb-10">
    <p>{{ $booking }}</p>
    </div>

<p> <a href="#">View Booking</a> </p>
   
</body>
</html>