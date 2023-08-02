<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserAccountsModel;
use App\Models\UserProfilesModel;
use App\Models\RegistrationModel;
use App\Models\SchoolModel;
use PHPMailer\PHPMailer\PHPMailer;
use App\Libraries\Phpmailer_library;

class forgot_pass extends BaseController
{
    public function index()
    {
        $data = [];


        helper('form');

        if ($this->request->getMethod() === 'post') {

            $rules = [
                'email' => 'required',
            ];
            $messages = [
                'email' => [
                    'required' => 'Inserire email'
                ],
            ];

            if (!$this->validate($rules, $messages)) {
                $data['validation'] = $this->validator;
            } else {

                $email = strtolower($this->request->getVar('email'));
                //echo $email;

                $userAccountsModel = new UserAccountsModel();
                $userAccount = $userAccountsModel->where(["email" => $email])->first();
                if (!$userAccount) {
                    session()->setFlashdata('error', 'L\'email non risulta essere presente nel nostro database!');
                    return redirect()->to('/forgot_pass');
                }

                $userProfilesModel = new UserProfilesModel();
                $userProfile = $userProfilesModel->where(["id" => $userAccount["profile_id"]])->first();
                if (!$userProfile) {
                    session()->setFlashdata('error', 'Non è presente un profilo valido! Si prega di contattare l\'assistenza');
                    return redirect()->to('/forgot_pass/error');
                }

                $informations = array_merge($userProfile, $userAccount);

                echo $informations['password'];
                echo $informations["username"];

                if ($this->email($informations)) {
                    session()->setFlashdata('success', 'Email inviata email a ' . $informations['email'] . ' per la creazione di una nuova password!');
                    return redirect()->to('/login');
                } else {
                    session()->setFlashdata('error', 'Email non inviata a causa di un errore inaspettato! Contattare l\'assistenza per email a assistenza@orderfast.it');
                    return redirect()->to('/forgot_pass/error');
                }
            }
        }

        echo view('templates/header');
        echo view('forgot_pass/index', $data);
        echo view('templates/footer');
        return;
    }



    private function email($data)
    {
        $fromemail = $this->siteConfig->fromemail;
        $hash = $data['password'];
        $toemail = $data['email'];
        $subject = "Aggiornamento password profilo OrderFasst";

        $mesg = "<html><body>Buongiorno ".$data['firstname'].",<br/>grazie per usare il nostro servizio. Questa email serve per recuperare la password. Intanto le ricordiamo anche il suo username: ".$data['username'].".
        Mi raccomando, non lo condivida con nessuno!<br/>
		Clicca sul <a href='" . base_url() . "/nuova_password?hash=".$hash."'>link</a> per confermare la richiesta di nuova password.<br />
		Se il link non è visibile, copia la riga qui sotto e incollala sul tuo browser (compreso il punto finale se dovesse esserci): <br />
		" . base_url() . "/nuova_password?".$hash."<br/><i>Se non dovessi essere stato tu a richiedere il cambio password puoi ignorare questa email.</i>
		</body><html>";

        $email = new Phpmailer(true);

        $email->IsSMTP();
        $email->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $email->isHTML(true);
        $email->CharSet = PHPMailer::CHARSET_UTF8;
        $email->Encoding = PHPMailer::ENCODING_BASE64;
        $email->SMTPAuth = true;
        $email->Host = $this->siteConfig->host;
        $email->Port = $this->siteConfig->port;
        $email->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $email->Username = $this->siteConfig->username;
        $email->Password = $this->siteConfig->password;
        $email->setFrom($fromemail, $this->siteConfig->setFrom);
        $email->addAddress($toemail);
        $email->Subject = $subject;
        $email->Body = $mesg;
        /* $filename = base_url() . $this->siteConfig->img;
        $email->addAttachment($filename);
        $email->addAttachment(base_url() . '/favicon.ico');*/

        return $email->send();
    }

    public function success()
    {
        echo view('templates/header', ['title' => ucfirst("Inivio email riuscito")]);
        echo view('forgot_pass/success');
        echo view('templates/footer');
    }

    public function error()
    {
        echo view('templates/header', ['title' => ucfirst("Invio email non riuscito")]);
        echo view('forgot_pass/error');
        echo view('templates/footer');
    }
}
