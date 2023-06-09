<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Access extends CI_Controller {

	function load_page($content, $data_to_pass = null)
	{
		$this->load->view('USER/header.php');
		$this->load->view("{$content}", $data_to_pass);
	}

	public function index()
	{
		$this->load_page('USER/login.php');
	}

	function validate_credentials($username, $password){
        $this -> db -> select('*'); 
        $this -> db -> from('users');
        $this -> db -> where('username', $username); 
        $query = $this->db->get();
        if ($query && $query->num_rows() == 1) {
			if (password_verify($password, $query->result()[0]->password)){    
			// if (1==1){        
			return $query->result()[0];
			} else {
				return null;
			}
        }else{
            return null;
        }
    }

	public function registrazione(){
		$this->load_page('USER/registrazione.php');

	}

	
	function login(){
        $this->load->library('form_validation');
        $this->form_validation->set_rules('username', 'Username', 'trim|required');
		$this->form_validation->set_rules('password', 'Password', 'trim|required');
        if ($this->form_validation->run() == FALSE){
            //se c'è qualche errore nella validazione del form
            $this->index();
        } else {
            //se la validazione è ok
			$user = $this->validate_credentials($this->input->post('username'), $this->input->post('password'));
			if ($user){
				$data = array(
					'id' => $user -> id,
					'username' => $user -> username,
					'fullname' => $user->fullname,
					'logged-in' => true,
					'azienda' => $user->azienda,
				);
				$this->session->set_userdata($data); //definisco le varibili di sessione
				redirect('Tool');
			} else{
				redirect('access');
			}
        }
    }

	function registra(){
		$this->load->library('form_validation');
        $this->form_validation->set_rules('username', 'Username', 'trim|required');
		$this->form_validation->set_rules('password', 'Password', 'trim|required');
        if ($this->form_validation->run() == FALSE){
            //se c'è qualche errore nella validazione del form
            //$this->index();
			echo "errore validazione";
        } else {
            //se la validazione è ok
			$form_username = html_escape($this->input->post("username"));
			$form_password = html_escape($this->input->post("password"));
			$form_fullname = html_escape($this->input->post("fullname"));
			$form_azienda = html_escape($this->input->post("business"));

			$new_user = array(
				"username" => $form_username,
				"password" =>  password_hash($form_password,PASSWORD_DEFAULT),
				"fullname" => $form_fullname,
				"azienda" => $form_azienda

			);
			
			$insert = $this->crud_model->create_object("users", $new_user);
			
			if($insert){
				redirect('access');
			} else {
				show_404();
			}
        }
	}


	function check_session(){
		$logged_in = $this->session->userdata('logged-in'); 
        if (!isset($logged_in) || $logged_in != true){
            //utente non loggato
            return false;
        }else{
			//utente loggato
			return true;
        }
	}

	function logout(){
		$this->session->sess_destroy();
		redirect('access');

	}
}
