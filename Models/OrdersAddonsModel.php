<?php namespace App\Models;


use App\Models\BaseModel;


use CodeIgniter\Model;


class OrdersAddonsModel extends Model
{
    protected $table ="orders_addons";
    protected $primayKey = "id";


    protected $allowedFields = ['order_id','ordered_product_id', 'addon_id', 'addon_name', 'addon_price'];

}
