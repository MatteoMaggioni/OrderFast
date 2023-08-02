<?php

namespace App\Controllers;

use App\Models\PRRestaurantsModel;
use App\Models\TicketsModel;
use App\Models\EventsModel;
use App\Models\AddonsModel;
use App\Models\TablesModel;
use App\Models\RestaurantsModel;
use App\Model\EventsTablesModel;
use Exception;

class PR extends BaseController
{
    public function ticketsPrResume()
    {
        /* MODIFICARE LOGIN PER RIDIREZIONARE SULLA PAGINA DI RESUME */
        /* blocchi uno per ristorante */
        $pr_id = session('profile_id');
        $ticketsModel = new TicketsModel();
        print_r($pr_id);
        /* definire il numero di biglietti mancanti per ogni evento  */
        $restaurantsModel = new RestaurantsModel();
        $data['restaurants'] = $restaurantsModel->select('restaurants.name as restaurant_name, sum(pr_restaurants_events.max_tickets) as max_tickets, restaurants.logo as logo, restaurants.id as id')->join('pr_restaurants_events', 'restaurants.id = pr_restaurants_events.restaurant_id')->join('events', 'events.id = pr_restaurants_events.event_id')->where('events.date > CURRENT_DATE')->where('pr_restaurants_events.pr_id', $pr_id)->groupBy('restaurants.id')->find();
        $data['num_tickets'] = $ticketsModel->select('tickets.restaurant_id, count(distinct tickets.id) as count')->join('events', 'events.id = tickets.event_id')->where('events.date > CURRENT_DATE')->where('pr_id', $pr_id)->groupBy('tickets.restaurant_id')->find();
        $data['pr_id'] = $pr_id;

        echo view('templates/header', $data);
        echo view('pr/resumePr');
        echo view('templates/footer');
    }

    /* gestione biglietti vecchi e  */
    public function restaurantPrTickets($pr_id, $restaurant_id)
    {
        $ticketsModel = new TicketsModel();
        $restaurantsModel = new RestaurantsModel();
        $eventsModel = new EventsModel();
        $eventsTablesModel = new EventsTablesModel();

        if ($this->request->getMethod('post')) {
            /* modificare nel caso di tavolo aggiunta tavolo togliere tavolo */
            $ticket = $this->request->get();
            $ticketsModel->save($ticket);
            $ticket_id = $this->db->insert_id(); /* last inserted id */

            if ($this->request->getPost('newTicket')) {
                session()->setFlashdata("success", 'Creazione biglietto avvenuta con successo');
                $data['event'] = $eventsModel->where('id', $ticket['event_id'])->first();
                $data['restaurant'] = $restaurantsModel->where('id', $ticket['restaurant_id'])->first();
                $data['ticket'] = $ticket;

                /* switchcase per tipologia di pagamenti */
                if ($ticket['payment_method_id'] == 2 /* contanti */) {
                    /* crea qr per visualizzazione pagina al cliente */
                    echo view('templates/header', $data);
                    echo view('pr/ticketReciept');
                    echo view('templates/footer');
                    return;
                }

                /* crea qr per fare il pagamento tramite carta */
                $qr = $ticketsModel->where('id', $ticket_id)->first()['qr'];
                $data['qrLink'] = urlencode(base_url('/payTicket/'.$qr));
                echo view('templates/header', $data);
                echo view('pr/qrPayment');
                echo view('templates/footer');
                return;
            }

            session()->setFlashdata("success", 'Aggiornamento avvenuto con successo');
        }

        $data['restaurant_name'] = $restaurantsModel->where('id', $restaurant_id)->first()['name'];
        $tickets = $ticketsModel->join('pr_restaurants_events', 'pr_restaurants_events.pr_id = tickets.pr_id', 'RIGHT')->join('events', 'events.id = tickets.event_id')->where(['pr_id' => $pr_id, 'restaurant_id' => $restaurant_id])->find();
        $eventsTemp = $eventsModel->join('pr_restaurants_events', 'pr_restaurants_events.event_id = events.id')->where(['pr_restaurants_events.pr_id' => $pr_id, 'restaurant_id' => $restaurant_id, 'events.date > CAST( GETDATE() AS Date )'])->find();
        $events_id = [];
        foreach ($eventsTemp as $event) {
            $events_id[] = $event['id'];
        }
        $events_tables = $eventsTablesModel->where('event_id', $events_id)->find();

        $events = [];
        foreach ($eventsTemp as $event) {
            $temp['event'] = $event;
            foreach ($tickets as $ticket) {
                if ($ticket['event_id'] == $event['id']) {
                    $temp['tickets'][] = $ticket;
                }
            }
            foreach ($events_tables as $table) {
                if ($table['event_id'] == $event['id']) {
                    $temp['tables'][] = $table;
                }
            }
            $events[] = $temp;
        }

        echo view('templates/header', $data);
        echo view('pr/restaurantPrTickets');
        echo view('templates/footer');
    }

    public function payTicket($qr) {
        $ticketsModel = new TicketsModel();
        $ticket = $ticketsModel->join('events', 'events.id = tickets.event_id')->join('restaurants', 'tickets.restaurant_id = restaurants.id')->where('qr', $qr)->first();
        $session_payment = [
            'restaurant_id' => $ticket['restaurant_id'],
            'totalPrice' => $ticket['ticket_price']
        ];
        if($ticket['table_id']) {
            $eventsTablesModel = new EventsTablesModel();
            $table = $eventsTablesModel->where(['table_id' => $ticket['table_id'], 'event_id' => $ticket['event_id']])->first();
            $session_payment['totalPrice'] = $table['num_persons'] * $table['cost_per_person'];
            $ticket['table'] = $table;
        }

        session()->set($session_payment);
        $data['ticket'] = $ticket;

        echo view('templates/header', $data);
        echo view('pr/ticketPayment');
        echo view('templates/footer');
        return;
    }

}
