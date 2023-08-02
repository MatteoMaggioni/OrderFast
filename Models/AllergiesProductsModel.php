<?php namespace App\Models;


use App\Models\BaseModel;


use CodeIgniter\Model;


class AllergiesProductsModel extends Model
{
    protected $table ="allergies_products";
    protected $primayKey = "id";


    protected $allowedFields = ['allergy_id','product_id'];


}
