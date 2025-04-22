<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Booking;
use Paynow\Payments\Paynow;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Test\Constraint\ResponseIsSuccessful;

class PayNowController extends Controller
{
    public function payment($id)
    {
        $timeout = 10;
        $retries = 0;
        $sleep_time = 10;
        $booking = Booking::with('facility')->find($id);
        $amount = $booking->facility->price;
        $reservationno = 'Reservation Number #'.$id;
        require_once base_path('vendor/paynow/php-sdk/autoloader.php');



        $paynow = new Paynow(
            env('INTEGRATION_ID'),
            env('INTEGRATION_KEY'),
            'http://example.com/gateways/paynow/update',

            // The return url can be set at later stages. You might want to do this if you want to pass data to the return url (like the reference of the transaction)
            'http://example.com/return?gateway=paynow'
        );

        # $paynow->setResultUrl('');
        # $paynow->setReturnUrl('');

        $payment = $paynow->createPayment($reservationno, 'kudam775@gmail.com');

        $payment->add('Payment made for a facility reservation', $amount);
        $response = $paynow->sendMobile($payment, '0777696355', 'ecocash');
        // $response = $paynow->send($payment);


        if ($response->success()) {
            // Or if you prefer more control, get the link to redirect the user to, then use it as you see fit
            $link = $response->redirectUrl();
            $pollUrl = $response->pollUrl();

            while ($retries < $timeout) {
                time().sleep($sleep_time);
                $status = $paynow->pollTransaction($pollUrl);


                if($status->paid()) {
                    return response()->json([
                        'message' => 'Transaction Was Succesfull'
                    ],Response::HTTP_OK);
                } else {
                    return response()->json([
                        'message' => 'Transaction Fail please try again',
                        'status' => $pollUrl
                    ],Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            }$retries++;


            // Check the status of the transaction
            $status = $paynow->pollTransaction($pollUrl);
        }
    }
}
