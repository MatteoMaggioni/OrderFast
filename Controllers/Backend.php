<?php

namespace App\Controllers;

use App\Models\ProductsModel;
use App\Models\AddonsModel;
use App\Models\TablesModel;
use App\Models\OrdersModel;
use App\Models\OrdersAddonsModel;
use App\Models\OrdersProductsModel;
use App\Models\StatusModel;
use App\Models\CategoriesModel;
use App\Models\RestaurantsModel;
use App\Models\MagazziniModel;
use Exception;
use \Datetime;


class Backend extends BaseController
{
    public function orders()
    {
        $ordersModel = new OrdersModel();
        $ordersAddonsModel = new OrdersAddonsModel();
        $ordersProductsModel = new OrdersProductsModel();
        $productsModel = new ProductsModel();
        $tablesModel = new TablesModel();
        $addonsModel = new AddonsModel();
        $statusModel = new StatusModel();

        $restaurant_id = session()->get('restaurant_id');

        if ($this->request->getMethod() == 'post' /* && $this->request->getPost('orders_form') */) {

            $order_id = $this->request->getVar('order_id');
            $order_status = $this->request->getVar('status_id');
            $temp_order_status['status_id'] = $order_status;
            $order = $ordersModel->where('id', $order_id)->first();
            $ordersModel->update($order_id, $temp_order_status);

            if (($order_status == 2 || $order_status == 3 || $order_status == 4) and ($order['status_id'] == 1 || $order['status_id'] == 5)) {
                $ordered_products = $ordersProductsModel->where('order_id', $order_id)->find();
                foreach ($ordered_products as $product) {
                    if ($product['product_id']) {
                        $temp = $productsModel->where('id', $product['product_id'])->first();
                        if ($temp['is_quantified']) {
                            $temp['quantity'] += $product['quantity'];
                            $productsModel->save($temp);
                        }
                    }
                }
            }

            if (($order['status_id'] == 2 || $order['status_id'] == 3 || $order['status_id'] == 4) and ($order_status == 1 || $order_status == 5)) {
                $ordered_products = $ordersProductsModel->where('order_id', $order_id)->find();
                foreach ($ordered_products as $product) {
                    if ($product['product_id']) {
                        $temp = $productsModel->where('id', $product['product_id'])->first();
                        if ($temp['is_quantified']) {
                            $temp['quantity'] -= $product['quantity'];
                            $productsModel->save($temp);
                        }
                    }
                }
            }
        }

        $orders = $ordersModel->select('orders.*, status.name as status_name, status.id as status_id, payment_methods.name as payment_method_name')->join('status', 'status_id = status.id')->join('payment_methods', 'payment_method_slug = payment_methods.slug')->where(['orders.restaurant_id' => $restaurant_id])->orderBy('created', 'desc')->find();
        foreach ($orders as &$order) {
            $orderedProducts = $ordersProductsModel->where('order_id', $order['id'])->find();
            foreach ($orderedProducts as &$orderedProduct) {
                $orderedAddons = [];
                if ($orderedProduct['has_addons']) {
                    $orderedAddons = $ordersAddonsModel->where(['orders_addons.ordered_product_id' => $orderedProduct['ordered_product_id'], 'orders_addons.order_id' => $order['id']])->find();
                }
                $orderedProduct['addons'] = $orderedAddons;
            }
            $order['orderedProducts'] = $orderedProducts;
        }

        /* $orders = $ordersModel->select('orders.*, status.name as status_name, status.id as status_id, payment_methods.name as payment_method_name')->join('status', 'status_id = status.id')->join('payment_methods', 'payment_method_slug = payment_methods.slug')->where(['orders.restaurant_id' => $restaurant_id])->orderBy('created', 'desc')->find();
        foreach ($orders as &$order) {
            $orderedProducts = $ordersProductsModel->where('order_id', $order['id'])->find();
            foreach ($orderedProducts as &$orderedProduct) {
                $product = $productsModel->where('id', $orderedProduct['product_id'])->first();
                $addons = [];
                if ($orderedProduct['has_addons']) {
                    $orderedAddons = $ordersAddonsModel->where(['orders_addons.product_id' => $orderedProduct['product_id'], 'orders_addons.order_id' => $order['id']])->find();
                    // $orderedAddons = $ordersAddonsModel->join('products', 'products.id = orders_addons.addon_id', 'left')->join('addons', 'addons.addon_id = orders_addons.addon_id', 'left')->where(['orders_addons.product_id' => $orderedProduct['product_id'], 'orders_addons.order_id' => $order['id']])->find();
                    $addonsIdList = [];
                    foreach ($orderedAddons as $orderedAddon) {
                        $addonsIdList[] = $orderedAddon['addon_id'];
                    }
                    $addons = $productsModel->select('products.*, addons.price as addon_price')->join('addons', 'addons.addon_id = products.id')->whereIn('products.id', $addonsIdList)->where('addons.product_id', $orderedProduct['product_id'])->find();
                }
                $orderedProduct['product'] = $product;
                $orderedProduct['addons'] = $addons;
            }
            $order['orderedProducts'] = $orderedProducts;
        } */

        $data['orders'] = $orders;

        $status = $statusModel->find();
        $data['status'] = $status;

        echo view("templates/header", $data);
        echo view('backend/orders');
        echo view("templates/footer");
        return;
    }


    public function magazzino()
    {
        $productsModel = new ProductsModel();
        $categoriesModel = new CategoriesModel();
        $addonsModel = new AddonsModel();
        $magazziniModel = new MagazziniModel();

        if ($this->request->getMethod() == 'post') {

            $product = $this->request->getVar();

            try {
                $product['is_quantified'];
            } catch (Exception $e) {
                $product['quantity'] = 1;
                $product['is_quantified'] = null;
            }

            try {
                $product['is_vegetarian'];
            } catch (Exception $e) {
                $product['is_vegetarian'] = null;
            }

            try {
                $product['is_freezed'];
            } catch (Exception $e) {
                $product['is_freezed'] = null;
            }

            try {
                $product['is_addon'];
            } catch (Exception $e) {
                $product['is_addon'] = null;
            }

            $imgpath = $this->siteConfig->PUBLICPATH . "images/";
            $image = $this->request->getFile('image');
            $oldimage = $this->request->getVar('oldimage');

            if ($image) {
                //print_r("ONE");
                if ($image->isValid()) {

                    //print_r("TWO");
                    $restaurantsModel = new RestaurantsModel();
                    $restaurant = $restaurantsModel->where('id', (int)session()->get('restaurant_id'))->first();
                    $restaurant_folder = (string)$restaurant['id'] . $restaurant['name'];
                    //print_r($image->getName());
                    if (!is_dir($imgpath . $restaurant_folder)) {
                        mkdir($imgpath . $restaurant_folder);
                        mkdir($imgpath . $restaurant_folder . '/products_images');
                        mkdir($imgpath . $restaurant_folder . '/categories_images');
                    }
                    if (file_exists($imgpath . $oldimage) && !is_dir($imgpath . $oldimage)) {
                        unlink($imgpath . $oldimage);
                    }
                    rename($image, $imgpath . $restaurant_folder  . '/products_images/' . $image->getName());
                    $product['image'] = '/images/' . $restaurant_folder  . '/products_images/' . $image->getName();
                }
            }

            if ($this->request->getPost('nuovo')) {
                $product['restaurant_id'] = (int)session()->get('restaurant_id');
                $product_id = $productsModel->insert($product);

                $has_addons = false;
                foreach ($product['addons'] as $addon_id) {
                    if ($addon_id) {
                        $addon = [
                            'addon_id' => $addon_id,
                            'product_id' => $product_id
                        ];
                        // se non esiste già uno con la stessa chiave
                        $addonsModel->save($addon);
                        $has_addons = 1;
                    }
                }

                $product['has_addons'] = $has_addons;
                //print_r($product);
                $product['id'] = $product_id;
                $productsModel->save($product);
            } else {
                $addonsModel->where('product_id', $product['id'])->delete();
                $has_addons = false;
                foreach ($product['addons'] as $addon_id) {
                    if ($addon_id) {
                        $addon = [
                            'addon_id' => $addon_id,
                            'product_id' => $product['id']
                        ];
                        // se non esiste già uno con la stessa chiave
                        $addonsModel->save($addon);
                        $has_addons = 1;
                    }
                }

                $product['has_addons'] = $has_addons;
                //print_r($product);
                $productsModel->save($product);
            }
            //$tempOrder = $ordersModel->where('id', $order_id)->first();
        }

        $data['products'] = $productsModel->where('products.restaurant_id', (int)session()->get('restaurant_id'))->orderBy('name')->find();
        foreach ($data['products'] as &$product) {
            $product['addons'] = $productsModel->select('addons.addon_id as id, products.name as name')->join('addons', 'addons.product_id = products.id')->where(['has_addons' => 1, 'addons.product_id' => $product['id']])->find();
        }
        $categories = $categoriesModel->where('restaurant_id', (int)session()->get('restaurant_id'))->where('is_subcategory IS NULL')->orderBy('name')->find();
        //print_r((int)session()->get('restaurant_id'));
        $data['addons'] = $productsModel->select('id, name')->where(['restaurant_id' => (int)session()->get('restaurant_id'), 'is_addon' => 1])->find();

        foreach ($categories as &$category) {
            if ($category['has_subcategories']) {
                $subcategories = $categoriesModel->where(['restaurant_id' => (int)session()->get('restaurant_id'), 'is_subcategory' => $category['id']])->orderBy('name')->find();
                $category['subcategories'] = $subcategories;
            } else {
                $category['subcategories'] = [];
            }
        }

        $data['categories'] = $categories;
        $data['magazzini'] = $magazziniModel->where('restaurant_id', (int)session()->get('restaurant_id'))->find();

        echo view("templates/header", $data);
        echo view('backend/magazzino');
        echo view("templates/footer");
        return;
    }

    public function magazzini()
    {
        $magazziniModel = new MagazziniModel();

        if ($this->request->getMethod() == 'post') {
            $form = $this->request->getVar();
            $magazziniModel->save($form);
        }

        $data['magazzini'] = $magazziniModel->where('restaurant_id', (int)session()->get('restaurant_id'))->find();

        echo view("templates/header", $data);
        echo view('backend/magazzini');
        echo view("templates/footer");
        return;
    }

    public function categories()
    {
        $categoriesModel = new CategoriesModel();

        if ($this->request->getMethod() == 'post') {

            $category = $this->request->getVar();
            if ($category['is_subcategory'] == 0) {
                $category['is_subcategory'] = null;
            }
            //print_r($category);

            $imgpath = $this->siteConfig->PUBLICPATH . "images/";
            $image = $this->request->getFile('image');
            $oldimage = $this->request->getVar('oldimage');

            if ($image) {
                //print_r("ONE");
                if ($image->isValid()) {

                    //print_r("TWO");
                    $restaurantsModel = new RestaurantsModel();
                    $restaurant = $restaurantsModel->where('id', (int)session()->get('restaurant_id'))->first();
                    $restaurant_folder = (string)$restaurant['id'] . $restaurant['name'];
                    //print_r($image->getName());
                    if (!is_dir($imgpath . $restaurant_folder)) {
                        mkdir($imgpath . $restaurant_folder);
                        mkdir($imgpath . $restaurant_folder . '/products_images');
                        mkdir($imgpath . $restaurant_folder . '/categories_images');
                    }
                    if (file_exists($imgpath . $oldimage) && $oldimage) {
                        unlink($imgpath . $oldimage);
                    }
                    rename($image, $imgpath . $restaurant_folder  . '/categories_images/' . $image->getName());
                    $category['image'] = '/images/' . $restaurant_folder  . '/categories_images/' . $image->getName();
                }
            }

            if ($this->request->getPost('nuovo')) {
                $category['restaurant_id'] = (int)session()->get('restaurant_id');
            } else {
                /* controllo che la categoria padre non abbia solo quella come sottocategoria */
                $old_category = $categoriesModel->where('id', $category['id'])->first();
                $subcategories = $categoriesModel->where('is_subcategory', $old_category['is_subcategory'])->find();
                if (count($subcategories) == 1) {
                    $temp = $categoriesModel->where('id', $old_category['is_subcategory'])->first();
                    $temp['has_subcategories'] = null;
                    $categoriesModel->save($temp);
                }
            }

            /* aggiungo has_subcategory alla nuova categoria padre e aggiorno la categoria che ho acquisito */
            if ($categoryPadre = $categoriesModel->where('id', $category['is_subcategory'])->first()) {
                $categoryPadre['has_subcategories'] = 1;
                $categoriesModel->save($categoryPadre);
            }


            //print_r($category);

            $category['slug'] = str_replace(array("’", "'", " "), array("_", "_", "_"), strtolower(trim($category['name'])));
            $categoriesModel->save($category);
            //$tempOrder = $ordersModel->where('id', $order_id)->first();
        }

        $data['categories'] = $categoriesModel->where('categories.restaurant_id', (int)session()->get('restaurant_id'))->orderBy('name')->find();

        echo view("templates/header", $data);
        echo view('backend/categories');
        echo view("templates/footer");
        return;
    }

    public function deletecategory($category_id)
    {
        $categoriesModel = new CategoriesModel();

        $categoriesModel->delete($category_id);
        $subcategories = $categoriesModel->where('is_subcategory', $category_id)->find();

        foreach ($subcategories as &$sub) {
            $sub['is_subcategory'] = null;
            $categoriesModel->save($sub);
        }

        return redirect()->to(site_url('/categories'));
    }

    public function tables()
    {
        $tablesModel = new TablesModel();

        if ($this->request->getMethod() == 'post') {

            if ($this->request->getPost('tablesform')) {
                $table = $tablesModel->where('id', $this->request->getVar('id'))->first();
            }
            $form = $this->request->getVar();
            $table['name'] = $form['name'];
            $table['num_persons'] = $form['num_persons'];
            $table['cost_per_person'] = $form['cost_per_person'];
            $table['fixed_cost'] = $form['fixed_cost'];
            $table['min_price'] = (float)$form['num_persons'] * (float)$form['cost_per_person'];
            if ($this->request->getPost('newtable')) {
                $table['restaurant_id'] = (int)session()->get('restaurant_id');
            }
            $tablesModel->save($table);
        }

        $data['tables'] = $tablesModel->where('restaurant_id', (int)session()->get('restaurant_id'))->find();

        echo view("templates/header", $data);
        echo view('backend/tables');
        echo view("templates/footer");
        return;
    }

    public function deletetable($tableid)
    {
        $tablesModel = new TablesModel();

        $tablesModel->delete($tableid);

        return redirect()->to(site_url('/tables'));
    }

    public function deleteproduct($product_id)
    {
        $productsModel = new ProductsModel();
        $addonsModel = new AddonsModel();

        $product = $productsModel->where('id', $product_id)->first();
        $productsModel->delete($product_id);
        $addonsModel->where('product_id', $product_id)->delete();

        $publicpath = $this->siteConfig->PUBLICPATH;
        if (file_exists($publicpath . $product['image'])) {
            unlink($publicpath . $product('image'));
        }

        return redirect()->to(site_url('/magazzino'));
    }

    public function deletemagazzino($magazzinoid)
    {
        $magazziniModel = new MagazziniModel();
        $productsModel = new ProductsModel();

        $magazziniModel->delete($magazzinoid);

        $products = $productsModel->where('magazzino_id', $magazzinoid)->find();
        foreach ($products as &$product) {
            $product['magazzino_id'] = null;
            $productsModel->save($product);
        }

        return redirect()->to(site_url('/magazzini'));
    }

    public function analytics()
    {
        $restaurant_id = (int)session()->get('restaurant_id');
        $data = [
            'startDate' => date("Y/m/d"),
            'endDate' => date("Y/m/d")
        ];

        $startDate = date("Y/m/d");
        $endDate = date("Y/m/d");
        $firstDate = date('Y-m-d', strtotime('-1 day', strtotime($startDate)));

        if ($this->request->getMethod() == 'post') {
            $date = $this->request->getVar();
            $startDate = $date['startDate'];
            $endDate = $date['endDate'];
            if ($startDate > $endDate) {
                session()->setFlashdata("error", 'Attenzione! La data di inizio intervallo non può essere posteriore a quella di fine');
                echo view("templates/header", $data);
                echo view('backend/analytics');
                echo view("templates/footer");
                return;
            }
            $temp = new DateTime($startDate);
            $interval = $temp->diff(new DateTime($endDate));
            $firstDate = date('Y-m-d', strtotime('-' . $interval->days . 'day', strtotime($startDate)));
        }

        $ordersModel = new OrdersModel();

        // prezzo totale di vendita
        $totalRevenew = $ordersModel->select('SUM(total_price) as totalRevenew')->where('restaurant_id', $restaurant_id)->where("created > '" . $startDate . "'")->where("created <= '" . $endDate . "'")->whereIn('status_id', [1, 5])->find()[0]['totalRevenew'];
        $totalRevenew = round($totalRevenew, 2);
        $temp = $ordersModel->select('SUM(total_price) as totalRevenew')->where('restaurant_id', $restaurant_id)->where("created > '" . "2023-01-31" . "'")->where("created <= '" . $endDate . "'")->whereIn('status_id', [1, 5])->find()[0]['totalRevenew'];
        if ($totalRevenew && $temp) {
            $percentage = (float)$temp / $totalRevenew * 100;
            $revenewPercentage = round($percentage, 1);
        } else {
            $revenewPercentage = 0;
        }

        // prodotti ordinati
        $totalProducts = $ordersModel->select('SUM(quantity) as quantity')->join('orders_products', 'order_id = orders.id')->where('restaurant_id', $restaurant_id)->where("created > '" . $startDate . "'")->where("created <= '" . $endDate . "'")->whereIn('status_id', [1, 5])->find()[0]['quantity'];
        $temp = $ordersModel->select('SUM(quantity) as quantity')->join('orders_products', 'order_id = orders.id')->where('restaurant_id', $restaurant_id)->where("created > '" . $firstDate . "'")->where("created <= '" . $startDate . "'")->whereIn('status_id', [1, 5])->find()[0]['quantity'];
        if ($totalProducts and $temp) {
            $percentage = (float)$temp / $totalProducts * 100;
            $productsPercentage = round($percentage, 1);
        } else {
            $productsPercentage = 0;
        }

        // ordini
        $totalOrders = $ordersModel->select('COUNT(id) as orders')->where('restaurant_id', $restaurant_id)->where("created > '" . $startDate . "'")->where("created <= '" . $endDate . "'")->whereIn('status_id', [1, 5])->find()[0]['orders'];
        $temp = $ordersModel->select('COUNT(id) as orders')->where('restaurant_id', $restaurant_id)->where("created > '" . $firstDate . "'")->where("created <= '" . $startDate . "'")->whereIn('status_id', [1, 5])->find()[0]['orders'];
        if ($totalOrders and $temp) {
            $percentage = (float)$temp / $totalOrders * 100;
            $ordersPercentage = round($percentage, 1);
        } else {
            $ordersPercentage = 0;
        }

        // tavoli - suddivisi per giorni
        $totalTables = $ordersModel->select('COUNT(table_id) as tables')->where('restaurant_id', $restaurant_id)->where("created > '" . $startDate . "'")->where("created <= '" . $endDate . "'")->whereIn('status_id', [1, 5])->groupBy('DATE(created)')->find();
        $supportTotalTables = 0;
        foreach ($totalTables as $tt) {
            $supportTotalTables += $tt['tables'];
        }
        $totalTables = $supportTotalTables;
        $temp = $ordersModel->select('COUNT(table_id) as tables')->where('restaurant_id', $restaurant_id)->where("created > '" . $firstDate . "'")->where("created <= '" . $startDate . "'")->whereIn('status_id', [1, 5])->groupBy('DATE(created)')->find();
        $supportTemp = 0;
        foreach ($temp as $tt) {
            $supportTemp += $tt['tables'];
        }
        $temp = $supportTemp;
        if ($totalTables and $temp) {
            $percentage = (float)$temp / $totalTables * 100;
            $tablesPercentage = round($percentage, 1);
        } else {
            $tablesPercentage = 0;
        }

        // prodotto più e meno venduto
        $products = $ordersModel->select('product_id, product_name, SUM(quantity) as quantity')->join('orders_products', 'order_id = orders.id')->where('restaurant_id', $restaurant_id)->where("created > '" . $startDate . "'")->where("created <= '" . $endDate . "'")->whereIn('status_id', [1, 5])->groupBy('product_id')->orderBy('quantity')->findAll();
        try {
            $mostSold = end($products);
            if ($products[0]['product_id']) {
                $lessSold = $products[0];
            } else {
                $lessSold = $products[1];
            }
            $mostSoldPast = $ordersModel->select('product_id, product_name, SUM(quantity) as quantity')->join('orders_products', 'order_id = orders.id')->where(['restaurant_id' => $restaurant_id, 'product_id' => $mostSold['product_id']])->where("created > '" . $firstDate . "'")->where("created <= '" . $startDate . "'")->whereIn('status_id', [1, 5])->first();
            $lessSoldPast = $ordersModel->select('product_id, product_name, SUM(quantity) as quantity')->join('orders_products', 'order_id = orders.id')->where(['restaurant_id' => $restaurant_id, 'product_id' => $lessSold['product_id']])->where("created > '" . $firstDate . "'")->where("created <= '" . $startDate . "'")->whereIn('status_id', [1, 5])->first();
            $mostSoldPercentage = round($mostSoldPast['quantity'] / $mostSold['quantity'], 2);
            $lessSoldPercentage = round($lessSoldPast['quantity'] / $lessSold['quantity'], 2);
        } catch (Exception $e) {
            $products = [
                'quantity' => 0,
                'product_name' => ""
            ];
            $mostSold = $products;
            $lessSold = $products;
            $mostSoldPercentage = 0;
            $lessSoldPercentage = 0;
        }


        // valore medio dell'ordine
        $avgOrder = $ordersModel->select('AVG(total_price) as totalRevenew')->where('restaurant_id', $restaurant_id)->where("created > '" . $startDate . "'")->where("created <= '" . $endDate . "'")->whereIn('status_id', [1, 5])->find()[0]['totalRevenew'];
        $avgOrder = round($avgOrder, 2);
        $temp = $ordersModel->select('AVG(total_price) as totalRevenew')->where('restaurant_id', $restaurant_id)->where("created > '" . "2023-01-31" . "'")->where("created <= '" . $endDate . "'")->whereIn('status_id', [1, 5])->find()[0]['totalRevenew'];
        if ($avgOrder && $temp) {
            $percentage = (float)$temp / $avgOrder * 100;
            $avgPercentage = round($percentage, 1);
        } else {
            $avgPercentage = 0;
        }

        // numero di prodotti medi nell'ordine
        $avgTotalProducts = $ordersModel->select('SUM(quantity) as quantity')->join('orders_products', 'order_id = orders.id')->where('restaurant_id', $restaurant_id)->where("created > '" . $startDate . "'")->where("created <= '" . $endDate . "'")->whereIn('status_id', [1, 5])->groupBy('orders.id')->find();
        $avgOrderProducts = 0;
        foreach ($avgTotalProducts as $qt) {
            $avgOrderProducts += $qt['quantity'];
        }
        if ($avgOrderProducts && $temp) {
            $avgOrderProducts = round($avgOrderProducts / count($avgTotalProducts) - 0.5, 2); // ho tolto 0.5 per il costo del servizio che non sempre c'è
        } else {
            $avgOrderProducts = 0;
        }
        $temp = $ordersModel->select('SUM(quantity) as quantity')->join('orders_products', 'order_id = orders.id')->where('restaurant_id', $restaurant_id)->where("created > '" . $firstDate . "'")->where("created <= '" . $startDate . "'")->whereIn('status_id', [1, 5])->groupBy('orders.id')->find();
        $avgOrderProductsPast = 0;
        foreach ($temp as $qt) {
            $avgOrderProductsPast += $qt['quantity'];
        }
        if ($avgOrderProducts && $temp) {
            $avgOrderProductsPast = round($avgOrderProductsPast / count($temp) - 0.5, 2); // ho tolto 0.5 per il costo del servizio che non sempre c'è
            $avgPercentage = round($avgOrderProductsPast / $avgOrderProducts, 2);
        } else {
            $avgProductsPercentage = 0;
        }
        if ($avgOrderProducts && $temp) {
            $percentage = (float)$temp / $avgOrderProducts * 100;
            $avgProductsPercentage = round($percentage, 1);
        } else {
            $avgProductsPercentage = 0;
        }


        // motedi di pagamento
        $paymentsMethod = $ordersModel->select('payment_method_slug as method, count(distinct(id)) as quantity, sum(total_price) as total')->where('restaurant_id', $restaurant_id)->where("created > '" . $startDate . "'")->where("created <= '" . $endDate . "'")->whereIn('status_id', [1, 5])->groupBy('payment_method_slug')->orderBy('payment_method_slug')->find();
        $paymentsMethodPast = $ordersModel->select('payment_method_slug as method, count(distinct(id)) as quantity, sum(total_price) as total')->where('restaurant_id', $restaurant_id)->where("created > '" . $firstDate . "'")->where("created <= '" . $startDate . "'")->whereIn('status_id', [1, 5])->groupBy('payment_method_slug')->orderBy('payment_method_slug')->find();
        if ($paymentsMethodPast) {
            foreach ($paymentsMethod as &$pm) {
                foreach ($paymentsMethodPast as $pmp) {
                    if ($pm['method'] == $pmp['method']) {
                        if ($pmp['quantity'] != 0) {
                            $pm['qpercentage'] = (float)$pmp['quantity'] / $pm['quantity'] * 100;
                        } else {
                            $pm['qpercentage'] = 100;
                        }
                        print_r($pm);
                        if ($pm['total_price'] != 0) {
                            $pm['tpercentage'] = (float)$pmp['total'] / $pm['total'] * 100;
                        } else {
                            $pm['tpercentage'] = 100;
                        }
                        break;
                    } else {
                        $pm['qpercentage'] = 100;
                        $pm['tpercentage'] = 100;
                    }
                }
            }
        } else {
            foreach ($paymentsMethod as &$pm) {
                $pm['qpercentage'] = 100;
                $pm['tpercentage'] = 100;
            }
        }


        print_r($paymentsMethod);

        $data = [
            'firstDate' => $firstDate,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalRevenew' => $totalRevenew,
            'revenewPercentage' => $revenewPercentage,
            'totalProducts' => $totalProducts,
            'productsPercentage' => $productsPercentage,
            'totalTables' => $totalTables,
            'tablesPercentage' => $tablesPercentage,
            'totalOrders' => $totalOrders,
            'ordersPercentage' => $ordersPercentage,
            'mostSold' => $mostSold,
            'lessSold' => $lessSold,
            'mostSoldPercentage' => $mostSoldPercentage,
            'lessSoldPercentage' => $lessSoldPercentage,
            'avgOrder' => $avgOrder,
            'avgPercentage' => $avgPercentage,
            'avgOrderProducts' => $avgOrderProducts,
            'avgProductsPercentage' => $avgProductsPercentage,
            'metodi' => $paymentsMethod
        ];

        echo view("templates/header", $data);
        echo view('backend/analytics');
        echo view("templates/footer");
        return;
    }

    public function soldProducts($startDate, $endDate)
    {
        $restaurant_id = (int)session()->get('restaurant_id');
        $temp = new DateTime($startDate);
        $interval = $temp->diff(new DateTime($endDate));
        $firstDate = date('Y-m-d', strtotime('-' . $interval->days . 'day', strtotime($startDate)));

        $orderedProductsModel = new OrdersProductsModel();
        $products = $orderedProductsModel->select('orders_products.product_name as product_name, COUNT(orders_products.id) as quantity_sold, SUM(orders_products.price) as total, categories.name as category, magazzini.name as magazzino')->join('orders', 'orders_products.order_id = orders.id')->join('products', 'products.id = orders_products.product_id', 'LEFT')->join('categories', 'categories.id = products.category_id', 'LEFT')->join('magazzini', 'magazzini.id = products.magazzino_id', 'LEFT')->where("orders.created > '" . $startDate . "'")->where("orders.created <= '" . $endDate . "'")->whereIn('orders.status_id', [1, 5])->where('orders_products.product_id IS NOT NULL')->where('orders.restaurant_id', $restaurant_id)->groupBy('orders_products.product_id')->find();

        $currentYear = date('Y');
        $currentYearStartDate = date('Y-m-d', strtotime('January 1st ' . $currentYear));
        $previousYearStartDate = date('Y-m-d', strtotime('January 1st ' . ($currentYear - 1)));

        $yearProducts = $orderedProductsModel->select("product_name, SUM(CASE WHEN(created > '" . $currentYearStartDate . "') then quantity else 0) as currYear, SUM(CASE WHEN(created > '" . $previousYearStartDate . "' AND created < '" . $currentYearStartDate . "') then quantity else 0) as pastYear")->join('orders', 'orders_products.order_id = orders.id')->whereIn('orders.status_id', [1, 5])->where('orders_products.product_id IS NOT NULL')->where('orders.restaurant_id', $restaurant_id)->groupBy('orders_products.product_id')->find();


        $data = [
            'products' => $products,
            'yearProducts' => $yearProducts
        ];

        echo view("templates/header", $data);
        echo view('backend/soldProducts');
        echo view("templates/footer");
        return;
    }
}
