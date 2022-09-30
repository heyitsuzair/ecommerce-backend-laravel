<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use stdClass;

class MoneySetupController extends Controller
{
    function stripe(Request $req)
    {

        $stripeData = [];

        // adding delivery charges to stripe data
        array_push($stripeData, [
            "price_data" => [
                "currency" => 'pkr',
                "product_data" => [
                    "name" => "Delivery Charges",
                    "images" => ["https://d1wqzb5bdbcre6.cloudfront.net/2bd6b3caea0ac05c72a61f1b50b7af46bc1c92ae2678ac81588c99bbf0cc3bcb/68747470733a2f2f66696c65732e7374726970652e636f6d2f6c696e6b732f4d44423859574e6a644638785356633465584a43637a685a5958565a656e703466475a735833526c633352664d6c703053455a6d4d6c52526357466d5a454a575645644e4d46687853446b7830303457696232384174"],
                ],
                "unit_amount" => '150' . "00",
            ],
            "description" => 'Delivery Charges',
            "quantity" => "1",
        ]);

        // pushing all incoming data array to stripeData array
        foreach ($req->data as $key => $value) {
            $object = (object)$value;

            $array =   [
                "price_data" => [
                    "currency" => 'pkr',
                    "product_data" => [
                        "name" => $object->title,
                        "images" => ["$object->src"],
                    ],
                    "unit_amount" => $object->price . "00",
                ],
                "description" => $object->title,
                "quantity" => $object->quantity,
            ];
            array_push($stripeData, $array);
        }
        $stripe = new \Stripe\StripeClient(
            env('STRIPE_SECRET')
        );
        $response = $stripe->checkout->sessions->create([
            'success_url' => 'https://kfc-mern-uzair.vercel.app',
            'cancel_url' => 'https://kfc-mern-uzair.vercel.app',
            'line_items' => $stripeData,
            'mode' => 'payment',
        ]);
        return $response;
    }
}