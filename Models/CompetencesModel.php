<?php namespace App\Models;


use App\Models\BaseModel;


use CodeIgniter\Model;


class CompetencesModel extends Model
{
    protected $table ="competences";
    protected $primayKey = "id";


    protected $allowedFields = ['description'];


    
}
