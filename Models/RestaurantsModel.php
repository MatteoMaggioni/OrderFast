<?php namespace App\Models;


use App\Models\BaseModel;


use CodeIgniter\Model;


class RestaurantsModel extends Model
{
    protected $table ="restaurants";
    protected $primayKey = "id";


    protected $allowedFields = ['name','description','location','phone','mall','country','region','state','subscription','payments','payments_type','price','payments_months','payments_initialdate','payments_expiration','stripe_secret','stripe_key'];


}
