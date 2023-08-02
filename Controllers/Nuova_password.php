<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserAccountsModel;

class Nuova_password extends Controller
{

    public function index()
    {
        $data = [];

        $usersAccountModel = new UserAccountsModel(); 
        $uri = $this->request->uri;
        $vecchiaPassword = $uri->getQuery();
        $vecchiaPassword = str_replace("=", "", $vecchiaPassword);
        $vecchiaPassword = str_replace("_", ".", $vecchiaPassword);
        $vecchiaPassword = urldecode($vecchiaPassword);

        if (!$user_id = $usersAccountModel->where(["password" => $vecchiaPassword])->first()) {
            $vecchiaPassword = $vecchiaPassword . '.';
            $user_id = $usersAccountModel->where(["password" => $vecchiaPassword])->first();
        }


        if ($this->request->getMethod() === 'post') {

            $rules['password'] = 'required|min_length[8]|max_length[255]';
            $rules['password_confirm'] = 'matches[password]';


            $messages = [
                'password' => [
                    'required' => 'Password necessaria',
                    'min_length' => 'La password deve essere lunga almeno 8 caratteri',
                ],
                'password_confirm' => [
                    'matches' => 'Le password non coincidono',
                ],
            ];

            if (!$this->validate($rules, $messages)) {
                $data['validation'] = $this->validator;
            } else {

                $nuovaPassword = $this->request->getVar('password');
                if ($user_id) {
                    if ($usersAccountModel->update($user_id['id'], ['password' => $nuovaPassword])) {
                        session()->setFlashdata('success', 'La password è stata modificata con successo!');
                        return redirect()->to('/nuova_password/success');
                    } else {
                        session()->setFlashdata('success', 'La password è stata modificata con successo!');
                        return redirect()->to('/nuova_password/success');
                    }
                } else {
                    session()->setFlashdata('error', 'User non riconosciuto!');
                    return redirect()->to('/nuova_password/error');
                }
                echo view('templates/header');
                echo view('/index');
                echo view('templates/footer');
            }
        }

        echo view('templates/header', $data);
        echo view('forgot_pass/nuova_password', $data);
        echo view('templates/footer');
    }

    public function success()
    {
        echo view('templates/header', ['title' => ucfirst("Modifica password completata")]);
        echo view('nuova_password/success');
        echo view('templates/footer');
    }

    public function error()
    {
        echo view('templates/header', ['title' => ucfirst("Modifica password non riuscita")]);
        echo view('nuova_password/error');
        echo view('templates/footer');
    }
}
