<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ManageBookings extends CI_Controller {
	public function index()
	{
		$this->load->model('ManageBookings_model')or die("error");
		$this->load->view('templates/header');
		$this->load->view('templates/navbarAdmin');
		$this->load->view('managebookings');
		$this->load->view('templates/footer');
	}

	public function insertBooking()
	{
		//List of all the data for the particular booking. We need to verify the booking is ok before
		//inserting the data into the database

		$returnMessage = new stdClass();

		$assets = $this->input->post('assets', TRUE);
		$startDate = $this->input->post('loanStartDate');
		$endDate = $this->input->post('loanEndDate');
		$startPeriod = $this->input->post('loanStartPeriod');
		$endPeriod = $this->input->post('loanEndPeriod');
		$additionalDetails = $this->input->post('additionalDetails', TRUE);
		$bookingPeriod = $this->input->post('bookingPeriod');
		$bookingType = $this->input->post('bookingType');
		$user = $this->input->post('selectedUser', TRUE);
		$setupTitle = $this->input->post('setupTitle', TRUE);
		$setupLocation = $this->input->post('setupLocation', TRUE);

		//Warning/Danger/Success

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
			$loanStatus = "Booked";
			$query = $this->db->query("INSERT INTO loans (UserID,LoanStartDate,LoanEndDate,LoanStatus,AdditionalNotes,LoanStartPeriod,LoanEndPeriod,LoanType,LoanLocation,Title) VALUES ($userID, STR_TO_DATE('$startDate', '%Y-%m-%d'), STR_TO_DATE('$endDate', '%Y-%m-%d'),'$loanStatus',{$this->db->escape($additionalDetails)},'$startPeriod','$endPeriod','$bookingType',{$this->db->escape($setupLocation)},{$this->db->escape($setupTitle)})");

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

			if($this->config->item('enableEmails')){
				//Generate Email
				$page_data["ADDITIONALDETAILS"] = $additionalDetails;
				$page_data["BOOKINGID"] = $loanBookingID;
				$page_data["BOOKINGDATE"] = "$startDate - $endDate";
				$page_data["BOOKINGTIME"] = "$startPeriod - $endPeriod";
				$page_data["ASSETS"] = $assetNames;
				$page_data["ACTION"] = "Created";
				$page_data['TYPE'] = "Setup";
				$page_data["MESSAGE"] = "The following setup has been created";
				$page_data["TITLE"] = $setupTitle;
				$page_data["LOCATION"] = $setupLocation;

				$emailCode = $this->load->view('templates/emailSetup', $page_data, TRUE);

				$sent = $this->sendEmail($email, $this->config->item('from'), $this->config->item('cc'), $emailCode, 'IT Department Setup #' . $loanBookingID);

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

			if($bookingType == "bookingSetup"){
				//Create Calendar Item for Outlook
				//'2015-05-12 20:00:00'
				$bookingCalendarTitle = "";
				if(strlen($setupTitle) > 0){
					$bookingCalendarTitle = $setupTitle;
				}else{
					$bookingCalendarTitle = 'IT Department Booking #' . $loanBookingID;
				}
				$event = array(
					'id' => 1,
					'title' => $bookingCalendarTitle,
					'address' => "",
					'description' => 'Booking Details:\nPlease find below the details of your setup\n\nSetup Date & Time\nStart Date: ' . $startDate . '\nEnd Date: ' . $endDate . '\nStart Period: ' . $startPeriod . '\nEnd Period: ' . $endPeriod . '\n\nAssets & Location\n' . $assetNames . '\n\nAdditional Details\n' . $additionalDetails,
					'datestart' => date('Ymd\THis', strtotime($startDate . " " . $startPeriod)),
					'dateend' => date('Ymd\THis', strtotime($endDate . " " . $endPeriod)),
					'address' => $setupLocation
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

		$query = $this->db->query("SELECT * FROM assets ORDER BY AssetName ASC");
		log_message('error', "----------------------------------");
		log_message('error', "Checking Avaliable Equipment for a single day booking");
		log_message('error', "----------------------------------");
		foreach($query->result() as $row){
			array_push($avaliableAssets, $row->AssetID);
			$query2 = $this->db->query("SELECT * FROM loans RIGHT OUTER JOIN loansassetslink ON loans.LoanBookingID=loansassetslink.LoanBookingID");

			foreach($query2->result() as $row2){
				//Now we need to decide whether the current asset is in use for the specified time period
				//The user can book a loan if the start and end period are before or after any current loans.
				//Do not check loans which have been completed
				if($row2->AssetIDLoan == $row->AssetID && $row2->LoanStatus != "Completed"){
					log_message('error', "Found Asset: " . $row->AssetName . " (" . $row->AssetTag . ")");
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
							//log_message('error', "Asset is not avaliable for todays date");
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
						//log_message('error', "Removing asset since its not avaliable");
						log_message('error', $row->AssetName . " NOT Avaliable");
						foreach($avaliableAssets as $assetID){
							if($assetID == $row->AssetID){
								unset($avaliableAssets[array_search($assetID, $avaliableAssets)]);
								$avaliableAssets = array_values($avaliableAssets);
							}
						}
					}else{
						log_message('error', $row->AssetName . " Avaliable");
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

		$query = $this->db->query("SELECT * FROM assets ORDER BY AssetName ASC");

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

	public function completeLoan()
	{
		//List of variables we need to populate
		$loanID;

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

		//$query = $this->db->query("DELETE FROM loans WHERE LoanBookingID='$loanID'");
		//$query = $this->db->query("DELETE FROM loansassetslink WHERE LoanBookingID='$loanID'");

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
			$page_data['TYPE'] = "Setup";
			$page_data["MESSAGE"] = "The following setup has been completed";

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

	public function getUpcomingSetups(){
		log_message('error', "Inside Upcoming Setup");
		$query = $this->db->query("SELECT * FROM loans WHERE LoanStatus<>'Completed'");
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
			$bookingType = $row->LoanType;
			$setupNotficationSent = $row->SetupNotficationSent;
			$setupTitle = $row->Title;
			$setupLocation = $row->LoanLocation;

			$sendEmail = false;
			$currentTime = new DateTime('now');

			$loanDateTime = new DateTime($loanStartDate);
			$dateDifference = $loanDateTime->diff($currentTime);
			$differenceMinutes = $dateDifference->i;
			$differenceDays = $dateDifference->d;
			$differenceHours = $dateDifference->h;

			//If booking is today date and is a setup and the due date is less than current date
			if($bookingType == "bookingSetup" && $differenceDays == 0){
				//Get start period of the booking. Loop through each time period.
				//If time period matches. Check if current time is within 30 minutes or current time.
				//If time is within this, send email to remind of upcoming booking

				$timeDifference;
				$time1;

				$time1 = new DateTime($loanStartDate . $loanStartPeriod);

				$timeDifference = $time1->diff($currentTime);
				$differenceMinutes = $timeDifference->i;
				$differenceDays = $timeDifference->d;
				$differenceHours = $timeDifference->h;

				var_dump($currentTime);
				echo "<br>";
				var_dump($time1);
				echo "<br>";
				var_dump($currentTime < $time1);
				echo "<br>";

				//If time difference is not negative (i.e not in past) and is less than or equal to 30, then send reminder email
				if($currentTime < $time1 && $setupNotficationSent == 0 && $differenceDays == 0 && $differenceHours == 0 && ($differenceMinutes > 0 and $differenceMinutes <= 30)){
					echo "SEND EMAIL FOR THIS SETUP #" . $loanBookingID . " days: " . $differenceDays . " hours: " . $differenceHours . " minutes:" . $differenceMinutes . " Start Period " . $loanStartPeriod . "<br>";

					$this->db->query("UPDATE loans SET setupNotficationSent=1 WHERE LoanBookingID='$loanBookingID'");
					if($this->db->affected_rows() > 0){
						//Database updated successfully. Lets send email

						//Get list of assets for the setup
						$assetNames = "";
						$query3 = $this->db->query("SELECT AssetIDLoan FROM loansassetslink WHERE LoanBookingID='$loanBookingID'");
						foreach($query3->result() as $row){
							//Get all assets from assets table
							$query4 = $this->db->query("SELECT AssetName,AssetTag FROM assets WHERE AssetID='{$row->AssetIDLoan}'");
							foreach($query4->result() as $row2){
								$assetNames .= $row2->AssetName . ' (' . $row2->AssetTag . ')<br>';
							}
						}
						//Get email address of user we want to send too
						$email = $this->db->query("SELECT Email FROM users WHERE UserID='$userID'")->row()->Email;

						//Generate Email
						if($this->config->item('enableEmails')){
							//Generate Email
							$page_data["ADDITIONALDETAILS"] = $additionalNotes;
							$page_data["BOOKINGID"] = $loanBookingID;
							$page_data["BOOKINGDATE"] = "$loanStartDate - $loanEndDate";
							$page_data["BOOKINGTIME"] = "$loanStartPeriod - $loanEndPeriod";
							$page_data["ASSETS"] = $assetNames;
							$page_data["ACTION"] = "Due Soon";
							$page_data['TYPE'] = "Setup";
							$page_data["MESSAGE"] = "The following setups are due in the next 30 minutes";
							$page_data["TITLE"] = $setupTitle;
							$page_data["LOCATION"] = $setupLocation;

							$emailCode = $this->load->view('templates/emailSetup', $page_data, TRUE);

							$sent = $this->sendEmail($email, $this->config->item('from'), $this->config->item('cc'), $emailCode, 'SETUP REMINDER for Booking #' . $loanBookingID);

							if ($sent)
							{
								$returnMessage = "Success";
							} else {
								$returnMessage = "Error";
								$this->db->query("UPDATE loans SET setupNotficationSent=0 WHERE LoanBookingID='$loanBookingID'");
							}
						}else{
							$returnMessage = "Success";
						}
					}
				}else{
					echo "DON'T SEND EMAIL FOR THIS SETUP #" . $loanBookingID . " days: " . $differenceDays . " hours: " . $differenceHours . " minutes:" . $differenceMinutes . " Start Period " . $loanStartPeriod . "<br>";
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
		$this->load->model('ManageBookings_model');
		return $this->ManageBookings_model->getListOfLoans();
	}
}
