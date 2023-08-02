<?php

namespace App\Controllers;

use App\Models\CategoriesModel;
use App\Models\ProductsModel;
use App\Models\AddonsModel;
use App\Models\TablesModel;
use App\Models\RestaurantsModel;
use Exception;

class Home extends BaseController
{
    public function index($restaurant_id = null, $table_id = null)
    {
        $categoriesModel = new CategoriesModel();
        $productsModel = new ProductsModel();
        $addonsModel = new AddonsModel();
        $tablesModel = new TablesModel();
        $restaurantsModel = new RestaurantsModel();

        $categories = $categoriesModel->where('restaurant_id', $restaurant_id)->where('is_subcategory IS NULL')->orderBy('name')->find();

        $datacategories = [];

        foreach ($categories as $category) {
            if ($category['has_subcategories']) {
                $subcategories = $categoriesModel->where('is_subcategory', $category['id'])->orderBy('name')->find();
                $tempcategories['has_subcategories'] = 1;
                $tempcategories['category'] = $category;
                foreach ($subcategories as $category) {
                    $tempsubcategories[] = $this->create_product_list($category, $productsModel, $addonsModel);
                }
                $tempcategories['subcategories'] = $tempsubcategories;
                $datacategories[] = $tempcategories;
                $tempsubcategories = [];
            } else {
                $temp = $this->create_product_list($category, $productsModel, $addonsModel);
                $datacategories[] = $temp;
            }
        }

        /* try { */
        // per controllare se il tavolo che è stato scelto sia del ristorante
        $table = $tablesModel->where(['id' => $table_id, 'restaurant_id' => $restaurant_id])->first();
        if ($table) {
            $minPrice = $table['min_price'];
            $fixedCost = $table['fixed_cost'];
            session()->set('minPrice', (string)$minPrice);
            if (!session()->get('table_id')) {
                session()->set('table_id', (string)$table['id']);
                unset($_SESSION['error']);
            }
        } else {
            $minPrice = 0;
            $fixedCost = 0;
            session()->set('minPrice', (string)$minPrice);
            if (!session()->get('table_id')) {
                session()->setFlashdata("error", 'Nessun tavolo selezionato! Inquadra un QR valido');
            }
        }
        /*         } catch (Exception $e) {
            //con if else adesso il catch risulta non necessario
            $minPrice = 0;
            $fixedCost = 0;
            session()->set('minPrice', (string)$minPrice);
            if (!session()->get('table_id')) {
                session()->setFlashdata("error", 'Nessun tavolo selezionato');
            }
        } */

        $data = [
            'categories' => $datacategories,
            'minPrice' => $minPrice
        ];

        /* serve il controllo sul fatto che ci sia già il carrello per evitare che venga sovrascritto nel caso di aggiornamento pagina */
        if (!session()->get('cart')) {
            if ($minPrice) {
                // creazione prodotto costo del tavolo
                $product = $productsModel->first();
                foreach ($product as &$el) {
                    $el = null;
                }
                $product['name'] = "Costo del servizio";
                $product['price'] = $fixedCost;
                $product['cart_product_id'] = 1;
                $temp = [
                    'product' => $product,
                    'addons' => [],
                    'order_quantity' => 1,
                ];
                $session['cart'][] = $temp;
                $session['totalPrice'] = $fixedCost;
            } else {
                $session['cart'] = null;
                $session['totalPrice'] = null;
            }
            /* $session['minPrice'] = $minPrice;
            $session['fixedCost'] = $fixedCost; */
            session()->set($session);
        } /* else {
            // modifico il prezzo del costo fisso in caso venisse cambiato in corsa
            if ($minPrice) {
                $cart = session()->get('cart');
                print_r($cart);
                foreach($cart as &$product) {
                    if(strcmp($product['product']['name'], "Costo del servizio") == 0) {
                        $product['product']['price'] = $fixedCost;
                    }
                }
                $session['cart'] = $cart;
                $total = 0;
                foreach ($cart as $product) {
                    $total += $product['product']['price'] * $product['order_quantity'];
                }
                //$session['totalPrice'] = $total;

            } else {
                $session['cart'] = null;
                //$session['totalPrice'] = null;
            }
            session()->set($session);
        } */

        /* gestione id ristorante e logo */
        try {
            if (!session()->get('restaurant_id')) {
                $session['restaurant_id'] = (string)$restaurant_id;
                $session['logo'] = $restaurantsModel->where('id', $restaurant_id)->first()['logo'];
                $session['subscription'] = $restaurantsModel->where('id', $restaurant_id)->first()['subscription'];
                session()->set($session);
            }
        } catch (Exception $e) {
            $session['restaurant_id'] = (string)$restaurant_id;
            $session['logo'] = $restaurantsModel->where('id', $restaurant_id)->first()['logo'];
            $session['subscription'] = $restaurantsModel->where('id', $restaurant_id)->first()['subscription'];
            session()->set($session);
        }

        // session()->remove('cart');
        // session()->remove('totalPrice');
        // print_r(session()->get('restaurant_id'));
        // print_r(session()->get());

        echo view('templates/header', $data);
        echo view('frontend/shop');
        echo view('templates/footer');
        return;
    }

    public function add_to_cart_product($product_id = null)
    {

        $productsModel = new ProductsModel();
        $addonsModel = new AddonsModel();

        // cart = [products], totalPrice // products = product, [addons]
        $cart = session()->get('cart');
        $totalPrice = session('totalPrice');

        if ($product_id) {
            $product = $productsModel->where('id', $product_id)->first();
        } /* else {
            $product_id = $this->request->getPost('product_id');
        } */
        $temp['product'] = $product;

        //$temp['order_product_price'] = $product['price'];

        try {
            $totalPrice += $product['price'];
        } catch (Exception $e) {
            $totalPrice = $product['price'];
        }

        $temp['addons'] = [];

        if ($this->request->getMethod() == 'post') {
            $addonsIdList = $this->request->getVar();
            foreach ($addonsIdList as $addonId) {
                $productAddon = $productsModel->where('id', $addonId)->first();
                $addon = $addonsModel->where(['addon_id' => $addonId, 'product_id' => $product_id])->first();
                if ($addon['price']) {
                    $productAddon['price'] = $addon['price'];
                }
                $temp['addons'][] = $productAddon;
                //$temp['order_product_price'] = $productAddon['price'];
                $totalPrice += $productAddon['price'];
            }
        }

        $is_in = false;
        if ($cart) {
            foreach ($cart as &$product) {
                if ($product['product'] == $temp['product'] && $product['addons'] == $temp['addons']) {
                    $product['order_quantity']++;
                    //$product['order_product_price'] += $temp['order_product_price'];
                    $is_in = true;
                    break;
                }
            }
        }

        if (!$is_in) {
            $temp['order_quantity'] = 1;
            $cart[] = $temp;
        }

        $i = 0;
        foreach ($cart as &$product) {
            $product['cart_product_id'] = $i;
            $i++;
        }

        $data = [
            'cart' => $cart,
            'totalPrice' => $totalPrice,
        ];

        session()->set($data);

        return redirect()->to(site_url('/' . session()->get('restaurant_id') . '/' . session()->get('table_id')));
    }

    private function create_product_list($category, $productsModel, $addonsModel)
    {
        $temp['category'] = $category;
        $temp['products'] = $productsModel->select('products.*')->join('magazzini', 'magazzini.id = products.magazzino_id')->where(['category_id' => $category['id'], 'quantity >' => 0, 'visibility' => 1])->orderBy('name')->find();
        foreach ($temp['products'] as &$product) {
            if ($product['has_addons']) {
                $product['addons'] = $addonsModel->select('products.id, products.name, products.price, addons.price as addonPrice')->join('products', 'addons.addon_id = products.id')->where(['product_id' => $product['id'], 'quantity > 0'])->find();
                foreach ($product['addons'] as &$addon) {
                    if ($addon['addonPrice']) {
                        $addon['price'] = $addon['addonPrice'];
                    }
                }
            } else {
                $product['addons'] = [];
            }
        }
        $temp['has_subcategories'] = 0;
        return $temp;
    }
}
