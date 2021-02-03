<?php
// defined('BASEPATH') OR exit('No direct script access allowed');

class ManageLoans extends CI_Controller {
	public function index()
	{
		$this->load->model('ManageLoans_model')or die("error");
		$this->load->view('templates/header');
		$this->load->view('templates/navbarAdmin');
		$this->load->view('manageloans');
		$this->load->view('templates/footer');
	}

	public function insertBooking()
	{
		//List of all the data for the particular booking. We need to verify the booking is ok before

		//Used to send return message back to view
		$returnMessage = new stdClass();

		//Set variables for all posted data
		$assets = $this->input->post('assets', TRUE);
		$startDate = $this->input->post('loanStartDate');
		$endDate = $this->input->post('loanEndDate');
		$startPeriod = $this->input->post('loanStartPeriod');
		$endPeriod = $this->input->post('loanEndPeriod');
		$additionalDetails = $this->input->post('additionalDetails', TRUE);
		$bookingPeriod = $this->input->post('bookingPeriod');
		$bookingType = $this->input->post('bookingType');
		$user = $this->input->post('selectedUser', TRUE);
		$isReservation = $this->input->post('reservation');

		//1.) Lets check all the posted variables are what we expected them to be
		if(!(isset($user))){
			//We have recieved no user
			$returnMessage->Severity = "Warning";
			$returnMessage->Message = "Please select the user who is booking this equipment";
			echo json_encode($returnMessage);
			return false;
		}
		if(!(isset($bookingPeriod))){
			//We have recieved no booking period
			$returnMessage->Severity = "Warning";
			$returnMessage->Message = "Please select either a single or multi day booking";
			echo json_encode($returnMessage);
			return false;
		}
		if(!(isset($bookingType))){
			//We have recieved no booking type
			$returnMessage->Severity = "Warning";
			$returnMessage->Message = "Please select either a loan or a setup";
			echo json_encode($returnMessage);
			return false;
		}
		if(!(isset($assets))){
			//We have recieved no assets. This is no good :(
			$returnMessage->Severity = "Warning";
			$returnMessage->Message = "Please select some items to loan out";
			echo json_encode($returnMessage);
			return false;
		}
		if(!(isset($startDate))){
			//No start date recieved
			$returnMessage->Severity = "Warning";
			$returnMessage->Message = "Please select a Loan Start Date";
			echo json_encode($returnMessage);
			return false;
		}
		if(!(isset($endDate))){
			//No end date recieved
			$returnMessage->Severity = "Warning";
			$returnMessage->Message = "Please select a Loan End Date";
			echo json_encode($returnMessage);
			return false;
		}
		if(!(isset($startPeriod))){
			//No end date recieved
			$returnMessage->Severity = "Warning";
			$returnMessage->Message = "Please select a Loan Start Period";
			echo json_encode($returnMessage);
			return false;
		}
		if(!(isset($endPeriod))){
			//No end date recieved
			$returnMessage->Severity = "Warning";
			$returnMessage->Message = "Please select a Loan End Period";
			echo json_encode($returnMessage);
			return false;
		}

		//2.) We have recieved all the data we were expecting. Next we need to check if the assets are still avaliable or not.
		$allAssetsAvaliable = true; //Lets assume all assets are avaliable until proven otherwise
		$assetsUnavaliable = []; //Store any assets which are unavaliable so we can remove from users shopping cart

		if($bookingPeriod == "loanTypeSingle"){
			foreach($assets as $assetID){
				$query2 = $this->db->query("SELECT * FROM loans RIGHT OUTER JOIN loansassetslink ON loans.LoanBookingID=loansassetslink.LoanBookingID");
				foreach($query2->result() as $row2){
					if($row2->AssetIDLoan == $assetID && $row2->LoanStatus != "Completed"){
						//We have found a booking for this asset. Now check whether it clashes with the booking trying to be made
						$assetAvaliableBefore = true;
						$assetAvaliableAfter = true;
						$assetAvaliableDate = true;

						//Check if the asset has been loaned out from "Book Equipment"
						if($row2->LoanStartDate == $startDate && $row2->LoanEndDate == $endDate){
							log_message('error', "Asset has a booking today");
							if($startPeriod < $row2->LoanStartPeriod  && $endPeriod < $row2->LoanStartPeriod){
								log_message('error', $startPeriod . " is less than " . $row2->LoanStartPeriod  . " and " . $endPeriod . " is less than " . $row2->LoanStartPeriod);
								log_message('error', "Asset is avaliable for booking before start period");
							} else{
								log_message('error', $startPeriod . " is less than " . $row2->LoanStartPeriod  . " and " . $endPeriod . " is less than " . $row2->LoanStartPeriod);
								log_message('error', "Asset is not avaliable for booking before start period");
								$assetAvaliableBefore = false;
							}
							if($startPeriod > $row2->LoanEndPeriod && $endPeriod > $row2->LoanEndPeriod) {
								log_message('error', $startPeriod . " is greater than " . $row2->LoanEndPeriod  . " and " . $endPeriod . " is greater than " . $row2->LoanEndPeriod);
								log_message('error', "Asset is avaliable for booking after start period");
							} else{
								log_message('error', $startPeriod . " is greater than " . $row2->LoanEndPeriod  . " and " . $endPeriod . " is greater than " . $row2->LoanEndPeriod);
								log_message('error', "Asset is not avaliable for booking after start period");
								$assetAvaliableAfter = false;
							}
						} else {
							//If the Loan spans multiple days then see if the asset is avaliable
							if($startDate < $row2->LoanStartDate || $startDate > $row2->LoanEndDate){
								//Asset avaliable
								log_message('error', "Asset avaliable for todays date");
							} else {
								log_message('error', "Asset is not avaliable for todays date");
								$assetAvaliableDate = false;
							}
						}

						//Remove asset if it currently in use at selected time
						if(($assetAvaliableBefore == false && $assetAvaliableAfter == false) || $assetAvaliableDate == false){
							$allAssetsAvaliable = false;
							log_message('error', "Removing Asset since its not avaliable");
							$query3 = $this->db->query("SELECT * FROM assets WHERE AssetID=$assetID");
							foreach($query3->result() as $row){
								array_push($assetsUnavaliable, $row->AssetName);
							}
						}
					}
				}
			}
		} elseif($bookingPeriod == "loanTypeMulti"){
			foreach($assets as $assetID){
				$query2 = $this->db->query("SELECT * FROM loans RIGHT OUTER JOIN loansassetslink ON loans.LoanBookingID=loansassetslink.LoanBookingID");
				foreach($query2->result() as $row2){
					if($row2->AssetIDLoan == $assetID && $row2->LoanStatus != "Completed"){
						log_message('error', "Found Asset: " . $row2->AssetIDLoan);
						$assetAvaliableBefore = true;
						$assetAvaliableAfter = true;
						$assetAvaliableDate = true;

						//If the Loan start date and loan end date are the same then check periods to see if asset is avaliable
						if($row2->LoanStartDate == $startDate && $row2->LoanEndDate == $endDate){
							//Since this loan is for a whole day then mark as unavalaible
							$assetAvaliableDate = false;
						} else {
							//If the Loan spans multiple days then see if the asset is avaliable
							if($startDate < $row2->LoanStartDate && $endDate < $row2->LoanStartDate){
								//Asset avaliable
								//log_message('error', $loanStartDate . " is less than " . $row2->LoanStartDate  . " and " . $loanEndDate . " is less than " . $row2->LoanStartDate);
								log_message('error', "Asset avaliable for todays date");
							} else {
								//log_message('error', $loanStartDate . " is less than " . $row2->LoanStartDate  . " and " . $loanEndDate . " is less than " . $row2->LoanStartDate);
								log_message('error', "Asset is not avaliable for todays date");
								$assetAvaliableBefore = false;
							}
							if($startDate > $row2->LoanEndDate && $endDate > $row2->LoanEndDate){
								//Asset avaliable
								//log_message('error', $loanStartDate . " is less than " . $row2->LoanEndDate  . " and " . $loanEndDate . " is less than " . $row2->LoanEndDate);
								log_message('error', "Asset avaliable for todays date");
							} else {
								//log_message('error', $loanStartDate . " is less than " . $row2->LoanEndDate  . " and " . $loanEndDate . " is less than " . $row2->LoanEndDate);
								log_message('error', "Asset is not avaliable for todays date");
								$assetAvaliableAfter = false;
							}
						}

						//Remove asset if it currently in use at selected time
						if(($assetAvaliableBefore == false && $assetAvaliableAfter == false) || $assetAvaliableDate == false){
							$allAssetsAvaliable = false;
							log_message('error', "Removing Asset since its not avaliable");
							$query3 = $this->db->query("SELECT * FROM assets WHERE AssetID=$assetID");
							foreach($query3->result() as $row){
								array_push($assetsUnavaliable, $row->AssetName);
							}
						}
					}
				}
			}
		} else {
			$returnMessage->Severity = "Danger";
			$returnMessage->Message = "Cannot detect Loan Booking Period. Booking was not made";
			echo json_encode($returnMessage);
			return false;
		}

		//3.) Awesome all assets are still avaliable. Lets insert the booking into the table now.

		if($allAssetsAvaliable){
			//Insert a new booking into the loans table
			$userID = $user;
			$loanStatus = "";
			if($isReservation == "true"){
				$loanStatus = "Reserved";
			}else{
				$loanStatus = "Booked";
			}
			$query = $this->db->query("INSERT INTO loans (UserID,LoanStartDate,LoanEndDate,LoanStatus,AdditionalNotes,LoanStartPeriod,LoanEndPeriod,LoanType) VALUES ($userID, STR_TO_DATE('$startDate', '%Y-%m-%d'), STR_TO_DATE('$endDate', '%Y-%m-%d'),'$loanStatus',{$this->db->escape($additionalDetails)},'$startPeriod','$endPeriod','$bookingType')");

			//Insert assets into assets table
			//To do this we need to get the booking id from the previous query
			$loanBookingID = $this->db->insert_id();
			//Loop through each asset and add to loansssetslink table
			foreach($assets as $assetID){
				$query = $this->db->query("INSERT INTO loansassetslink (LoanBookingID, AssetIDLoan) VALUES ($loanBookingID, $assetID)");
			}

			//Get list of Asset Names
			$assetNames = "";
			foreach($assets as $assetID){
				$currentAssetName = $this->db->query("SELECT AssetName,AssetTag From assets WHERE AssetID=$assetID");
				$row = $currentAssetName->row();
				$assetNames .= $row->AssetName . ' (' . $row->AssetTag . ')<br>';
			}

			//Get email address of user we want to send too
			$query = $this->db->query("SELECT Email FROM users where UserID='$user'");
			$email = $query->row()->Email;

			log_message("error", "SENDING TOO");
			log_message("error", $email);

			//Send different email depending on whether its a loan or a setup
			if($bookingType == "bookingLoan"){
			} else if($bookingType == "bookingSetup"){
				$this->email->message('<h1>Booking Details:</h1>Please find below the details of your setup<br><h2>Setup Date & Time</h2>Start Date: ' . $startDate . '<br>End Date: ' . $endDate . '<br>Start Period: ' . $startPeriod . '<br>End Period: ' . $endPeriod . '<br><h2>Assets & Location</h2>' . $assetNames . "<h2>Additional Details</h2>" . $additionalDetails);
			}

			if($bookingType == "bookingSetup"){
				//Create Calendar Item for Outlook
				//'2015-05-12 20:00:00'
				$event = array(
					'id' => 1,
					'title' => 'IT Department Booking #' . $loanBookingID,
					'address' => "",
					'description' => 'Booking Details:\nPlease find below the details of your setup\n\nSetup Date & Time\nStart Date: ' . $startDate . '\nEnd Date: ' . $endDate . '\nStart Period: ' . $startPeriod . '\nEnd Period: ' . $endPeriod . '\n\nAssets & Location\n' . $assetNames . '\n\nAdditional Details\n' . $additionalDetails,
					'datestart' => date('Ymd\THis', strtotime($startDate . " " . $startBookingTime)),
					'dateend' => date('Ymd\THis', strtotime($endDate . " " . $endBookingTime)),
					'address' => ""
				);

				// Build the ics file
				$ical = "BEGIN:VCALENDAR\r\n";
				$ical .= "VERSION:2.0\r\n";
				$ical .= "PRODID:-//hacksw/handcal//NONSGML v1.0//EN\r\n";
				$ical .= "CALSCALE:GREGORIAN\r\n";
				$ical .= "BEGIN:VEVENT\r\n";
				$ical .= "DTEND:" . $event['dateend'] . "\r\n";
				$ical .= "UID:" . md5($event['title']) . "\r\n";
				$ical .= "DTSTAMP:" . time() . "\r\n";
				$ical .= "LOCATION:" . addslashes($event['address']) . "\r\n";
				$ical .= "DESCRIPTION:" . $event['description'] . "\r\n";
				$ical .= "URL;VALUE=URI:http://bookings/" . $event['id'] . "\r\n";
				$ical .= "SUMMARY:" . addslashes($event['title']) . "\r\n";
				$ical .= "DTSTART:" . $event['datestart'] . "\r\n";
				$ical .= "END:VEVENT\r\n";
				$ical .= "END:VCALENDAR\r\n";

				$this->email->attach($ical, 'attachment', 'event.ics', 'text/calendar');
			}

			if($this->config->item('enableEmails')){
				if($isReservation == "true"){
					$page_data['TYPE'] = "Reservation";
					$page_data["MESSAGE"] = "The following reservation has been created";
				}else{
					$page_data['TYPE'] = "Booked";
					$page_data["MESSAGE"] = "The following booking has been created";
				}

				//Generate Email
				$page_data["ADDITIONALDETAILS"] = $additionalDetails;
				$page_data["BOOKINGID"] = $loanBookingID;
				$page_data["BOOKINGDATE"] = "$startDate - $endDate";
				$page_data["BOOKINGTIME"] = "$startPeriod - $endPeriod";
				$page_data["ASSETS"] = $assetNames;
				$page_data["ACTION"] = "Created";


				$emailCode = $this->load->view('templates/email', $page_data, TRUE);

				$sent;
				if($isReservation == "true"){
					$sent = $this->sendEmail($email, $this->config->item('from'), $this->config->item('cc'), $emailCode, 'IT Department Reservation #' . $loanBookingID);
				}else{
					$sent = $this->sendEmail($email, $this->config->item('from'), $this->config->item('cc'), $emailCode, 'IT Department Booking #' . $loanBookingID);
				}

				if ($sent)
				{
					$returnMessage->Severity = "Success";
					$returnMessage->Message = "Booking Was Successfull";
				} else {
					$returnMessage->Severity = "Danger";
					$returnMessage->Message = $this->email->print_debugger();
				}
			}else{
				$returnMessage->Severity = "Success";
				$returnMessage->Message = "Booking Was Successfull";
			}

			echo json_encode($returnMessage);
			return false;
		} else {
			//If assets are not avaliable then return which assets are not avalaible and ask user to alter their booking
			$returnMessage->Severity = "Warning";
			$returnMessage->Message = $assetsUnavaliable;
			echo json_encode($returnMessage);
			return false;
		}
	}

	public function getAvaliableEquipementSingle()
	{
		$loanDate = $_POST["loanDate"];
		$startPeriod = $_POST["startPeriod"];
		$endPeriod = $_POST["endPeriod"];

		$avaliableAssets = array();

		$query = $this->db->query("SELECT * FROM assets ORDER BY AssetName ASC,AssetTag");
		foreach($query->result() as $row){
			array_push($avaliableAssets, $row->AssetID);
			$query2 = $this->db->query("SELECT * FROM loans RIGHT OUTER JOIN loansassetslink ON loans.LoanBookingID=loansassetslink.LoanBookingID");
			foreach($query2->result() as $row2){
				//Now we need to decide whether the current asset is in use for the specified time period
				//The user can book a loan if the start and end period are before or after any current loans.
				//Do not check loans which have been completed
				if($row2->AssetIDLoan == $row->AssetID && $row2->LoanStatus != "Completed"){
					log_message('error', "Found Asset: " . $row->AssetID);
					$assetAvaliableBefore = true;
					$assetAvaliableAfter = true;
					$assetAvaliableDate = true;

					//Check if the asset has been loaned out from "Book Equipment"
					if($row2->LoanStartDate == $loanDate && $row2->LoanEndDate == $loanDate){
						log_message('error', "Asset has a booking today");
						if($startPeriod < $row2->LoanStartPeriod  && $endPeriod < $row2->LoanStartPeriod){
							log_message('error', $startPeriod . " is less than " . $row2->LoanStartPeriod  . " and " . $endPeriod . " is less than " . $row2->LoanStartPeriod);
							log_message('error', "Asset is avaliable for booking before start period");
						} else{
							log_message('error', $startPeriod . " is less than " . $row2->LoanStartPeriod  . " and " . $endPeriod . " is less than " . $row2->LoanStartPeriod);
							log_message('error', "Asset is not avaliable for booking before start period");
							$assetAvaliableBefore = false;
						}
						if($startPeriod > $row2->LoanEndPeriod && $endPeriod > $row2->LoanEndPeriod) {
							log_message('error', $startPeriod . " is greater than " . $row2->LoanEndPeriod  . " and " . $endPeriod . " is greater than " . $row2->LoanEndPeriod);
							log_message('error', "Asset is avaliable for booking after start period");
						} else{
							log_message('error', $startPeriod . " is greater than " . $row2->LoanEndPeriod  . " and " . $endPeriod . " is greater than " . $row2->LoanEndPeriod);
							log_message('error', "Asset is not avaliable for booking after start period");
							$assetAvaliableAfter = false;
						}
					} else {
						//If the Loan spans multiple days then see if the asset is avaliable
						if($loanDate < $row2->LoanStartDate || $loanDate > $row2->LoanEndDate){
							//Asset avaliable
							log_message('error', "Asset avaliable for todays date");
						} else {
							log_message('error', "Asset is not avaliable for todays date");
							$assetAvaliableDate = false;
						}
					}

					//If asset is overdue then mark as not avaliable
					if($row2->LoanStatus == "Overdue"){
						log_message('error', "Asset is not avaliable for as it is overdue from a previous booking");
						$assetAvaliableDate = false;
					}

					//Remove asset if it currently in use at selected time
					if(($assetAvaliableBefore == false && $assetAvaliableAfter == false) || $assetAvaliableDate == false){
						log_message('error', "Removing Asset since its not avaliable");
						foreach($avaliableAssets as $assetID){
							if($assetID == $row->AssetID){
								unset($avaliableAssets[array_search($assetID, $avaliableAssets)]);
								$avaliableAssets = array_values($avaliableAssets);
							}
						}
					}
				}
			}
		}

		$assetsJSON = array();

		foreach($avaliableAssets as $asset){
			$query = $this->db->query("SELECT * FROM assets WHERE AssetID=$asset");
			foreach($query->result() as $row){
				$assetJSON = new stdClass();
				$assetJSON->AssetName = $row->AssetName;
				$assetJSON->AssetID = $row->AssetID;
				$assetJSON->AssetDescription = $row->AssetDescription;
				$assetJSON->AssetTag = $row->AssetTag;
				array_push($assetsJSON, $assetJSON);
			}
		}

		echo json_encode($assetsJSON);
	}

	public function getAvaliableEquipementMulti()
	{
		$loanStartDate = $_POST["startDate"];
		$loanEndDate = $_POST["endDate"];

		$avaliableAssets = array();

		$query = $this->db->query("SELECT * FROM assets ORDER BY AssetName ASC,AssetTag");

		foreach($query->result() as $row){
			array_push($avaliableAssets, $row->AssetID);
			$query2 = $this->db->query("SELECT * FROM loans RIGHT OUTER JOIN loansassetslink ON loans.LoanBookingID=loansassetslink.LoanBookingID");
			foreach($query2->result() as $row2){
				if($row2->AssetIDLoan == $row->AssetID && $row2->LoanStatus != "Completed"){
					log_message('error', "Found Asset: " . $row->AssetID);
					$assetAvaliableBefore = true;
					$assetAvaliableAfter = true;
					$assetAvaliableDate = true;

					//If the Loan start date and loan end date are the same then check periods to see if asset is avaliable
					if($row2->LoanStartDate == $loanStartDate && $row2->LoanEndDate == $loanStartDate){
						//Since this loan is for a whole day then mark as unavalaible
						$assetAvaliableDate = false;
					} else {
						//If the Loan spans multiple days then see if the asset is avaliable
						if($loanStartDate < $row2->LoanStartDate && $loanEndDate < $row2->LoanStartDate){
							//Asset avaliable
							//log_message('error', $loanStartDate . " is less than " . $row2->LoanStartDate  . " and " . $loanEndDate . " is less than " . $row2->LoanStartDate);
							log_message('error', "Asset avaliable for todays date");
						} else {
							//log_message('error', $loanStartDate . " is less than " . $row2->LoanStartDate  . " and " . $loanEndDate . " is less than " . $row2->LoanStartDate);
							log_message('error', "Asset is not avaliable for todays date");
							$assetAvaliableBefore = false;
						}
						if($loanStartDate > $row2->LoanEndDate && $loanEndDate > $row2->LoanEndDate){
							//Asset avaliable
							//log_message('error', $loanStartDate . " is less than " . $row2->LoanEndDate  . " and " . $loanEndDate . " is less than " . $row2->LoanEndDate);
							log_message('error', "Asset avaliable for todays date");
						} else {
							//log_message('error', $loanStartDate . " is less than " . $row2->LoanEndDate  . " and " . $loanEndDate . " is less than " . $row2->LoanEndDate);
							log_message('error', "Asset is not avaliable for todays date");
							$assetAvaliableAfter = false;
						}
					}

					//If asset is overdue then mark as not avaliable
					if($row2->LoanStatus == "Overdue"){
						log_message('error', "Asset is not avaliable for as it is overdue from a previous booking");
						$assetAvaliableDate = false;
					}

					//Remove asset if it currently in use at selected time
					if(($assetAvaliableBefore == false && $assetAvaliableAfter == false) || $assetAvaliableDate == false){
						log_message('error', "Removing Asset since its not avaliable");
						foreach($avaliableAssets as $assetID){
							if($assetID == $row->AssetID){
								unset($avaliableAssets[array_search($assetID, $avaliableAssets)]);
								$avaliableAssets = array_values($avaliableAssets);
							}
						}
					}
				}
			}
		}

		$assetsJSON = array();

		foreach($avaliableAssets as $asset){
			$query = $this->db->query("SELECT * FROM assets WHERE AssetID=$asset");
			foreach($query->result() as $row){
				$assetJSON = new stdClass();
				$assetJSON->AssetName = $row->AssetName;
				$assetJSON->AssetID = $row->AssetID;
				$assetJSON->AssetDescription = $row->AssetDescription;
				$assetJSON->AssetTag = $row->AssetTag;
				array_push($assetsJSON, $assetJSON);
			}
		}

		echo json_encode($assetsJSON);
	}

	public function bookReservation()
	{
		//List of variables we need to populate
		$loanID;
		$returnMessage = "";

		//Asset ID
		if(isset($_POST['loanID'])){
			if(empty($_POST['loanID'])){
				echo "The Loan ID field must be filled out before saving";
				return;
			}
			else $loanID = $_POST['loanID'];
		} else {
			echo "Loan ID has not been initialised";
			return;
		}

		//All checks on the data have passed. We want to mark this booking as booked
		$query = $this->db->query("UPDATE loans SET LoanStatus='Booked' WHERE LoanBookingID='$loanID'");

		if($this->db->affected_rows() > 0){
			echo "Success";
		} else {
			echo "Error removing Loan from loansassetslink";
		}

		//Get email address of user we want to send too
		$details = $this->db->query("SELECT * FROM loans WHERE LoanBookingID='$loanID'")->row();
		$email = $this->db->query("SELECT Email FROM users WHERE UserID='{$details->UserID}'")->row()->Email;

		$assetNames = "";
		$query3 = $this->db->query("SELECT AssetIDLoan FROM loansassetslink WHERE LoanBookingID='$loanID'");
		foreach($query3->result() as $row){
			//Get all assets from assets table
			$query4 = $this->db->query("SELECT AssetName,AssetTag FROM assets WHERE AssetID='{$row->AssetIDLoan}'");
			foreach($query4->result() as $row2){
				$assetNames .= $row2->AssetName . ' (' . $row2->AssetTag . ')<br>';
			}
		}

		if($this->config->item('enableEmails')){
			//Generate Email
			$page_data["ADDITIONALDETAILS"] = $details->AdditionalNotes;
			$page_data["BOOKINGID"] = $loanID;
			$page_data["BOOKINGDATE"] = "$details->LoanStartDate - $details->LoanEndDate";
			$page_data["BOOKINGTIME"] = "$details->LoanStartPeriod - $details->LoanEndPeriod";
			$page_data["ASSETS"] = $assetNames;
			$page_data["ACTION"] = "Created";
			$page_data['TYPE'] = "Booking";
			$page_data["MESSAGE"] = "The following booking has been created";

			$emailCode = $this->load->view('templates/email', $page_data, TRUE);

			$sent = $this->sendEmail($email, $this->config->item('from'), $this->config->item('cc'), $emailCode, 'IT Department Booking #' . $loanID);

			if ($sent)
			{
				$returnMessage = "Success";
			} else {
				$returnMessage = "Error";
			}
		}else{
			$returnMessage = "Success";
		}
	}

	public function cancelReservation()
	{
		//List of variables we need to populate
		$loanID;
		$returnMessage = "";

		//Asset ID
		if(isset($_POST['loanID'])){
			if(empty($_POST['loanID'])){
				echo "The Loan ID field must be filled out before saving";
				return;
			}
			else $loanID = $_POST['loanID'];
		} else {
			echo "Loan ID has not been initialised";
			return;
		}

		//Get email address of user we want to send too
		$details = $this->db->query("SELECT * FROM loans WHERE LoanBookingID='$loanID'")->row();
		$email = $this->db->query("SELECT Email FROM users WHERE UserID='{$details->UserID}'")->row()->Email;

		$assetNames = "";
		$query3 = $this->db->query("SELECT AssetIDLoan FROM loansassetslink WHERE LoanBookingID='$loanID'");
		foreach($query3->result() as $row){
			//Get all assets from assets table
			$query4 = $this->db->query("SELECT AssetName,AssetTag FROM assets WHERE AssetID='{$row->AssetIDLoan}'");
			foreach($query4->result() as $row2){
				$assetNames .= $row2->AssetName . ' (' . $row2->AssetTag . ')<br>';
			}
		}

		//Generate Email
		if($this->config->item('enableEmails')){
			//Generate Email
			$page_data["ADDITIONALDETAILS"] = $details->AdditionalNotes;
			$page_data["BOOKINGID"] = $loanID;
			$page_data["BOOKINGDATE"] = "$details->LoanStartDate - $details->LoanEndDate";
			$page_data["BOOKINGTIME"] = "$details->LoanStartPeriod - $details->LoanEndPeriod";
			$page_data["ASSETS"] = $assetNames;
			$page_data["ACTION"] = "Cancelled";
			$page_data['TYPE'] = "Reservation";
			$page_data["MESSAGE"] = "The following reservation has been cancelled";

			$emailCode = $this->load->view('templates/email', $page_data, TRUE);

			$sent = $this->sendEmail($email, $this->config->item('from'), $this->config->item('cc'), $emailCode, 'IT Department Reservation #' . $loanID . " Cancelled");

			if ($sent)
			{
				$returnMessage = "Success";
			} else {
				$returnMessage = "Error";
			}
		}else{
			$returnMessage = "Success";
		}

		//All checks on the data have passed. Delete the reservation
		$query = $this->db->query("DELETE FROM loans WHERE LoanBookingID='$loanID'");
		$query = $this->db->query("DELETE FROM loansassetslink WHERE LoanBookingID='$loanID'");

		if($this->db->affected_rows() > 0){
			echo "Success";
		} else {
			echo "Error removing Loan from loansassetslink";
		}
	}

	public function completeLoan()
	{
		//List of variables we need to populate
		$loanID;
		$returnMessage = "";

		//Asset ID
		if(isset($_POST['loanID'])){
			if(empty($_POST['loanID'])){
				echo "The Loan ID field must be filled out before saving";
				return;
			}
			else $loanID = $_POST['loanID'];
		} else {
			echo "Loan ID has not been initialised";
			return;
		}

		//All checks on the data have passed. We want to mark this booking as completed rather then delete so we can use for future reference
		$query = $this->db->query("UPDATE loans SET LoanStatus='Completed' WHERE LoanBookingID='$loanID'");

		if($this->db->affected_rows() > 0){
			echo "Success";
		} else {
			echo "Error removing Loan from loansassetslink";
		}

		//Get email address of user we want to send too
		$details = $this->db->query("SELECT * FROM loans WHERE LoanBookingID='$loanID'")->row();
		$email = $this->db->query("SELECT Email FROM users WHERE UserID='{$details->UserID}'")->row()->Email;

		$assetNames = "";
		$query3 = $this->db->query("SELECT AssetIDLoan FROM loansassetslink WHERE LoanBookingID='$loanID'");
		foreach($query3->result() as $row){
			//Get all assets from assets table
			$query4 = $this->db->query("SELECT AssetName,AssetTag FROM assets WHERE AssetID='{$row->AssetIDLoan}'");
			foreach($query4->result() as $row2){
				$assetNames .= $row2->AssetName . ' (' . $row2->AssetTag . ')<br>';
			}
		}

		//Generate Email
		if($this->config->item('enableEmails')){
			//Generate Email
			$page_data["ADDITIONALDETAILS"] = $details->AdditionalNotes;
			$page_data["BOOKINGID"] = $loanID;
			$page_data["BOOKINGDATE"] = "$details->LoanStartDate - $details->LoanEndDate";
			$page_data["BOOKINGTIME"] = "$details->LoanStartPeriod - $details->LoanEndPeriod";
			$page_data["ASSETS"] = $assetNames;
			$page_data["ACTION"] = "Completed";
			$page_data['TYPE'] = "Booking";
			$page_data["MESSAGE"] = "The following booking has been completed";

			$emailCode = $this->load->view('templates/email', $page_data, TRUE);

			$sent = $this->sendEmail($email, $this->config->item('from'), $this->config->item('cc'), $emailCode, 'IT Department Booking #' . $loanID . " Completed");

			if ($sent)
			{
				$returnMessage = "Success";
			} else {
				$returnMessage = "Error";
			}
		}else{
			$returnMessage = "Success";
		}
	}

	public function getLoan()
	{
		$loanBookingID;
		//Check if we recieved a loanBookingID
		if(isset($_POST['loanBookingID'])){
			if(empty($_POST['loanBookingID'])){
			  echo "The Loan Booking ID field must be filled out before saving";
			  return;
			}
			else $assetTag = $_POST['loanBookingID'];
		} else {
			echo "Loan Booking ID has not been initialised";
			return;
		}

		//Fetches information about a particular asset and returns this as a JSON array
		$query = $this->db->query("SELECT AssetName,AssetDescription,AssetTag,AssetLocation FROM assets WHERE AssetTag='$assetTag'")->row();

		echo json_encode($query);
	}

	public function getLoanInfo()
	{
		$loanID;
		//Check if we recieved an asset tag
		if(isset($_POST['loanID'])){
			if(empty($_POST['loanID'])){
			  echo "The Loan ID field must be filled out before saving";
			  return;
			}
			else $loanID = $_POST['loanID'];
		} else {
			echo "Loan ID has not been initialised";
			return;
		}

		$query = $this->db->query("SELECT * FROM loans WHERE LoanBookingID='$loanID'")->row();

		echo json_encode($query);
	}

	public function getOverdueBookings(){
		log_message('error', "Inside Overdue");
		$query = $this->db->query("SELECT * FROM loans");
		foreach($query->result() as $row){
			//Loans Table
            $loanBookingID = $row->LoanBookingID;
            $userID = $row->UserID;
			$loanStartDate = $row->LoanStartDate;
			$loanEndDate = $row->LoanEndDate;
			$loanStatus = $row->LoanStatus;
            $additionalNotes = $row->AdditionalNotes;
            $loanStartPeriod = $row->LoanStartPeriod;
			$loanEndPeriod = $row->LoanEndPeriod;
			$overdueEmailLastSent = $row->OverdueEmailLastSent;
			$additionalDetails = $row->AdditionalNotes;
			$bookingType = $row->LoanType;

			$sendEmail = false;
			$currentDate = date('Y-m-d');

			if($loanStatus == "Booked" && $bookingType == "bookingLoan"){
				//Lets check if the booking is overdue or not.
				//The criteria for an overdue booking is as follows
				//The current date is 2 days after the initial date they said they would hand it back and month and year are 0
				//Month or year is greater than 0
				//If the 2 days falls on Saturday or Sunday then it must not be sent until Monday
				//An email must have not been sent for this initial 2 days email to be sent
				//If initial email has been sent then only send an email if the current is 1 week from previous sent email

				//Since we have never sent an email about the overdue loan before. Lets check if 2 days have passed

				if($overdueEmailLastSent == null){
					$datetime1 = new DateTime('now');
					$datetime2 = new DateTime($loanEndDate);
					$difference = $datetime1->diff($datetime2);
					$differenceDays = $difference->d;
					$differenceMonths = $difference->m;
					$differenceYears = $difference->y;

					if(($differenceMonths > 0 or $differenceYears > 0) && $datetime1 > $datetime2){
						//Overdue
						$sendEmail = true;
					}elseif($differenceMonths == 0 && $differenceYears == 0 && $differenceDays >= 1 && $datetime1 > $datetime2){
						//Overdue
						$sendEmail = true;
					}
				}
			} elseif($loanStatus == "Overdue"){
				//An Overdue email has already been sent. Since we don't want to spam emails. Check that 1 day has passed before sending another email

				$datetime1 = new DateTime('now');
				$datetime2 = new DateTime($overdueEmailLastSent);
				$difference = $datetime1->diff($datetime2);
				$differenceDays = $difference->d;
				$differenceMonths = $difference->m;
				$differenceYears = $difference->y;

				if(($differenceMonths > 0 or $differenceYears > 0) && ($datetime1 > $datetime2)){
					//Overdue
					$sendEmail = true;
				}elseif($differenceMonths == 0 && $differenceYears == 0 && $differenceDays >= 1 && $datetime1 > $datetime2){
					//Overdue
					$sendEmail = true;
				}elseif($overdueEmailLastSent == NULL){
					//Overdue
					$sendEmail = true;
				}
			}

			if($sendEmail == true){
				//We need to send an email to the user to return their equipment as it's overdue
				//We also need to mark asset as overdue in the database

				//Mark loan as overude in the database
				$this->db->query("UPDATE loans SET OverdueEmailLastSent='$currentDate', LoanStatus='Overdue' WHERE LoanBookingID='$loanBookingID'");
				if($this->db->affected_rows() > 0){
					//Database updated successfully. Lets send email

					//Get email address of user we want to send too
					$email = $this->db->query("SELECT Email FROM users WHERE UserID='$userID'")->row()->Email;

					//Get list of assets which are overdue
					$assetNames = "";
					$query3 = $this->db->query("SELECT AssetIDLoan FROM loansassetslink WHERE LoanBookingID='$loanBookingID'");
					foreach($query3->result() as $row){
						//Get all assets from assets table
						$query4 = $this->db->query("SELECT AssetName,AssetTag FROM assets WHERE AssetID='{$row->AssetIDLoan}'");
						foreach($query4->result() as $row2){
							$assetNames .= $row2->AssetName . ' (' . $row2->AssetTag . ')<br>';
						}
					}

					if($this->config->item('enableEmails')){
						//Generate Email
						$page_data['TYPE'] = "Booking";
						$page_data["ADDITIONALDETAILS"] = $additionalDetails;
						$page_data["BOOKINGID"] = $loanBookingID;
						$page_data["BOOKINGDATE"] = "$loanStartDate - $loanEndDate";
						$page_data["BOOKINGTIME"] = "$loanStartPeriod - $loanEndPeriod";
						$page_data["ASSETS"] = $assetNames;
						$page_data["ACTION"] = "Overdue";
						$page_data["MESSAGE"] = "The following booking is Overdue. If this has come in error or you would like to extend your booking, please reply directly to this email";

						$emailCode = $this->load->view('templates/email', $page_data, TRUE);

						$sent = $this->sendEmail($email, $this->config->item('from'), $this->config->item('cc'), $emailCode, 'Overdue Booking #' . $loanBookingID);

						if ($sent)
						{
							echo "Email sent for booking " . $loanBookingID;
						} else {
							echo "Email failed to send for booking " . $loanBookingID;
							$this->db->query("UPDATE loans SET OverdueEmailLastSent=null, LoanStatus='Booked' WHERE LoanBookingID='$loanBookingID'");
						}
					}else{
						$returnMessage->Severity = "Success";
						$returnMessage->Message = "Booking Was Successfull";
					}
				}
			}
		}
	}

	private function sendEmail($to, $from, $cc, $message, $title)
	{
		$this->email->set_crlf( "\r\n" );
		$this->email->from($from);
		$this->email->to($to);
		$this->email->cc($cc);
		$this->email->subject($title);
		$this->email->message($message);
		$sent = $this->email->send();
		return $sent;
	}

	public function getListOfLoans()
	{
		$this->load->model('ManageLoans_model');
		return $this->ManageLoans_model->getListOfLoans();
	}
}
