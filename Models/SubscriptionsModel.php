<?php namespace App\Models;


use App\Models\BaseModel;


use CodeIgniter\Model;


class SubscriptionsModel extends Model
{
    protected $table ="subscriptions";
    protected $primayKey = "id";

    protected $allowedFields = ['restaurant_id','price','initial_date','final_date','account_id'];

}
