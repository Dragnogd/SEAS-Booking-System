<?php
// defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends CI_Controller {
	public function index()
	{
		$this->load->model('Settings_model')or die("error");
		$this->load->view('templates/header');
		$this->load->view('templates/navbarAdmin');
		$this->load->view('settings');
	}
}
