<?php

class ManageBookings_model extends CI_Model {
    public function getListOfLoans()
	{
		//This will fetch a list of loans currently in the system
        $query = $this->db->query("SELECT * FROM loans ORDER BY LoanStartDate ASC");
        $htmlString = "<table id='loansTable' class='table'><thead><tr><th scope='col'>ID</th><th scope='col'>User</th><th scope='col'>Start Date</th><th scope='col'>End Date</th><th scope='col'>Start Period</th><th scope='col'>End Period</th><th scope='col'>Location</th><th scope='col'>Additional Notes</th><th scope='col'>Status</th><th scope='col'>Assets</th><th scope='col'>Complete Booking</th></tr></thead><tbody>";
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
			$loanType = $row->LoanType;
			$loanLocation = $row->LoanLocation;

            if($loanType == "bookingSetup"){
                if($loanStatus != "Completed"){
                    //Users Table (Find what user id matches which user)
                    $query2 = $this->db->query("SELECT Forename,Surname FROM users WHERE UserID='$userID'");
                    $name;
                    foreach($query2->result() as $row){
                        $name = $row->Forename . " " . $row->Surname;
                    }

                    //Loans Assets Link Table
                    $query3 = $this->db->query("SELECT * FROM loansassetslink WHERE LoanBookingID='$loanBookingID'");
                    $assetsList = "";
                    foreach($query3->result() as $row){
                        $assetIDLoan = $row->AssetIDLoan;

                        //Find asset in Assets table
                        $query4 = $this->db->query("SELECT AssetName,AssetTag FROM assets WHERE AssetID='$assetIDLoan'");
                        foreach($query4->result() as $row){
                            log_message('error', $row->AssetName);
                            $assetsList .= $row->AssetName . " (" . $row->AssetTag . ")<br>";
                            log_message('error', $assetsList);
                        }
                    }

					$currentDate = date('Y-m-d');

					$loanStatusHTML = "";
					if($loanStatus == "Overdue"){
						$loanStatusHTML = "<span class='badge bg-danger'>Overdue</span>";
					}elseif($loanStatus == "Booked"){
						$loanStatusHTML = "<span class='badge bg-success'>Booked</span>";
					}elseif($loanStatus == "Reserved"){
						$loanStatusHTML = "<span class='badge bg-warning'>Reserved</span>";
					}

					$htmlString .= "<tr id='$loanBookingID'><td>$loanBookingID</td><td>$name</td><td>$loanStartDate</td><td>$loanEndDate</td><td>$loanStartPeriod</td><td>$loanEndPeriod</td><td style='max-width: 500px;'>$loanLocation</td><td style='max-width: 500px;'>$additionalNotes</td><td>$loanStatusHTML</td><td>$assetsList</td><td><button type='button' class='completeBooking btn btn-block btn-success'>Complete</button></td></tr>";

                    log_message('error', $htmlString);
                    log_message('error', "-----------------------------");
                }
            }
		}
		$htmlString .= "</tbody></table>";

        //Output the constructed table

		echo $htmlString;
    }

    public function getStaff()
	{
		$query = $this->db->query("SELECT * FROM users ORDER BY Forename ASC");

		$htmlStr = "";
		foreach($query->result() as $user){
            $htmlStr .= "<option id='{$user->UserID}'>{$user->Forename} {$user->Surname}</option>";
            // log_message("error", $htmlStr);
		}

		echo $htmlStr;
    }

    public function getBookings()
    {
        $query = $this->db->query("SELECT * FROM loans WHERE LoanStatus='Booked' OR LoanStatus='Overdue' ORDER BY LoanBookingID");

        $htmlStr = "";
        foreach($query->result() as $loan){
            $user = $this->db->query("SELECT * FROM users WHERE UserID='{$loan->UserID}'")->row();
            $htmlStr .= "<option id='{$loan->LoanBookingID}'>#{$loan->LoanBookingID} {$user->Forename} {$user->Surname}</option>";
            log_message("error", $htmlStr);
        }

        echo $htmlStr;
    }
}
?>