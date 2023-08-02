<?php

namespace App\Controllers;

use App\Models\PrRestaurantsModel;
use App\Models\TicketsModel;
use App\Models\EventsModel;
use App\Models\AddonsModel;
use App\Models\TablesModel;
use App\Models\RestaurantsModel;
use App\Models\EventsTablesModel;
use App\Models\PrRestaurantsEventsModel;
use Exception;

class PRRestaurant extends BaseController
{
    public function createEvents()
    {

        $restaurant_id = session('restaurant_id');
        $eventsModel = new EventsModel();

        /* creazione evento */
        if ($this->request->getMethod() == 'post') {

            print_r($this->request->getMethod());
            $event = $this->request->getVar();
            $imgpath = $this->siteConfig->PUBLICPATH . "images/";
            $image = $this->request->getFile('image');

            //print_r($image);

            if ($image) {
                //print_r("ONE");
                if ($image->isValid()) {

                    $imagename = str_replace(' ', '', $image->getName());
                    print_r($image);
                    //print_r("TWO");
                    $restaurantsModel = new RestaurantsModel();
                    $restaurant = $restaurantsModel->where('id', (int)session()->get('restaurant_id'))->first();
                    $restaurant_folder = (string)$restaurant['id'] . $restaurant['name'];
                    //print_r($image->getName());

                    if (!is_dir($imgpath . $restaurant_folder)) {
                        mkdir($imgpath . $restaurant_folder);
                        mkdir($imgpath . $restaurant_folder . '/products_images');
                        mkdir($imgpath . $restaurant_folder . '/categories_images');
                        mkdir($imgpath . $restaurant_folder . '/events_images');
                    }

                    rename($image, $imgpath . $restaurant_folder  . '/events_images/' . $imagename);
                    $event['image'] = '/images/' . $restaurant_folder  . '/events_images/' . $imagename;
                    print_r($event['image']);
                }
            }

            $event_id = $eventsModel->insert($event);

            if ($event_id) {
                /* associazione automatica con i pr che hanno sono selzionati */
                $past_pr = $prRestaurants->where(['restaurant_id' => $restaurant_id, 'is_selected' => 1])->find();
                $temp['event_id'] = $event_id;
                foreach ($past_pr as $pr_restaurant) {
                    $temp = [
                        'percentage' => $pr_restaurant['percentage'],
                        'pr_restaurant_id' => $pr_restaurant['id']
                    ];
                    $prRestaurantsEventsModel->insert($temp);
                }

                /* associazione automatica tavoli disponibili */
                $tablesModel = new TablesModel();
                $eventsTablesModel = new EventsTablesModel();
                $tables = $tablesModel->select('tables.*, tables.id as table_id')->where('restaurant_id', $restaurant_id)->find();
                foreach($tables as &$table) {
                    $table['event_id'] = $event_id;
                    $eventsTablesModel->insert($table);
                }

            }

            print_r($event);
        }

        $events = $eventsModel->select('events.*, COUNT(tickets.id) as num_tickets, COUNT(CASE WHEN table_id THEN 1 END) as num_tables')->join('tickets', 'tickets.event_id = events.id', 'LEFT')->where('events.restaurant_id', $restaurant_id)->groupBy('events.id')->orderBy('date')->find();

        foreach ($events as $event) {
            if ($event['date'] > date("Y-m-d")) {
                $data['events'][] = $event;
            } else {
                $data['pastevents'][] = $event;
            }
        }

        echo view('templates/header', $data);
        echo view('pr/events');
        echo view('templates/footer');
    }

    public function showEvent($event_id)
    {
        $restaurant_id = session('restaurant_id');
        $eventsModel = new EventsModel();
        $prRestaurantsModel = new PrRestaurantsModel();
        $prRestaurantsEventsModel = new PrRestaurantsEventsModel();
        $eventsTablesModel = new EventsTablesModel();

        /* modifica evento */
        if ($this->request->getMethod('post') && $this->request->getPost('modifyEvent')) {

            $event = $this->request->getVar();
            $imgpath = $this->siteConfig->PUBLICPATH . "images/";
            $image = $this->request->getFile('image');
            $oldimage = $this->request->getVar('oldimage');

            //print_r($image);

            if ($image) {
                //print_r("ONE");
                if ($image->isValid()) {

                    $imagename = str_replace(' ', '', $image->getName());
                    print_r($image);
                    //print_r("TWO");
                    $restaurantsModel = new RestaurantsModel();
                    $restaurant = $restaurantsModel->where('id', (int)session()->get('restaurant_id'))->first();
                    $restaurant_folder = (string)$restaurant['id'] . $restaurant['name'];
                    //print_r($image->getName());

                    if (!is_dir($imgpath . $restaurant_folder)) {
                        mkdir($imgpath . $restaurant_folder);
                        mkdir($imgpath . $restaurant_folder . '/products_images');
                        mkdir($imgpath . $restaurant_folder . '/categories_images');
                        mkdir($imgpath . $restaurant_folder . '/events_images');
                    }
                    if (file_exists($imgpath . $oldimage) && !is_dir($imgpath . $oldimage)) {
                        unlink($imgpath . $oldimage);
                    }
                    rename($image, $imgpath . $restaurant_folder  . '/events_images/' . $imagename);
                    $event['image'] = '/images/' . $restaurant_folder  . '/events_images/' . $imagename;
                    print_r($event['image']);
                }
            }

            print_r($event);
            $eventsModel->save($event);
        }

        /* aggiungo pr all'evento */
        if ($this->request->getMethod('post') && $this->request->getPost('addPr')) {
            $pr = $this->request->getVar();
            foreach($pr as $pr_id) {
                $temp = [
                    'pr_restaurant' => $pr_id,
                    'event_id' => $event_id
                ];
                $prRestaurantsEventsModel->insert($temp);
            }
        }

        /* modifica pr rispetto all'evento */
        if ($this->request->getMethod('post') && $this->request->getPost('modifyPr')) {
            $pr = $this->request->getVar();
            $prRestaurantsEventsModel->save($pr);
        }

        /* modifica tavoli per l'evento solo per quelli non venduti */
        if ($this->request->getMethod('post') && $this->request->getPost('modifyTable')) {
            $table = $this->request->getVar();
            $eventsTablesModel->save($table);
        }

        $data['event'] = $eventsModel->select('events.*,events.id as id, COUNT(tickets.id) as num_tickets, COUNT(CASE WHEN table_id THEN 1 END) as num_tables')->join('tickets', 'tickets.event_id = events.id', 'LEFT')->where('events.id', $event_id)->first();
        $data['all_pr'] = $prRestaurantsModel->where('restaurant_id', $restaurant_id)->find();
        $data['pr_event'] = $prRestaurantsEventsModel->select('pr_restaurants_events.*, COUNT(tickets.id) as num_tickets, COUNT(CASE WHEN tickets.table_id THEN 1 END) as num_tables')->join('tickets', 'tickets.pr_restaurant_id = pr_restaurants_events.pr_restaurant_id', 'LEFT')->where('pr_restaurants_events.event_id', $event_id)->groupBy('pr_restaurants_events.event_id')->find();

        /* elimino da all_pr tutti i pr già selezionati */
        foreach ($data['all_pr'] as $pr) {
            foreach ($data['pr_event'] as $selected_pr) {
                if ($selected_pr['pr_restaurant_id'] == $pr['id']) {
                    unset($array[array_search($pr, $data['all_pr'])]);
                }
            }
        }

        $data['tables'] = $eventsTablesModel->where('event_id', $event_id)->find();

        echo view('templates/header', $data);
        echo view('pr/showEvent');
        echo view('templates/footer');
    }
}
