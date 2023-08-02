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
                    $temp = $productsModel->where('id', $product['product_id'])->first();
                    if ($temp['is_quantified']) {
                        $temp['quantity'] += $product['quantity'];
                        $productsModel->save($temp);
                    }
                }
            }

            if (($order['status_id'] == 2 || $order['status_id'] == 3 || $order['status_id'] == 4) and ($order_status == 1 || $order_status == 5)) {
                $ordered_products = $ordersProductsModel->where('order_id', $order_id)->find();
                foreach ($ordered_products as $product) {
                    $temp = $productsModel->where('id', $product['product_id'])->first();
                    if ($temp['is_quantified']) {
                        $temp['quantity'] -= $product['quantity'];
                        $productsModel->save($temp);
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
            // print_r($category);

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
                    print_r($image->getName());
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
                $subcategories = $categoriesModel->where('is_subcategory', $category['id'])->find();
                foreach ($subcategories as &$sub_cat) {
                    $sub_cat['is_subcategory'] = null;
                    $categoriesModel->save($sub_cat);
                }
            }

            $has_subcategories = false;
            foreach ($category['subcategories'] as $subcat_id) {
                if ($subcat_id && $category['id'] != $subcat_id) {
                    $subcategory = $categoriesModel->where('id', $subcat_id)->first();
                    $subcategory['is_subcategory'] = $category['id'];
                    $categoriesModel->save($subcategory);
                    $has_subcategories = 1;
                }
            }

            $category['has_subcategories'] = $has_subcategories;

            //print_r($category);

            $category['slug'] = strtolower(trim($category['name']));
            $categoriesModel->save($category);
            //$tempOrder = $ordersModel->where('id', $order_id)->first();
        }

        $data['categories'] = $categoriesModel->where('categories.restaurant_id', (int)session()->get('restaurant_id'))->orderBy('name')->find();
        foreach ($data['categories'] as &$category) {
            if ($category['has_subcategories']) {
                $category['subcategories'] = $categoriesModel->where('is_subcategory', $category['id'])->orderBy('name')->find();
            } else {
                $category['subcategories'] = [];
            }
        }

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

        foreach($subcategories as &$sub) {
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
}
