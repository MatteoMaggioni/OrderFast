<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    protected $siteConfig;
    protected $errorForm = false;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['form'];

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);
        $this->siteConfig = config("CustomConfig");

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();
    }

    /**
     * Imposta un messaggio di errore nei dati session flash
     */
    protected function addErrorMessage($message)
    {
        $error = session()->getFlashdata('error');
        if (is_array($error))
            $errors = array_merge($error, (array)$message);
        else
            $errors = array_merge((array)$error, (array)$message);

        session()->setFlashdata('error', $errors);
        $this->errorForm = true;
    }

    protected function hasError()
    {
        return $this->errorForm;
    }

    protected function addSuccessMessage($message)
    {
        $success = session()->getFlashdata('success');
        if (is_array($success))
            $messages = array_merge($success, (array)$message);
        else
            $messages = array_merge((array)$success, (array)$message);
        session()->setFlashdata('success', $messages);
    }

    protected function addInfoMessage($message)
    {

        // $info = session()->getFlashdata('message');
        // if (is_array($info))
        // 	$messages = array_merge($info,(array)$message);
        // else
        // 	$messages = array_merge((array)$info,(array)$message);
        // session()->setFlashdata('message',$messages);

        $info = session()->getFlashdata('message');
        $info[] = $message;
        session()->setFlashdata('message', $info);
    }

    public function invio_email($data, $template) {
        $fromemail = $this->siteConfig->fromemail;
        $toemail = $operator['email'];
        //$toemail = 'matteomaggioni94@gmail.com';
        $subject = "Creazione profilo operatore";
        $emaildata = [
            'logo' => base_url() . '/favicon.ico',
            'url' => base_url() . '/login'
        ];

        $mesg = view($template, $emaildata);

        $email = new Phpmailer_library();

        $email->IsSMTP();
        $email->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $email->isHTML(true);
        $email->CharSet = $this->siteConfig->charset;
        $email->Encoding = PHPMailer::ENCODING_BASE64;
        $email->SMTPAuth = true;
        $email->Host = $this->siteConfig->host;
        $email->Port = $this->siteConfig->port;
        $email->SMTPSecure = $this->siteConfig->smtpsecure;
        $email->Username = $this->siteConfig->username;
        $email->Password = $this->siteConfig->password;
        $email->setFrom($fromemail, $this->siteConfig->setFrom);
        $email->addAddress($toemail);
        $email->Subject = $subject;
        $email->Body = $mesg;
        $filename = base_url() . $this->siteConfig->img;
        $email->addAttachment($filename);
        $email->addAttachment(base_url() . '/favicon.ico');

        return $email->send();
    }
}
