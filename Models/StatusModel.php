<?php namespace App\Models;


use App\Models\BaseModel;


use CodeIgniter\Model;


class StatusModel extends Model
{
    protected $table ="status";
    protected $primayKey = "id";


    protected $allowedFields = ['name'];

}
