<?php

class ManageAssets_model extends CI_Model {
	public function getListOfAssets()
	{
		//This will fetch a list of assets currently in the database and construct a table which is returned.
		$query = $this->db->query("SELECT * FROM assets ORDER BY AssetName,AssetTag");
		$htmlString = "<table id='assetsTable' class='table'><thead><tr><th scope='col'>Name</th><th scope='col'>Description</th><th scope='col'>Asset Tag</th></tr></thead><tbody>";
		foreach($query->result() as $row){
			$assetID = $row->AssetID;
			$assetName = $row->AssetName;
			$assetTag = $row->AssetTag;
			$assetDescription = $row->AssetDescription;
            $htmlString .= "<tr id='$assetID'><td>$assetName</td><td>$assetDescription</td><td>$assetTag</td></tr>";
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