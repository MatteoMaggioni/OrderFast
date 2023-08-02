<?php namespace App\Models;


use App\Models\BaseModel;


use CodeIgniter\Model;


class PaymentMethodsModel extends Model
{
    protected $table ="payment_methods";
    protected $primayKey = "id";


    protected $allowedFields = ['name','description', 'slug'];

}
