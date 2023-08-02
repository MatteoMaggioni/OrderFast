<?php namespace App\Models;


use App\Models\BaseModel;


use CodeIgniter\Model;


class EventsTablesModel extends Model
{
    protected $table ="events_tables";
    protected $primayKey = "id";

    protected $allowedFields = ['event_id','table_id','is_sold','name','restaurant_id','min_persons','cost_per_person','fixed_cost'];
}