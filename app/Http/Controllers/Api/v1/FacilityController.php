<?php

namespace App\Http\Controllers\Api\v1;

use Exception;
use App\Models\Image;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Enum\AvailabilityStatus;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\SearchFacilityAvailabilityRequest;
use App\Http\Resources\FacilityResource;
use App\Http\Resources\FacilityCollection;
use App\Http\Requests\StoreFacilityRequest;
use App\Http\Requests\UpdateFacilityRequest;
use App\Models\Booking;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

class FacilityController extends Controller
{

    // for Authorization a isAdmin middleware was used

    // Display a listing of the Facilities
    public function index()
    {
        $facilities = Facility::with('images')->paginate(20);
        return new FacilityCollection($facilities);
    }

    /**
     *  Store a newly created resource in storage. only Authorized user
     *  will be able to create a facility i.e admins or any user
     */
    public function store(StoreFacilityRequest $request)
    {

        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $facility = Facility::create($validated + ['user_id' => Auth::id()]);


            if ($request->hasFile('images')) {

                $request->validate([ 'images.*'  => 'required|mimes:jpeg,png,jpg,gif|max:2048' ]);
                $images = $request->file('images');

                foreach ($images as $image) {

                    $imageName = $image->getClientOriginalName();
                    $imagePath = $image->store('Facility_images', 'public');

                    Image::create([
                        'facility_id' => $facility->id,
                        'name' => $imageName,
                        'path' => $imagePath,
                    ]);
                }
            }
            DB::commit();

            $facility->load('images');

            return response()->json([

                'data' => new FacilityResource($facility),
                'message' => 'Facility Succesfully Created',
            ], Response::HTTP_CREATED);

        } catch (Exception $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Failed to create facility. Error: ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Facility $facility)
    {
        $facility->load('bookings', 'images');
        return new  FacilityResource($facility);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFacilityRequest $request, Facility $facility)
    {


        $facility->update($request->validated());

        return response()->json([
            'data' => new FacilityResource($facility),
            'message' => 'Facility Succesfully Updated',
        ], Response::HTTP_OK);
    }

    public function addfacilityimage(Request $request, Facility $facility)
    {
        $facility = Facility::findorFail($facility->id);
        $request->validate([
            'images.*'  => 'required|mimes:jpeg,png,jpg,gif|max:2048',
        ]);


        if ($request->hasFile('images')) {
            $images = $request->file('images');

            foreach ($images as $image) {

                $imageName = $image->getClientOriginalName();
                $imagePath = $image->store('Facility_images', 'public');

                Image::create([
                    'facility_id' => $facility->id,
                    'name' => $imageName,
                    'path' => $imagePath,
                ]);
            }
        }
        $facility->load('images');
        return response()->json([
            'data' => new FacilityResource($facility),
            'message' => 'Facility Images Succesfully Added',
        ], Response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Facility $facility)
    {
        try {

            $facility->delete();
            return response()->noContent();

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Facility Belongs to a Booking. Cannot Be deleted.',
            ], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * The user will search a facility with dates they might want to book to check if the
     * Facility is free or not.
     */

    public function searchfacilityavailability(SearchFacilityAvailabilityRequest $request)
    {

        $validated = $request->validated();
        $checkIn = $validated['check_in'];
        $checkOut = $validated['check_out'];
    }
}
