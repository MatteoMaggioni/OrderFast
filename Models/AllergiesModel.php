<?php namespace App\Models;

use App\Models\BaseModel;

use CodeIgniter\Model;

class AllergiesModel extends Model
{
    protected $table ="allergies";
    protected $primayKey = "id";

    protected $allowedFields = ['name','description'];

}
