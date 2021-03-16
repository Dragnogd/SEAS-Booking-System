<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ManageAssets extends CI_Controller {
	public function index()
	{
		$this->load->model('ManageAssets_model')or die("error");
		$this->load->view('templates/header');
		$this->load->view('templates/navbarAdmin');
		$this->load->view('manageassets');
	}



	public function updateAsset()
	{
		//Update a particular asset into the database

		//List of variables we need to populate
		$assetName;
		$assetDescription;
		$assetTag;
		$assetLocation;
		$assetID;

		//Asset ID
		//Must be only a number value.
		if(isset($_POST['assetID'])){
			if(empty($_POST['assetID'])){
			  echo "The Asset ID field must be filled out before saving";
			  return;
			}      
			else {
				if(is_numeric($_POST['assetID'])){
					$assetID = $_POST['assetID'];
				} else {
					echo "The Asset ID field must be a number";
					return;
				}
			}
		} else {
			echo "Asset ID has not been initialised";
			return;
		}

		//Asset name
		//Can be anything the user wants this to be but must be filled out.
		if(isset($_POST['assetName'])){
			if(empty($_POST['assetName'])){
			  echo "The Asset Name field must be filled out before saving";
			  return;
			}      
			else $assetName = $_POST['assetName'];
		} else {
			echo "Asset Name has not been initialised";
			return;
		}

		//Asset Description
		//Can be anything the user wants this to be but must be filled out.
		if(isset($_POST['assetDescription'])){
			if(empty($_POST['assetDescription'])){
			  echo "The Asset Description field must be filled out before saving";
			  return;
			}      
			else $assetDescription = $_POST['assetDescription'];
		} else {
			echo "Asset Description has not been initialised";
			return;
		}

		//Asset Tag
		//Must be only a number value.
		//Must not be a duplicate of values already in database
		if(isset($_POST['assetTag'])){
			if(empty($_POST['assetTag'])){
			  echo "The Asset Tag field must be filled out before saving";
			  return;
			}      
			else {
				if(is_numeric($_POST['assetTag'])){
					//Check if the asset tag is not already in the database
					$query = $this->db->query("SELECT * FROM assets WHERE AssetTag='" . $_POST['assetTag'] . "'")->row();
					if(isset($query)){
						if($query->AssetTag == $_POST['assetNewID']){
							$assetTag = $_POST['assetTag'];
						}else{
							echo "Asset Tag is already in use";
							return;
						}
					} else {
						$assetTag = $_POST['assetTag'];
					}	
				} else {
					echo "The Asset Tag field must be a number";
					return;
				}
			}
		} else {
			echo "Asset Tag has not been initialised";
			return;
		}

		//Asset Location
		//This is selected from a drop down by the user. Cannot be empty
		if(isset($_POST['assetLocation'])){
			if(empty($_POST['assetLocation'])){
			  echo "The Asset Location field must be filled out before saving";
			  return;
			}      
			else $assetLocation = $_POST['assetLocation'];
		} else {
			echo "Asset Location has not been initialised";
			return;
		}

		//All checks on the data have passed. Now we can insert this into the database
		$query = $this->db->query("UPDATE assets SET AssetName='$assetName', AssetDescription='$assetDescription', AssetLocation='$assetLocation', AssetTag='$assetTag' WHERE AssetID='$assetID'");
		if($this->db->affected_rows() > 0){
			echo "Success";
		} else {
			echo "Error inserting asset into database";
		}

	}

	public function insertNewAsset()
	{
		//Insert a new asset into the database

		//List of variables we need to populate
		$assetName;
		$assetDescription;
		$assetTag;
		$assetLocation;

		//Asset name
		//Can be anything the user wants this to be but must be filled out.
		if(isset($_POST['assetName'])){
			if(empty($_POST['assetName'])){
			  echo "The Asset Name field must be filled out before saving";
			  return;
			}      
			else $assetName = $_POST['assetName'];
		} else {
			echo "Asset Name has not been initialised";
			return;
		}

		//Asset Description
		//Can be anything the user wants this to be but must be filled out.
		if(isset($_POST['assetDescription'])){
			if(empty($_POST['assetDescription'])){
			  echo "The Asset Description field must be filled out before saving";
			  return;
			}      
			else $assetDescription = $_POST['assetDescription'];
		} else {
			echo "Asset Description has not been initialised";
			return;
		}

		//Asset Tag
		//Must be only a number value.
		//Must not be a duplicate of values already in database
		if(isset($_POST['assetTag'])){
			if(empty($_POST['assetTag'])){
			  echo "The Asset Tag field must be filled out before saving";
			  return;
			}      
			else {
				if(is_numeric($_POST['assetTag'])){
					//Check if the asset tag is not already in the database
					$query = $this->db->query("SELECT * FROM assets WHERE AssetTag='" . $_POST['assetTag'] . "'")->row();
					if(isset($query)){
						echo "The Asset Tag inputted already exists";
						return;
					} else {
						$assetTag = $_POST['assetTag'];
					}	
				} else {
					echo "The Asset Tag field must be a number";
					return;
				}
			}
		} else {
			echo "Asset Tag has not been initialised";
			return;
		}

		//Asset Location
		//This is selected from a drop down by the user. Cannot be empty
		if(isset($_POST['assetLocation'])){
			if(empty($_POST['assetLocation'])){
			  echo "The Asset Location field must be filled out before saving";
			  return;
			}      
			else $assetLocation = $_POST['assetLocation'];
		} else {
			echo "Asset Location has not been initialised";
			return;
		}

		//All checks on the data have passed. Now we can insert this into the database
		$query = $this->db->query("INSERT INTO assets (AssetName,AssetTag,AssetLocation,AssetDescription) VALUES('$assetName',$assetTag,'$assetLocation','$assetDescription')");
		if($this->db->affected_rows() > 0){
			echo "Success";
		} else {
			echo "Error inserting asset into database";
		}
	}

	public function deleteAsset()
	{
		//Delete an asset from the database
		//Note do not delete if outstanding loans/bookings

		//List of variables we need to populate
		$assetID;
		//Check if we recieved an asset tag
		if(isset($_POST['assetID'])){
			if(empty($_POST['assetID'])){
			  echo "The Asset ID field must be filled out before saving";
			  return;
			}      
			else $assetID = $_POST['assetID'];
		} else {
			echo "Asset ID has not been initialised";
			return;
		}

		//Check if there are currently any outstanding loans for this asset before deleting
		$result = $this->db->query("SELECT * FROM loansassetslink RIGHT OUTER JOIN loans ON loans.LoanBookingID=loansassetslink.LoanBookingID WHERE loansassetslink.AssetIDLoan='$assetID' AND loans.LoanStatus<>'Completed'");
		if ($result->num_rows()) {
			echo "Error deleting asset from database as the asset is currently in use";
		} else {
			//All checks on the data have passed. Now be remove the asset from the database
			$query = $this->db->query("DELETE FROM assets WHERE AssetID='" . $assetID . "'");
			if($this->db->affected_rows() > 0){
				echo "Success";
			} else {
				echo "Error deleting asset from database";
			}
		}
	}

	public function getAsset()
	{
		$assetID;
		//Check if we recieved an asset tag
		if(isset($_POST['assetID'])){
			if(empty($_POST['assetID'])){
			  echo "The Asset ID field must be filled out before saving";
			  return;
			}      
			else $assetID = $_POST['assetID'];
		} else {
			echo "Asset ID has not been initialised";
			return;
		}

		//Fetches information about a particular asset and returns this as a JSON array
		$query = $this->db->query("SELECT AssetName,AssetDescription,AssetTag,AssetLocation FROM assets WHERE AssetID='$assetID'")->row();

		echo json_encode($query);
	}

	public function getAssetID()
	{
		$assetTag;
		//Check if we recieved an asset tag
		if(isset($_POST['assetTag'])){
			if(empty($_POST['assetTag'])){
			  echo "The Asset Tag field must be filled out before saving";
			  return;
			}      
			else $assetTag = $_POST['assetTag'];
		} else {
			echo "Asset Tag has not been initialised";
			return;
		}

		//Fetches information about a particular asset and returns this as a JSON array
		$query = $this->db->query("SELECT AssetID FROM assets WHERE AssetTag='$assetTag'")->row();

		echo json_encode($query);
	}

}
