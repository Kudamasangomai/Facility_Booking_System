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
use App\Http\Resources\FacilityResource;
use App\Http\Resources\FacilityCollection;
use App\Http\Requests\StoreFacilityRequest;
use App\Http\Requests\UpdateFacilityRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

class FacilityController extends Controller
{
    /**
     * Display a listing of the Facilities
     * and will be viewed by guests with
     * their availibity status and prices
     * etc
     */
    public function index()
    {
        // return Facility::with("images")->paginate(20);
        $facilities = Facility::with('images')->paginate(20);
        return new FacilityCollection($facilities);
    }

    /**
     * Store a newly created resource in storage.
     * only Authorized user will be able to create
     * a facility i.e admins or any user
     * assigned the role
     */
    public function store(StoreFacilityRequest $request)
    {
        $this->authorize('create', Facility::class);
        $validated = $request->validated();
        $validated['status'] = AvailabilityStatus::Free->value;//to be removed this column

        DB::beginTransaction();
        try {
            $facility = Facility::create($validated + ['user_id' => Auth::id()]);


            if ($request->hasFile('images')) {

                $request->validate([
                    'images.*'  => 'required|mimes:jpeg,png,jpg,gif|max:2048',
                ]);
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
            // if ($facility) {
            $facility->load('images');
            return response()->json([
                'data' => new FacilityResource($facility),
                'message' => 'Facility Succesfully Created',
            ], Response::HTTP_CREATED);
            // }
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
        $facility = Facility::with('bookings', 'images')->findorFail($facility->id);
        return new  FacilityResource($facility);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFacilityRequest $request, Facility $facility)
    {

        $facility = Facility::findorFail($facility->id);
        $this->authorize('update', Facility::class);
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

            $this->authorize('delete', $facility);
            $facility->delete();
            return response()->json([
                'message' => ' Facility Successfully Deleted'
            ], Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Facility Belongs to a Booking. Cannot Be deleted.',
            ], Response::HTTP_FORBIDDEN);
        }
    }
}
