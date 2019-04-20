<?php
class group {

	// New Functions
	function generateGroupPage($connection, $db, $poster, $page) { 
		$text = '<div class="container-fluid"><div class="row">';
		$text .= $this->generateProfile($connection, $page);

		$data = $this->getPosts($connection, $db, $poster);
		if ($data != NULL) $text .= $this->displayGroupPosts($data[0], $connection, $db); 
		else $text .= '<div class="col-xs-6"><h1> No posts were found </h1>';
		$text .= $poster->getPostSystem("group");
		$text .= '</div></div>';
		echo $text;
	}
	
	function getPosts($connection, $db, $poster) {
		$id = $_GET['id'];
		$data = array();
		
		$posts = $poster->getGroupPosts($connection, $id);
		if ($posts != NULL) {
			$groupInfo = $poster->getPostedGroups($connection, $posts, $db);
			$userInfo = $poster->getPostingUsers($connection, $posts, $db, $id);
			$temp = array("postArray"=>$posts, "groupArray"=>$groupInfo, "userArray"=>$userInfo);
			array_push($data, $temp);
		}
		return $data;
	}		
	
	function displayGroupPosts($data, $connection, $db) {
		if ($data["postArray"] != NULL) {
			$group = $db->getGroup($connection, $data["postArray"][0]["IDs"]["partyID"]);
			$text = '<div class="col-xs-6"><h1>'. $group["name"] .' Posts:</h1>';		
			foreach($data["postArray"] as $row) {
				$userRow = $db->getIndexRowInfo($data["userArray"], $row["IDs"]["postPartyID"], "id");
				$date = date("M jS Y, H:i a", strtotime($row["Posts"]["tStamp"]));
				$posterUserName = $userRow["fName"]. " " .$userRow["lName"];
				$text .= '<h4 class="postHead"><a href="profile.php?id='. $userRow["id"] .'">'. $posterUserName .'</a>'; 
				$text .= ' posted on <a href="group.php?id='. $group["groupID"] .'">'. $group["name"] .'</a> wall at '. $date .':</h4>';
				$text .= '<div class="postBody"><p>'. $row["Posts"]["post"] .'</p></div>';
			}
		} else { $text = '<div class="col-xs-6"><h1> No posts were found </h1>'; }
		return $text;
	}

	function generateProfile($connection, $page) {
		$info = $this->getProfileInfo($connection);
		$img = "/CSE-201-Project-Folder/resources/img/" . $info["srcImg"];
		$text = '<div class="container-fluid"><div class="row"><div class="col-xs-6">';
		$text .= '<img src="' . $img . '" style="width:50%" /></img>';
		$text .= '<h2>'. $info["name"] .'</h2>';
		$text .= '<div class="profileInfo"><p>'. $info["description"] .'</p></div>';
		$text .= $page->generateButtons($connection, $page, 2);
		$text .= '</div>';
		return $text;
	}
	
	function getProfileInfo($db) {
        $profileInfo = array();
        $query = "Select * From groups Where groupID = " . $_GET["id"];
        $result = mysqli_query($db, $query);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
				$img = $row["srcImg"];
				if ($img == NULL) $img = "basic.png";
                $profileInfo = array("name"=>$row["name"], "description"=>$row["description"], "srcImg"=>$img);
            }
        }
        return $profileInfo;
    }
	
}	
?>