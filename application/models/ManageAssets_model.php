<?php

class ManageAssets_model extends CI_Model {
	public function getListOfAssets()
	{
		//This will fetch a list of assets currently in the database and construct a table which is returned.
		$query = $this->db->query("SELECT * FROM assets ORDER BY AssetName");
		$htmlString = "<table id='assetsTable' class='table'><thead><tr><th scope='col'>Name</th><th scope='col'>Description</th><th scope='col'>Tag</th><th scope='col'>Location</th></tr></thead><tbody>";
		foreach($query->result() as $row){
			$assetID = $row->AssetID;
			$assetName = $row->AssetName;
			$assetTag = $row->AssetTag;
			$assetLocation = $row->AssetLocation;
			$assetDescription = $row->AssetDescription;
           
            log_message('error', $assetID);            
            log_message('error', $assetName);
            log_message('error', $assetTag);
            log_message('error', $assetLocation);
            log_message('error', $assetDescription);

            $htmlString .= "<tr id='$assetID'><td>$assetName</td><td>$assetDescription</td><td>$assetTag</td><td>$assetLocation</td></tr>";
            
            log_message('error', $htmlString);
            log_message('error', "-----------------------------");             
		}
		$htmlString .= "</tbody></table>";

        //Output the constructed table
        
		echo $htmlString;
    }
    
    public function getListOfAssetNames(){
        $query = $this->db->query("SELECT AssetName FROM assets");
        $listofAssets = [];
        foreach($query->result() as $row){
            array_push($listofAssets, $row->AssetName);
        }
        echo json_encode($listofAssets);
    }
}
?>