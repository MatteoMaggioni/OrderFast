<?php namespace App\Models;


use App\Models\BaseModel;


use CodeIgniter\Model;


class ProductsModel extends Model
{
    protected $table ="products";
    protected $primayKey = "id";


    protected $allowedFields = ['name','description','image','quantity','is_composed','is_variation','is_quantified','has_addons','price','barista_type','short_description','promotion_price','is_foreground','restaurant_id','category_id','is_vegan','is_freezed','is_vegetarian','is_addon','magazzino_id','updated','created'];


    protected $createdField  = 'created';
    protected $updatedField  = 'updated';


    protected $beforeInsert = ['beforeInsert'];
    protected $beforeUpdate = ['beforeUpdate'];


    protected function beforeInsert(array $data)
    {


        $data['data']['created'] = date('Y-m-d H:i:s');
        $data['data']['updated'] = date('Y-m-d H:i:s');


        return $data;
    }


    protected function beforeUpdate(array $data)
    {


        $data['data']['updated'] = date('Y-m-d H:i:s');


        return $data;
    }


}
