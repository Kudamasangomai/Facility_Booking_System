<?php

namespace App\Http\Controllers\Api\v1;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Booking;
use App\Enum\UserType;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Enum\AvailabilityStatus;
use App\Jobs\ProcessBookingEmail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;
use App\Http\Resources\BookingResource;
use App\Http\Resources\FacilityResource;
use App\Http\Resources\BookingCollection;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Resources\FacilityCollection;
use App\Http\Requests\UpdateBookingRequest;
use App\Http\Requests\SearchAvailableFacilityRequest;
use App\Http\Requests\SearchFacilityAvailabilityRequest;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $bookings = Booking::with('user','facility')->get();
        if ($bookings->isEmpty()) {
            return response()->json([
                'message' => 'No Bookings Found',
            ], Response::HTTP_NO_CONTENT);
        }
        return new BookingCollection($bookings);
    }


    /**
     * Function for booking a facility
     */
    public function store(StoreBookingRequest $request)
    {

        $validated = $request->validated();
        $facility = Facility::findorFail($request->input('id'));



        $booking = new Booking();
        if ($booking->isFacilityBooked($facility->id, $validated['check_in'], $validated['check_out'])) {
            return response()->json([
                'message' => 'Facility is already booked for those dates'
            ], Response::HTTP_CONFLICT);
        }


        $bookfacility = Booking::create(
            $validated + [
                'user_id' => Auth::id(),
                'facility_id' => $facility->id
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
        $this->authorize('view', $booking);
        $booking = Booking::with('user', 'facility')
                    ->findOrFail($booking->id);
        return new BookingResource($booking);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookingRequest $request, $id)
    {
        $booking = Booking::findorFail($id);
        $this->authorize('update', $booking);
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
    public function destroy($id)
    {
        $booking = Booking::findorFail($id);
        $currentDateTime = now();
        $checkInDateTime = Carbon::parse($booking->check_in);
        $hoursUntilCheckIn = $currentDateTime->diffInHours($checkInDateTime, false);

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

    /**
     * The user will search a facility with dates
     * they might want to book to check if the
     * Facility is free or not.
     */

    public function searchfacilityavailability(SearchFacilityAvailabilityRequest $request)
    {
        $validated = $request->validated();
        $checkIn = $validated['check_in'];
        $checkOut = $validated['check_out'];

        try {
            // Get all facilitities
            $facilitities = Facility::all();

            // Filter out facilitities that are booked within the specified date range
            $availableRooms = $facilitities->filter(function ($facility) use ($checkIn, $checkOut) {
                $isBooked = Booking::where('facility_id', $facility->id)
                    ->where(function ($query) use ($checkIn, $checkOut) {
                        $query->whereBetween('check_in', [$checkIn, $checkOut])
                            ->orWhereBetween('check_out', [$checkIn, $checkOut])
                            ->orWhere(function ($q) use ($checkIn, $checkOut) {
                                $q->where('check_in', '<=', $checkIn)
                                    ->where('check_out', '>=', $checkOut);
                            });
                    })->exists();

                return !$isBooked;
            });

            if ($availableRooms->isEmpty()) {
                return response()->json([
                    'message' => 'No rooms are available for the selected dates.',
                    'dates' => "$checkIn - $checkOut",
                ], Response::HTTP_NOT_FOUND);
            }
            $availableRooms->load('images');
            return response()->json([
                'message' => 'Available Facilities found.',
                'total' => $availableRooms->count(),
                'rooms' => new FacilityCollection($availableRooms)
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while searching for available rooms.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
