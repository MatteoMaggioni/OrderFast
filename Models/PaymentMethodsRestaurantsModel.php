<?php namespace App\Models;


use App\Models\BaseModel;


use CodeIgniter\Model;


class PaymentMethodsRestaurantsModel extends Model
{
    protected $table ="payment_methods_restaurants";
    protected $primayKey = "id";


    protected $allowedFields = ['payment_methods_id','restaurant_id'];

}
