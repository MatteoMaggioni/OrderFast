<?php namespace App\Models;


use App\Models\BaseModel;


use CodeIgniter\Model;


class PrRestaurantsEventsModel extends Model
{
    protected $table ="pr_restaurants_events";
    protected $primayKey = "id";

    protected $allowedFields = ['pr_id','restaurant_id','event_id','max_tickets'];
}