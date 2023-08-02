<?php namespace App\Models;

use App\Models\BaseModel;

use CodeIgniter\Model;

class EventsTagsModel extends Model
{
    protected $table ="events_tags";
    protected $primayKey = "id";

    protected $allowedFields = ['event_id','tag_id'];

}