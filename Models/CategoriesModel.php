<?php namespace App\Models;


use App\Models\BaseModel;


use CodeIgniter\Model;


class CategoriesModel extends Model
{
    protected $table ="categories";
    protected $primayKey = "id";


    protected $allowedFields = ['name','slug','description','has_subcategories','is_subcategory','restaurant_id','image','updated','created'];


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
