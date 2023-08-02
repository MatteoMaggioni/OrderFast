<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserAccountsModel;
use App\Models\UserProfilesModel;
use App\Models\RegistrationModel;
use PHPMailer\PHPMailer\PHPMailer;
use App\Libraries\Phpmailer_library;

class forgot_username extends BaseController
{
	public function index()
    {
        $data=[];

        
        helper('form');
        
        if ($this->request->getMethod() === 'post'){
            
            $rules = [
                'codMec' => 'required'];
            $messages = [
                'codMec' => ['required' => 'Inserire codice meccanografico']
            ];
            
            if(! $this->validate($rules, $messages)){
                $data['validation'] = $this->validator;
            } else{
        
                $codMec = $this->request->getVar('codMec');
                //echo $email;

                $userProfilesModel = new UserProfilesModel();
                $userProfile = $userProfilesModel->where(["school_code"=>$codMec])->first();

                if(!$userProfile) {
                    session()->setFlashdata('error','Il codice meccanografico non risulta essere presente nel nostro database!');
                    return redirect()->to('/forgot_username/error');
                }
                
                $userAccountsModel = new UserAccountsModel();
                $userAccount = $userAccountsModel->where(["id" => $userProfile["user_id"]])->first();

                if(!$userAccount) {
                    session()->setFlashdata('error','Non &egrave; stata trovata un\'utenza valida per questo istituto!');
                    return redirect()->to('/forgot_username/error');
                }
                
                $registrationModel = new RegistrationModel();
                $schoolInformations = $registrationModel->where(["school_code"=>$codMec])->first();
                //echo $schoolInformations['school_email'];
                if(!$schoolInformations) {
                    session()->setFlashdata('error','Nel database non risulta essere stata effettuata nessuna registrazione!');
                    return redirect()->to('/forgot_username/error');
                }

                $schoolInformations = array_merge($schoolInformations,$userAccount);

                if ($this->email($schoolInformations)) {
                    session()->setFlashdata('success','Inviata email per la lettura del proprio username!');
                    return redirect()->to('forgot_username/success'); 
                } else {
                    session()->setFlashdata('error','Email non inviata a causa di un errore inaspettato!');
                    return redirect()->to('/forgot_username/error'); 
                }

            echo view('templates/header');
            echo view('index');
            echo view('templates/footer'); 
            }
        }
           
	echo view('templates/header');
        echo view('forgot_username/index', $data);
	echo view('templates/footer');
    }
            
        
    
    private function email($data){
        $fromemail=$this->siteConfig->fromemail;
        $username = $data['username'];
        $toemail = $data['school_email'];
        $subject = "Recupero username";

        $mesg = "<html><body>
		Questa email è stata inviata automaticamente a seguito della richiesta di recupero del proprio username. Nel caso non foste stati voi a richiederla potete ignorarla.
        Di seguito l'username per l'accesso al portale <a href:'https:campionatistudenteschi.it'>campionatistudenteschi.it</a>: <strong>$username</strong>
		</body><html>";


        $email = new Phpmailer_library();
        // $email = $this->phpmailer_library->load();
        
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
        $filename = base_url().$this->siteConfig->img;;
        $email->addAttachment($filename);
        $email->addAttachment(base_url().'/favicon.ico');
        
        return $email->send();
    }

	public function success(){
        echo view('templates/header', ['title' => ucfirst("Inivio email riuscito")]);
        echo view('forgot_username/success' , ['title' => ucfirst("Inivio email riuscito")]);
        echo view('templates/footer');
    }

    public function error(){
        echo view('templates/header', ['title' => ucfirst("Invio email non riuscito")]);
        echo view('forgot_username/error', ['title' => ucfirst("Inivio email riuscito")]);
        echo view('templates/footer');
    }


}