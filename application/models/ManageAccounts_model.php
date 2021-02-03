<?php

class ManageAccounts_model extends CI_Model {
    public function getListOfUsers()
	{
		//This will fetch a list of users currently in the database and construct a table which is returned.
		$query = $this->db->query("SELECT * FROM users");
		$htmlString = "<table id='usersTable' class='table'><thead><tr><th scope='col'>Forename</th><th scope='col'>Surname</th><th scope='col'>Email</th></tr></thead><tbody>";
		foreach($query->result() as $row){
			$userID = $row->UserID;
			$forename = $row->Forename;
			$surname = $row->Surname;
			$email = $row->Email;
           
            log_message('error', $userID);            
            log_message('error', $forename);
            log_message('error', $surname);
            log_message('error', $email);

            $htmlString .= "<tr id='$userID'><td>$forename</td><td>$surname</td><td>$email</td></tr>";
            
            log_message('error', $htmlString);
            log_message('error', "-----------------------------");             
		}
		$htmlString .= "</tbody></table>";

        //Output the constructed table
        
		echo $htmlString;
    }
}
?>