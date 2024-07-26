<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Payment;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class PaymentController extends Controller
{
    //
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function payEvent(Event $event)
    {
        try {
            //code...
            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorised', 401]);
            }


            $paymentIntent = PaymentIntent::create([
                'amount' => $event->price * 100,
                'currency' => 'xof',
                'payment_method_types' => ['card'],
            ]);

            $payment = Payment::create([
                'user_id' => $user->id,
                'paymentable_type' => Event::class,
                'paymentable_id' => $event->id,
                'stripe_payment_id' => $paymentIntent->id,
                'amount' => $event->price * 100,
                'status' => 'pending',
            ]);

            return response()->json([
                'payment_intent' => $paymentIntent->client_secret,
                'payment' => $payment,
            ], 201);
        } catch (ValidationException $e) {
            //throw $th;
            return response()->json([$e, 422]);
        } catch (Exception $e) {
            //throw $th;
            return response()->json($e);
        }
    }

    public function confirmPayment($payment_id)
    {
        try {
            //code...
            $payment = Payment::find($payment_id);

            if (!$payment) {
                return response()->json(['message' => 'Payment not found'], 404);
            }
            /* $validatedData = $request->validate([
                'payment_id' => 'required|string',
            ]); */


            $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

            //dd($payment->stripe_payment_id);

            $intent = $stripe->paymentIntents->retrieve($payment->stripe_payment_id);


            if ($intent->status == 'succeeded') {
                $payment->update(['status' => 'succeeded']);

                $event = Event::find($payment->paymentable_id);

                $event->users()->attach($payment->user_id);

                //Doit envoyer la facture ou le ticket par <mail></mail>

                return response()->json(['message' => 'Payment succeeded and user registered for the event'], 200);
            } else {
                return response()->json(['message' => 'Payment not confirmed'], 400);
            }
        } catch (Exception $e) {
            //throw $th;
            return response()->json($e);
        }
    }
}
