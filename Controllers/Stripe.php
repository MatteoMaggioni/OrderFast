<?php

namespace App\Controllers;

require_once '../vendor/autoload.php';

use App\Models\RestaurantsModel;

class Stripe extends BaseController
{
    public function index()
    {

        $restaurantsModel = new RestaurantsModel();
        $secret = $restaurantsModel->where('id', (int)session()->get('restaurant_id'))->first();

        \Stripe\Stripe::setApiKey($secret['stripe_secret']);

        header('Content-Type: application/json');

        try {
            // retrieve JSON from POST body
            $json_str = file_get_contents('php://input');
            $json_obj = json_decode($json_str);

            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => (int)(session()->get('totalPrice') * 100),
                'currency' => 'eur',
                "description" => session()->get('tableName')
            ]);

            $output = [
                'clientSecret' => $paymentIntent->client_secret,
            ];

            echo json_encode($output);
        } catch (Error $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
