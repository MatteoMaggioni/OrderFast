<?php namespace App\Models;

use App\Models\BaseModel;

use CodeIgniter\Model;

class AddonsModel extends Model
{
    protected $table ="addons";
    protected $primayKey = "id";

    protected $allowedFields = ['addon_id','product_id', 'is_quantified', 'price'];

}
 