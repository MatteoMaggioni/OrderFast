<?php

namespace App\Controllers;

use App\Models\RegistrationModel;
use App\Libraries\Phpmailer_library;
use PHPMailer\PHPMailer\PHPMailer;

class Registration extends BaseController
{

    public function index()
    {

        $data = [];
        helper('form');
        $email_assistenza = $this->siteConfig->fromemail;
        $data = [
            'title' => ucfirst("Registrazione nuovo utente"),
            'school' =>  null,
            'email_assistenza' => $email_assistenza,
        ];

        if ($this->request->getMethod() == 'post') {
            $rules = [
                'school_id' => 'required|is_unique[school_registration.school_id]',
                'school_region' => 'required',
                /* 'firstname' => 'required|min_length[3]|max_length[25]',
                'lastname' => 'required|min_length[3]|max_length[20]',
                'fiscalcode' => 'required|min_length[16]|max_length[16]', */
                'check_informativa' => 'required'
            ];
            $schoolModel = new SchoolModel();
            $school = $schoolModel->find($this->request->getVar('school_id'));

            if ($school['email'] == 'non disponibile') {
                $school['email'] = $school['code'] . "@istruzione.it";
                $schoolModel->save($school);
            }

            $messages = [
                'school_id' => [
                    'is_unique' => 'Istituto già registrato. Il sistema ha già inviato una email a ' . $school['email'] . ', si prega di controllare sia la casella di posta in arrivo che lo spam.',
                ]
            ];

            $model = new RegistrationModel();

            if (!$this->validate($rules, $messages)) {
                $data['validation'] = $this->validator;
                // se la registrazione c'è ed è confermata
                if ($registrazione = $model->where(['school_id' => $this->request->getVar('school_id')])->first()) {
                    if ($registrazione['confirmation_date']) {
                        session()->setFlashdata('error', 'Registrazione già correttamente conclusa. Nel caso non vi ricordaste username e/o password potete recuperarli seguendo le procedure sulla home.');
                        return redirect()->to('/registration/error');
                    }
                }
            }

            $newdata = [
                'school_id' => $this->request->getVar('school_id'),
                'school_code' => $this->request->getVar('school_code'),
                //'school_email' => $this->request->getVar('school_code').'@istruzione.it',
                'school_email' => $school['email'],
                /* 'operator_firstname' => $this->request->getVar('firstname'),
                    'operator_lastname' => $this->request->getVar('lastname'),
                    'operator_fiscalcode' => $this->request->getVar('fiscalcode'), */
                'school_consenso_liberatoria' => $this->request->getVar('check_informativa'),
                // 'school_consenso_trattamento' => $this->request->getVar('check_trattamento'),
            ];

            //$expired = ($today_time >= $expire_time) && !$this->check_school_exception($newdata["school_id"]);
            // $expired = ($expired) && !$this->check_school_exception($newdata["school_id"]);
            // $closed = ($closed) && !$this->check_school_exception($newdata["school_id"]);
            // $proroga = ($proroga) && !$this->check_school_exception($newdata["school_id"]);

            // eliminazione controllo chiusura

                $id = $model->insert($newdata, true);
                if ($id > 0) {
                    $registrazione = $model->where(['id' => $id])->first();
                    $registrazione['confirmation_link'] = base_url() . '/confirmation?code=' . $registrazione['school_code'] . '&key=' . $registrazione['registration_key'];

                    $model->set_confirmation_link($registrazione);

                    if ($this->sendemail_ss($registrazione)) {
                        session()->setFlashdata('success', 'Registrazione conclusa con successo! Controlla la email ' . $school['email'] . ' per completare la registrazione. Nel caso questo indirizzo email non dovesse essere quello corretto, si prega di scrivere all\'assistenza');
                        return redirect()->to('/registration/success');
                    } else {
                        session()->setFlashdata('error', 'Registrazione conclusa con successo ma non è stato possibile inviare email di conferma! Si prega di contattare l\'assistenza per segnalare il problema, grazie.');
                        return redirect()->to('/registration/error');
                    }
                } else {
                    # invia nuovamente l'email con il link per la conferma in caso di non conferma, non c'è il controllo perchè è già fatto prima
                    if ($registrazione = $model->where(['school_id' => $this->request->getVar('school_id')])->first()) {
                        $registrazione['confirmation_link'] = base_url() . '/confirmation?code=' . $registrazione['school_code'] . '&key=' . $registrazione['registration_key'];
                        $model->set_confirmation_link($registrazione);
                        $this->sendemail_ss($registrazione);
                        
                    } else {
                        session()->setFlashdata('error', 'Registrazione non correttamente conclusa! Errore imprevisto di sistema, riprovare più tardi oppure segnalare all\'assistenza, grazie.');
                        return redirect()->to('/registration/error');
                    }
                }

        }

        echo view('templates/header', $data);
        echo view('registration/index', $data);
        echo view('templates/footer', $data);
    }

    private function sendemail_ss($data)
    {
        $fromemail = $this->siteConfig->fromemail;
        $toemail = $data['school_email'];
        $subject = "Registrazione Campionati Studenteschi";
        $emaildata = [
            /* 'dirigente' => $data['operator_firstname'] . ' ' . $data['operator_lastname'], */
            'logo' => base_url() . '/favicon.ico',
            'url' => base_url() . '/confirmation?code=' . $data['school_code'] . '&key=' . $data['registration_key'],
        ];
        $mesg = view('templates/email', $emaildata);

        $email = new Phpmailer_library();
        $email->IsSMTP();
        $email->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        // TODO: debugging del send mail
        // $email->SMTPDebug = SMTP::DEBUG_SERVER; // 2
        // $email->Debugoutput = function ($str, $level) {
        //     // file_put_contents(
        //     //   '/path/to/log/file',
        //     //   date('Y-m-d H:i:s') . "\t" . $str,
        //     //   FILE_APPEND | LOCK_EX
        //     // );
        //     echo ($str);
        //     echo ($level);
        //   };

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
        $filename = base_url() . $this->siteConfig->img;
        $email->addAttachment($filename);
        $email->addAttachment(base_url() . '/favicon.ico');

        $esito = $email->send();
        if (!$esito) {
            // echo "Mailer Error: ". $email->ErrorInfo; die();
        } else {
            // echo "Message has been sent successfully";
        }

        return $esito;
    }


    /**
     * 
     */
    public function create()
    {
        $model = new RegistrationModel();

        $data = [
            'school_id' => $this->request->getPost('school_id'),
            'school_code' => $this->request->getPost('school_code'),
        ];

        if ($this->request->getMethod() === 'post' && $this->validate([
            'school_id' => 'required|min_length[3]|max_length[255]',
            'school_code'  => 'required',
            'privacy_check0' => 'required',
            'privacy_check1' => 'required',
        ])) {
            try {
                if ($model->save([
                    'school_id' => $this->request->getPost('school_id'),
                    'school_code'  => $this->request->getPost('school_code'),
                    'school_consenso_liberatoria' => $this->request->getPost('privacy_check0'),
                    'school_consenso_trattamento' => $this->request->getPost('privacy_check1'),
                ]) == false) {
                    // echo json_encode(['error'=>'Record non iserito.']);
                } else {
                    echo json_encode(['success' => 'Record inserito correttamente.']);
                }
            } catch (\Throwable $th) {
                echo json_encode(['error' => 'Duplicazione di chiave']);
            }
            echo view('templates/header', ['title' => ucfirst("esito registrazione istituto")]);
            echo view('registration/success');
            echo view('templates/footer');
            return;
        } else if ($this->request->getMethod() === 'post') {
        }

        echo view('templates/header', ['title' => ucfirst("registrazione istituto")]);
        echo view('registration/index', $data);
        echo view('templates/footer');
    }

    public function success()
    {
        echo view('templates/header', ['title' => ucfirst("esito registrazione istituto")]);
        echo view('registration/success');
        echo view('templates/footer');
    }

    public function error()
    {
        echo view('templates/header', ['title' => ucfirst("esito registrazione istituto")]);
        echo view('registration/error');
        echo view('templates/footer');
    }

}
