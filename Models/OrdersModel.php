<?php

namespace App\Models;


use App\Models\BaseModel;


use CodeIgniter\Model;


class OrdersModel extends Model
{
    protected $table = "orders";
    protected $primayKey = "id";

    protected $allowedFields = ['total_price', 'table_id', 'table_name', 'restaurant_id', 'status_id', 'payment_method_slug', 'updated', 'created'];
    
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
