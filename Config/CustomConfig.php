<?php namespace Config;

use CodeIgniter\Config\BaseConfig;

class CustomConfig extends BaseConfig
{
    public $siteName  = 'Order Fast';
    public $siteEmail = 'info@orderfast.it';
    public $management = 0; // = 1 - mette il sito in stato manutentivo - 0 per production

    public $notifica_email ="noreply@orderfast.it";

    public $COSTANTPATH = '/var/www/html/.....';
    public $PUBLICPATH = '/var/www/html/varreeds.com/public/';

    // email configuration

    public $fromemail = "info@orderfast.it";// "noreply@campionatistudenteschi.it";
    public $host = "mail.orderfast.it";
    public $port = 587;
    public $username = "info@orderfast.it";
    public $password = "OrderFast2023!";
    public $img = '/imgages/....';
    public $setFrom = 'OrderFast';
}
