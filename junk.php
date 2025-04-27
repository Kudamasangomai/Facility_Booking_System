// $availableRooms = Facility::whereDoestHaave

        // return new PostCollection(Post::with('user')->where('post', 'like', '%' . $searchword . '%')->paginate(5));

        // try {
        //     // Get all facilitities
        //     $facilitities = Facility::all();

        //     // Filter out facilitities that are booked within the specified date range
        //     $availableRooms = $facilitities->filter(function ($facility) use ($checkIn, $checkOut) {
        //         $isBooked = Booking::where('facility_id', $facility->id)
        //             ->where(function ($query) use ($checkIn, $checkOut) {
        //                 $query->whereBetween('check_in', [$checkIn, $checkOut])
        //                     ->orWhereBetween('check_out', [$checkIn, $checkOut])
        //                     ->orWhere(function ($q) use ($checkIn, $checkOut) {
        //                         $q->where('check_in', '<=', $checkIn)
        //                             ->where('check_out', '>=', $checkOut);
        //                     });
        //             })->exists();

        //         return !$isBooked;
        //     });

        //     if ($availableRooms->isEmpty()) {
        //         return response()->json([
        //             'message' => 'No rooms are available for the selected dates.',
        //             'dates' => "$checkIn - $checkOut",
        //         ], Response::HTTP_NOT_FOUND);
        //     }
        //     $availableRooms->load('images');
        //     return response()->json([
        //         'message' => 'Available Facilities found.',
        //         'total' => $availableRooms->count(),
        //         'rooms' => new FacilityCollection($availableRooms)
        //     ], Response::HTTP_OK);
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'message' => 'An error occurred while searching for available rooms.',
        //     ], Response::HTTP_INTERNAL_SERVER_ERROR);
        // }
