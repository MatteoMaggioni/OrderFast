<?php namespace App\Models;


use App\Models\BaseModel;


use CodeIgniter\Model;


class OrdersProductsModel extends Model
{
    protected $table ="orders_products";
    protected $primayKey = "id";


    protected $allowedFields = ['order_id','ordered_product_id', 'product_id','quantity', 'has_addons', 'price', 'product_name', 'product_price'];

}
