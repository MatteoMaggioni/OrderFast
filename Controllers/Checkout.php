<?php

namespace App\Controllers;

use App\Models\PaymentMethodsRestaurantsModel;
use App\Models\OrdersModel;
use App\Models\OrdersProductsModel;
use App\Models\OrdersAddonsModel;
use App\Models\TablesModel;
use App\Models\RestaurantsModel;
use App\Models\ProductsModel;
use App\Models\AddonsModel;
use App\Controllers\BaseController;
use Exception;
use Stripe;

class Checkout extends BaseController
{
    public function checkout()
    {
        $paymentMethodsRestaurantModel = new PaymentMethodsRestaurantsModel();
        $tablesModel = new TablesModel();
        $restaurantsModel = new RestaurantsModel();

        $pMRm = $paymentMethodsRestaurantModel->select('payment_methods.*')->join('payment_methods', 'payment_method_id = payment_methods.id')->where('restaurant_id', session()->get('restaurant_id'))->find();
        $data['payment_methods'] = $pMRm;

        $table = $tablesModel->where('id', session()->get('table_id'))->first();
        $data['table_name'] = $table['name'];

        $data['stripe_key'] = $restaurantsModel->where('id', (int)session()->get('restaurant_id'))->first()['stripe_key'];

        // print_r(session()->get('table_id'));

        echo view('templates/header', $data);
        echo view('frontend/checkout');
        echo view('templates/footer');
        return;
    }

    public function delete_cart_product($cart_product_id)
    {

        $i = 0;
        $cart = session()->get('cart');
        $newCart = [];
        foreach ($cart as &$cart_product) {
            if ($cart_product['cart_product_id'] == $cart_product_id) {
                if ($cart_product['order_quantity'] > 1) {
                    $cart_product['order_quantity']--;
                    $newCart[] = $cart_product;
                }
            } else {
                $newCart[] = $cart_product;
            }
        }

        $totalPrice = 0;
        foreach ($newCart as $cart_product) {
            $totalPrice += $cart_product['order_quantity'] * $cart_product['product']['price'];
            foreach ($cart_product['addons'] as $addon) {
                $totalPrice += $cart_product['order_quantity'] * $addon['price'];
            }
        }

        $data = [
            'cart' => $newCart,
            'totalPrice' => $totalPrice,
        ];

        session()->set($data);

        return redirect()->to(site_url('/checkout'));
    }

    public function compra_ora($payment_method_slug)
    {
        /* Gestione primo ordine */
        $restaurant_id = (int)session()->get('restaurant_id');
        $cart = session()->get('cart');
        $table_id = (int)session()->get('table_id');
        $totalPrice = session()->get('totalPrice');

        $ordersModel = new OrdersModel();
        $ordersProductsModel = new OrdersProductsModel();
        $ordersAddonsModel = new OrdersAddonsModel();
        $tablesModel = new TablesModel();
        $productsModel = new ProductsModel();
        $addonsModel = new AddonsModel();

        $table = $tablesModel->where('id', $table_id)->first();
        //controllo salvataggi corretti con trowback

        $order = [
            'total_price' => $totalPrice,
            'table_id' => $table_id,
            'restaurant_id' => $restaurant_id,
            'payment_method_slug' => $payment_method_slug,
            'table_name' => $table['name']
        ];


        switch ($payment_method_slug) {
            case 'stripe':
                $order['status_id'] = 1;
                $minus_quantity = true;
                break;
            default: // contanti - da pagare
                $order['status_id'] = 4;
                $minus_quantity = false;
                session()->setFlashdata("message", 'Abbiamo appena avvisato del tuo ordine. Per favore raccogli i soldi che sta per arrivare il personale.');

        }

        $order_id = $ordersModel->insert($order);

        foreach ($cart as $product) {
            if ($product['addons']) {
                $has_addons = 1;
            } else {
                $has_addons = 0;
            }

            $orderProductPrice = $product['product']['price'] * $product['order_quantity'];
            if ($has_addons) {
                foreach ($product['addons'] as $addon) {
                    try {
                        $orderProductPrice += $addon['price'] * $product['order_quantity'];
                    } catch (Exception $e) {
                    }
                }
            }

            $orderProduct = [
                'order_id' => $order_id,
                'ordered_product_id' => $product['cart_product_id'],
                'quantity' => $product['order_quantity'],
                'has_addons' => $has_addons,
                'price' => $orderProductPrice,
                'product_name' => $product['product']['name'],
                'product_price' => $product['product']['price'],
                'product_id' => $product['product']['id']
            ];

            if ($product['product']['id']) {
                $temp = $productsModel->where('id', $product['product']['id'])->first();
                if ($temp['is_quantified'] && $minus_quantity) {
                    $temp['quantity']--;
                    $productsModel->save($temp);
                }
            }

            $product_id = $ordersProductsModel->insert($orderProduct);
            if ($has_addons) {
                foreach ($product['addons'] as $addon) {
                    $orderAddon = [
                        'order_id' => $order_id,
                        'ordered_product_id' => $product['cart_product_id'],
                        'addon_id' => $addon['id'],
                        'addon_name' => $addon['name'],
                        'addon_price' => $addon['price']
                    ];
                    $tempaddon = $addonsModel->where(['product_id' => $product['product']['id'], 'addon_id' => $addon['id']])->first();
                    if ($tempaddon['is_quantified'] && $minus_quantity) {
                        $temp = $productsModel->where('id', $addon['id'])->first();
                        $temp['quantity']--;
                        $productsModel->save($temp);
                    }
                    $ordersAddonsModel->insert($orderAddon);
                }
            }
        }

        $data = [
            'cart' => $cart,
            'totalPrice' => $totalPrice,
            'order_id' => $order_id,
            'table' => $tablesModel->where('id', $table_id)->first()['name'],
            'table_id' => (string)$table_id,
            'restaurant_id' => (string)$restaurant_id
        ];

        $table['min_price'] = 0;
        $tablesModel->save($table);

        $session['cart'] = null;
        $session['totalPrice'] = null;
        $session['minPrice'] = 0;
        session()->set($session);

        session()->setFlashdata("success", 'Ordine avvenuto correttamente');

        echo view('templates/header', $data);
        echo view('/frontend/reciept');
        echo view('templates/footer');
        return;
    }
}
