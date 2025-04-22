<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Facility;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaypalController extends Controller

{
    /**
     * Disclaimer:
     * The code provided works for the intended purpose,
     * but I am still in the process of fully understanding 
     * its underlying mechanics and structure. I plan to 
     * continue learning and refining my understanding as
     * I work more with this implementation.Added few 
     * logic based with the project so the code my look 
     * confusing beacuse i feel so to but well its working lol.
     *  
     */

    public function payment(Request $request, $id)
    {

        $facility = Facility::findOrFail($id);
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();

        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('payment.success',['facility_id' => $facility->id]),
                "cancel_url" => route('payment.cancel'),
            ],
            "purchase_units" => [
                0 => [
                    "amount" => [
                        # do not hard code currency, remember the bond zig issue
                        "currency_code" => "USD",
                        "value" => $facility->price
                    ]
                ]
            ]
        ]);

        if (isset($response['id']) & $response['id'] != null) {

            foreach ($response['links'] as $links) {
                if ($links['rel'] == 'approve') {
                    return redirect()->away($links['href']);
                }
            }

            return redirect()
                ->route('cancel.payment')
                ->with('error', 'Something went wrong.');
        } else {
            return redirect()
                ->route('create.payment')
                ->with('error', $response['message'] ?? 'Something went wrong.');
        }
    }



    public function cancel()
    {
        return response()->json([
            'message' => 'Cancelled',
        ], Response::HTTP_NO_CONTENT);
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function success(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request['token']);

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {


            $facilityId = $request->query('facility_id');
            $facility = Facility::find($facilityId);

            // if ($facility) {
            //     // Update the the payments table as paid
            //     $facility->status = 'booked';
            //     $facility->save();
            // }

            return response()->json([
                'message' => 'Transaction complete and succesfull.',
                'facility_id' =>  $facility,
            ]);
        } else {
            return response()->json([
                'message' => $response
            ]);
        }
    }
}
