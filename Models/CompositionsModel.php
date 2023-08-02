<?php namespace App\Models;


use App\Models\BaseModel;


use CodeIgniter\Model;


class CompositionsModel extends Model
{
    protected $table ="compositions";
    protected $primayKey = "id";


    protected $allowedFields = ['product_id','price_variation','min_quantity','max_quantity','updated','created'];


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
