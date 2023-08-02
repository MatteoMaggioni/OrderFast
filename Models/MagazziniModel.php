<?php namespace App\Models;

use App\Models\BaseModel;

use CodeIgniter\Model;

class MagazziniModel extends Model
{
    protected $table ="magazzini";
    protected $primayKey = "id";

    protected $allowedFields = ['name','restaurant_id', 'visibility'];

}
