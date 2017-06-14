<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Staff extends CI_Controller {

    public function __construct() {
        parent::__construct();

        if (!$this->is_logged_in()) {
            redirect('login');
        }
    }

    public function index() {

        if ($this->session->userdata('role') == 'admin') {
            $this->load->view('admin_home');
        }

        if ($this->session->userdata('role') == 'staff') {
            $this->load->view('staff_home');
        }
    }

    public function profile() {
        $data['error'] = false;
        $data['active'] = 'profile';
        $data['id']=$this->session->userdata('id');
        
 if ($this->session->userdata('role') == 'staff') {
 $this->load->view('staffprofilepop1', $data);}

        if ($this->session->userdata('role') == 'admin') {
          $this->load->view('staffprofilepop', $data);
        }

        
    }

    public function patient_profile($id) {
        $data['id'] = $id;
        $data['error'] = false;
        $data['active'] = 'profile';
        $this->load->view('staff_patient_profile', $data);
    }

    public function add_patient() {
        $data['id'] = $this->patient_model->add_default();
        $data['error'] = false;
        $data['active'] = 'profile';
        $this->load->view('staff_patient_profile', $data);
    }

    public function delete_patient($id) {
        $data['firstname'] = $this->patient_model->get_firstname($id);
        $data['lastname'] = $this->patient_model->get_lastname($id);
        $this->patient_model->delete($id);
        $this->load->view('staff_deleted', $data);
    }

    public function update_patient_profile() {
        $id = $this->input->post('id');
        $firstname = $this->input->post('firstname');
        $lastname = $this->input->post('lastname');
        $sex = $this->input->post('sex');
        $birth = $this->input->post('birth');
        $device = $this->input->post('device');

        $data['error'] = false;
        $data['message'] = 'Patient profile succesfuly updated.';
        //update patient
        $patient = array(
            'firstname' => $firstname,
            'lastname' => $lastname,
            'sex' => $sex,
            'birth' => $birth,
            'device' => $device
        );
        $this->patient_model->update($id, $patient);

        $data['id'] = $id;
        $data['active'] = 'profile';
        $this->load->view('staff_patient_profile', $data);
    }

    public function update_profile() {
        $id = $this->input->post('id');
        $username = $this->input->post('username');
        $firstname = $this->input->post('firstname');
        $lastname = $this->input->post('lastname');
        $email = $this->input->post('email');

        $data['error'] = false;
        $data['error_message'] = '';
        $username_exists = $this->user_model->other_username_exists($username, $id);
        if ($username_exists) {
            $data['error'] = true;
            $data['error_message'] = $data['error_message'] . 'Username already exists! ';
        } else {
            $data['message'] = 'Your profile was succesfuly updated.';
            //update user
            $user = array(
                'username' => $username,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $email
            );
            $this->user_model->update($id, $user);
            $dat = array('username' => $username);
            $this->session->set_userdata($dat);
        }

        $data['active'] = 'profile';
        $this->load->view('staff_profile', $data);
    }

    public function update_password() {
        $id = $this->session->userdata('id');
        $password = $this->input->post('password');
        $this->user_model->update_password($id, $password);
        $data['error'] = false;
        $data['message'] = 'Password succesfuly changed';
        $data['active'] = 'password';
        $this->load->view('staff_profile', $data);
    }

    public function show_ekg() {
        $id = $this->input->post('id');
        $date = $this->input->post('date');
        $timestart = $this->input->post('timestart');
        $timeend = $this->input->post('timeend');
        $datetimestart = DateTime::createFromFormat('Y-m-d G:i:s', $date . ' ' . $timestart, new DateTimeZone('UTC'));
        $datetimeend = DateTime::createFromFormat('Y-m-d G:i:s', $date . ' ' . $timeend, new DateTimeZone('UTC'));
        //$datetimestart->setTimezone(new DateTimeZone('Etc/GMT+6'));
        //$datetimeend->setTimezone(new DateTimeZone('Etc/GMT+6'));
        $timestampstart = $datetimestart->getTimestamp() * 100;
        $timestampend = $datetimeend->getTimestamp() * 100;
        $ekgdata = $this->ekgdata_model->get_ekgdata($id, $timestampstart, $timestampend);
        $data['ekgdata'] = $ekgdata;
        $data['id'] = $id;
        $data['active'] = 'ekg';
        $data['error'] = false;

        $this->load->view('staff_patient_profile', $data);
    }

    private function is_logged_in() {
        return $this->session->userdata('is_logged_in');
    }

}