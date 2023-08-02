<?php namespace App\Models;

use App\Models\BaseModel;

use CodeIgniter\Model;

class EventsModel extends Model
{
    protected $table ="events";
    protected $primayKey = "id";

    protected $allowedFields = ['restaurant_id','name','description','location','min_age','ticket_price','image','date', 'presale_date'];

}