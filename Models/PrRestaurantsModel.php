<?php namespace App\Models;


use App\Models\BaseModel;


use CodeIgniter\Model;


class PrRestaurantsModel extends Model
{
    protected $table ="pr_restaurants";
    protected $primayKey = "id";

    protected $allowedFields = ['pr_id','restaurant_id','is_selected','percentage'];
}