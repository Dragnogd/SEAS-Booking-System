<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ManageAccounts extends CI_Controller {
	public function index()
	{
		$this->load->model('ManageAccounts_model')or die("error");
		$this->load->view('templates/header');
		$this->load->view('templates/navbarAdmin');
		$this->load->view('manageaccounts');
		$this->load->view('templates/footer');
	}

	public function insertNewUser()
	{
		//Insert a new asset into the database

		//List of variables we need to populate
		$forename;
		$surname;
		$email;

		//Forename
		//Can be anything the user wants this to be but must be filled out.
		if(isset($_POST['forename'])){
			if(empty($_POST['forename'])){
			  echo "The forename field must be filled out before saving";
			  return;
			}      
			else $forename = $_POST['forename'];
		} else {
			echo "Forename has not been initialised";
			return;
		}

		//Surname
		//Can be anything the user wants this to be but must be filled out.
		if(isset($_POST['surname'])){
			if(empty($_POST['surname'])){
			  echo "The surname field must be filled out before saving";
			  return;
			}      
			else $surname = $_POST['surname'];
		} else {
			echo "Surname has not been initialised";
			return;
		}

		//Email
		//Can be anything the user wants this to be but must be filled out.
		if(isset($_POST['email'])){
			if(empty($_POST['email'])){
			  echo "The email field must be filled out before saving";
			  return;
			}      
			else $email = $_POST['email'];
		} else {
			echo "Email has not been initialised";
			return;
		}
		
		//All checks on the data have passed. Now we can insert this into the database
		$query = $this->db->query("INSERT INTO users (Forename,Surname,Email) VALUES('$forename','$surname','$email')");
		if($this->db->affected_rows() > 0){
			echo "Success";
		} else {
			echo "Error inserting user into database";
		}
	}

	public function getUserID(){
		$email = $_POST['email'];
		$userID = $this->db->query("SELECT UserID FROM users WHERE email='$email'")->row()->UserID;
		echo $userID;
	}

	public function deleteUser()
	{
		//Delete an asset from the database
		//Note do not delete if outstanding loans/bookings

		//List of variables we need to populate
		$userID;

		//Check if we recieved an asset tag
		if(isset($_POST['userID'])){
			if(empty($_POST['userID'])){
			  echo "The UserID field must be filled out before saving";
			  return;
			}      
			else $userID = $_POST['userID'];
		} else {
			echo "UserID has not been initialised";
			return;
		}

		log_message('error', $userID);	

		//All checks on the data have passed. Now be remove the asset from the database
		$query = $this->db->query("DELETE FROM users WHERE UserID='" . $userID . "'");
		if($this->db->affected_rows() > 0){
			echo "Success";
		} else {
			echo "Error deleting user from database";
		}
	}

	public function getUser()
	{
		$userID;
		//Check if we recieved a loanBookingID
		if(isset($_POST['userID'])){
			if(empty($_POST['userID'])){
			  echo "The user ID field must be filled out before saving";
			  return;
			}      
			else $userID = $_POST['userID'];
		} else {
			echo "User ID has not been initialised";
			return;
		}

		//Fetches information about a particular asset and returns this as a JSON array
		$query = $this->db->query("SELECT * FROM users WHERE UserID='$userID'")->row();

		echo json_encode($query);
	}	

	public function updateUser()
	{
		//Update a particular asset into the database

		//List of variables we need to populate
		$userID;
		$forename;
		$surname;
		$email;
		
		//UserID
		//Read only property so user shouldn't have altered it
		if(isset($_POST['userID'])){
			if(empty($_POST['userID'])){
			  echo "The UserID field must be filled out before saving";
			  return;
			}      
			else $userID = $_POST['userID'];
		} else {
			echo "UserID has not been initialised";
			return;
		}

		//Forename
		//Can be anything the user wants this to be but must be filled out.
		if(isset($_POST['forename'])){
			if(empty($_POST['forename'])){
			  echo "The forename field must be filled out before saving";
			  return;
			}      
			else $forename = $_POST['forename'];
		} else {
			echo "forename has not been initialised";
			return;
		}

		//Surname
		//Can be anything the user wants this to be but must be filled out.
		if(isset($_POST['surname'])){
			if(empty($_POST['surname'])){
			  echo "The surname field must be filled out before saving";
			  return;
			}      
			else $surname = $_POST['surname'];
		} else {
			echo "Surname has not been initialised";
			return;
		}

		//Email
		//Can be anything the user wants this to be but must be filled out.
		if(isset($_POST['email'])){
			if(empty($_POST['email'])){
			  echo "The email field must be filled out before saving";
			  return;
			}      
			else $email = $_POST['email'];
		} else {
			echo "Email has not been initialised";
			return;
		}	
		
		//All checks on the data have passed. Now we can insert this into the database
		$query = $this->db->query("UPDATE users SET Forename='$forename', Surname='$surname', Email='$email' WHERE UserID='$userID'");
		if($this->db->affected_rows() > 0){
			echo "Success";
		} else {
			echo "Error updating users details";
		}

	}
}
