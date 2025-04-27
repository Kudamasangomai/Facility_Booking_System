<?php

namespace App\Http\Controllers\Api\v1;

use Carbon\Carbon;
use App\Models\Booking;
use App\Models\Facility;
use Illuminate\Http\Response;
use App\Jobs\ProcessBookingEmail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\BookingResource;
use App\Http\Resources\BookingCollection;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;


class BookingController extends Controller
{

    public function __construct() {
        $this->authorizeResource(Booking::class,'booking');
    }

    // Display a listing of the bookings. Only viewed By Admin
    public function index()
    {

        $bookings = Booking::with('user', 'facility')->paginate(20);
        if ($bookings->isEmpty()) {
            return response()->json([
                'message' => 'No Bookings Found',
            ], Response::HTTP_NOT_FOUND);
        }
        return new BookingCollection($bookings);
    }

    // Function for booking a facility public to everyuser
    public function store(StoreBookingRequest $request)
    {

        $validated = $request->validated();
        $facility = Facility::findorFail($validated['facility_id']);


        if (Booking::isFacilityBookeD($validated['facility_id'],$validated['check_in'],$validated['check_out']))
        {
            return response()->json([
                'message' => 'Facility is already booked for those dates'
            ], Response::HTTP_CONFLICT);
        }


        $bookfacility = Booking::create(
            $validated + [
                'user_id' => Auth::id(),
                'facility_id' => $validated['facility_id']
            ]
        );
        ProcessBookingEmail::dispatch($facility, $bookfacility);

        return response()->json([
            'message' => 'Facility Booked  Successfully',
            'data' => new BookingResource($bookfacility),

        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {

        $booking = Booking::with('user', 'facility')->findOrFail($booking->id);
        return new BookingResource($booking);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookingRequest $request, Booking $booking)
    {

        $booking->update($request->validated());
        return response()->json([
            'data' => new BookingResource($booking),
            'message' => 'Booking Record  Successfully Updated',
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage. Destory function will be used to Cancel
     * Reservation by the User. Cancellation should be 48 hours before an event
     */
    public function destroy(Booking $booking)
    {
        $booking = Booking::findorFail($booking->id);
        $currentDateTime = now();
        $checkInDateTime = Carbon::parse($booking->check_in);
        $hoursUntilCheckIn = $currentDateTime->diffInHours($checkInDateTime, false);

        dd($hoursUntilCheckIn);
        if ($hoursUntilCheckIn < 48) {
            return response()->json([
                'message' => 'Cancellation is only allowed at least 48 hours before the event.',
            ], Response::HTTP_FORBIDDEN);
        }
        $booking->delete();
        return response()->json([
            'message' => 'Your Booking Has Been Sussesfully Cancelled'
        ], Response::HTTP_OK);
    }


}
