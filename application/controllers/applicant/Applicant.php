<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Applicant
 *
 * @author Mohamed Badr
 */
class Applicant extends MY_Controller {

    //put your code here
    public function index() {
        $this->load->view('header');
        $this->load->view('home/home');
        $this->load->view('footer');
    }

    /*
     * 
     * check applicant password at login 
     *     
     * 
     */

    public function login() {
        $error = '';

        if (isset($_POST['login_form'])) {
            $this->load->library('form_validation');

            /* Set validation rule for name field in the form */
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
            $this->form_validation->set_rules('password', 'password', 'required');

            if ($this->form_validation->run() == FALSE) {
                
            } else {

                $appEmail = $this->input->post('email', true);
                $appPassword = $this->input->post('password', true);
                $this->load->model('applicant_m');
                $applicant_data = $this->applicant_m->find_list_by('email', $appEmail);

                if (sizeof($applicant_data) > 0) {

                    foreach ($applicant_data as $value) {
//                        var_dump($value);
//                        die();
                        if ($this->checkPassword($value->password, $appPassword) == TRUE) {
                            if ($value->isactive == '1') {
                                $userName = $value->first_name . " " . $value->last_name;

                                $newdata = array(
                                    'username' => $userName,
                                    'email' => $value->email,
                                    'logged_in' => TRUE,
                                    'appId' => $value->id
                                );

                                $this->session->set_userdata($newdata);
                                $this->load->helper('cookie');
                                $checked = $this->input->post('rememberme');

//                            set cookies
                                if ((int) $checked == 1) {
                                    $cookie = array(
                                        'name' => "ASEUser",
                                        'value' => $value->id,
                                        'expire' => '86500',
                                    );
                                    $this->input->set_cookie($cookie);
                                }
                                get_cookie("ASEUser");
                                redirect(site_url('applicant/applicant/dashboard'));
                            } else {
                                $error = "Not Active user ,Please check your mail to activate your link ";
                            }
                        } else {
                            $error = "Wrong Password ,Please Try Again";
                        }
                    }
                }
            }
        }

        $this->load->view('header');
        $this->load->view('auth/login', array('error' => $error));
        $this->load->view('footer');
    }

    /*
     * 
     * check applicant password at login 
     *     
     * 
     */

    private function checkPassword($password, $appPassword) {
//call decryptPass to decrypt applicant password  

        $decryptedPass = $this->decryptPass($password);

        if ($decryptedPass === $appPassword) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /*
     * 
     * Applicant Dasboard
     *      
     */

    public function dashboard() {
//check if user logged_in  flag true or not 
        if ($this->session->userdata('logged_in') === TRUE) {
            $data['appId'] = $this->session->userdata('appId');
            $data['username'] = $this->session->userdata('username');
            $data['email'] = $this->session->userdata('email');
            $this->load->view('header');
            $this->load->view('home/dashboard_v', $data);
            $this->load->view('footer');
        } else {
            redirect(site_url('applicant/applicant/login'));
        }
    }

    /*
     * 
     * Encrypt Password
     *      
     */

    private function encryptPass($param) {
        $this->load->library('encryption');
        return $this->encryption->encrypt($param);
    }

    /*
     * 
     * Decrypt Password
     *   
     */

    private function decryptPass($param) {
        $this->load->library('encryption');
        return $this->encryption->decrypt($param);
    }

}
