<?php

class AssetHistory_model extends CI_Model {
	public function getListOfAssets()
	{
		$assetsJSON = array();
		$query = $this->db->query("SELECT * FROM assets ORDER BY AssetName ASC, AssetTag");
		foreach($query->result() as $row){
			$assetJSON = new stdClass();
			$assetJSON->AssetName = $row->AssetName;
			$assetJSON->AssetID = $row->AssetID;
			$assetJSON->AssetDescription = $row->AssetDescription;
			$assetJSON->AssetTag = $row->AssetTag;
			array_push($assetsJSON, $assetJSON);
		}

		return json_encode($assetsJSON);
    }
}
?>