<?php
// defined('BASEPATH') OR exit('No direct script access allowed');

class AssetHistory extends CI_Controller {
	public function index()
	{
		$this->load->model('AssetHistory_model')or die("error");
		$this->load->view('templates/header');
		$this->load->view('templates/navbarAdmin');
		$this->load->view('assethistory');
	}

	public function getAssetHistory(){
		$assetTag = $this->input->post('AssetTag');
		$AssetID = $this->db->query("SELECT * FROM assets WHERE AssetTag='$assetTag'")->row()->AssetID;
		$query = $this->db->query("SELECT * FROM loans RIGHT OUTER JOIN loansassetslink ON loans.LoanBookingID=loansassetslink.LoanBookingID RIGHT OUTER JOIN users ON loans.UserID=users.UserID RIGHT OUTER JOIN assets ON loansassetslink.AssetIDLoan=assets.AssetID WHERE loansassetslink.AssetIDLoan='$AssetID' ORDER BY loans.loanEndDate DESC");

		echo json_encode($query->result());
		return;
	}
}