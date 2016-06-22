<?php
class post_page_class
{
	
	function humanTiming ($time)
	{
	    $time = time() - $time; 
	    $tokens = array 
	    (
	        31536000 => 'year',
	        2592000 => 'month',
	        604800 => 'week',
	        86400 => 'day',
	        3600 => 'hour',
	        60 => 'minute',
	        1 => 'second'
	    );
	    foreach ($tokens as $unit => $text) 
	    {
	        if ($time < $unit) continue;
	        $numberOfUnits = floor($time / $unit);
	        return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
	    }
	}
	
	function get_wall_posts($id)
	{
		$friend_id="";
		if($_REQUEST['userId']=="")
		{
			
			 $get_all_id="SELECT tbl_user.*,tbl_user.id as cid FROM tbl_user   INNER JOIN tbl_friends
	
							ON tbl_user.id =tbl_friends.friendId OR tbl_user.id = tbl_friends.userId where
							
							
							 ( tbl_friends.userId ='$id' or tbl_friends.friendId ='$id') and (tbl_friends.requestconfirmed=1) GROUP BY (cid )";
			
			$res_u=mysql_query($get_all_id);
			
			while ($row_all_friend=mysql_fetch_array($res_u)) 
			{
				if(trim($friend_id))
					$friend_id.=" or userId=".$row_all_friend['cid'];
				else 
					$friend_id="userId=".$row_all_friend['cid'];
			}
			
			if($friend_id!='')
				$all_id=$friend_id." or userId=".$id;
			else 
				$all_id="userId=".$id;
				
			 $sql = "select * from `tbl_share` where (".$all_id.") order by date desc ";
		}
		else 
		{
			$sql = "select * from `tbl_share` where userId='$id' order by date desc ";
		}
 		$res= mysql_query($sql); 

 		return $res;
	}
	
	
	function get_user_details($id=NULL)
	{
		if($id)
		{
			$sql = "select * from `tbl_user` where id = '".$id."' limit 1 ";
	 		$res= mysql_query($sql); 
	 		
	 		$row= mysql_fetch_array($res);
	 		return $row;
		}
	}
	
	function get_user_visible($id=NULL)
	{
		if($id)
		{
			$sql = "select * from `display_fields` where user_id = '".$id."' limit 1 ";
	 		$res= mysql_query($sql); 
	 		
	 		$row= mysql_fetch_array($res);
	 		return $row;
		}
	}
	
	function posted_by($user_post_id)
	{
	 	$sql = "select first_name,last_name from `tbl_user` where id = '".$user_post_id."' LIMIT 1";
 		$res= mysql_fetch_array(mysql_query($sql)); 
 		
 	    $posted_by=ucwords($res['first_name'])." ".ucwords($res['last_name']);
 		echo $posted_by;
	}
	
	function user_snap($user_post_id)
	{
	 	$sql = "select user_image from `tbl_user` where id = '".$user_post_id."' LIMIT 1";
 		$res= mysql_fetch_array(mysql_query($sql)); 
 		
 	   
	 	  if(file_exists("uploads/userimages/$res[user_image]") && $res['user_image']!="")
	 	  {
	 	  	 $user_snap=$res['user_image'];
	 	  }
	 	  else 
	 	  {
	 	  	$user_snap="profile-photo.jpg";
	 	  }
 	    
 	  
 	    
 		echo $user_snap;
	}
	
	function add_friend()
	{
		
		 $sql_added = "select * from `tbl_friends` where ( userId ='".$_REQUEST['friend_id']."' or friendId ='".$_REQUEST['friend_id']."')
											AND ( userId ='".$_SESSION['varUserID']."' or friendId ='".$_SESSION['varUserID']."')  ";
							$res_friend=mysql_query($sql_added);
							$count_friend=mysql_num_rows($res_friend);
							
		if($count_friend==0)
		{
			$sql ="INSERT INTO `tbl_friends` 
									(`userId` , `friendId`, `requestconfirmed`  )
			 	 
					VALUES ('".$_SESSION['varUserID']."','".$_REQUEST['friend_id']."','0')"; 
			
			mysql_query($sql);
			
			
			$get_email="select * from tbl_user where id='".$_REQUEST['friend_id']."'";
			$get_e=mysql_fetch_array(mysql_query($get_email));	
			
			
			$send_email="select * from tbl_user where id='".$_SESSION['varUserID']."'";
			$send_e=mysql_fetch_array(mysql_query($send_email));
			
				
			$namefrom='info@mygospellove.com';
			
			
			/*$to = "$get_e[email]";
			
			$friend_name=ucwords($_REQUEST['keywords']);
			$subject = "$send_e[first_name] wants to be friends on MyGospelLove";
			
			$message = '<b>'.ucwords($send_e[first_name]).' wants to be friends with you on MyGospelLove. </b>
						<br>
						<br>
						
							'.ucwords($send_e[first_name]).' is a member of My Gospel Love and would like for you to join and fellowship with like-minded individuals.  
						
						
						<br>
						<br>
						
						<b>Please join: </b><a href="http://www.MyGospelLove.com">MyGospelLove.com </a> <b>and Find your Friends</b>
						'; 
			
			 
			 
			$headers = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
			$headers .= "From: info@mygospellove.com \r\n";
					
				
			mail($to,$subject,$message,$headers);*/
			
			$msg='<center><br><span style="color:green;font-size:12px;font-weight:bold;padding-top:20px;" ><img src="admin/images/tick_icon35.gif" width="15" height="15">&nbsp;Request Sent Successfully</span></center><br>';
		}
		else 
		{
			$msg='<center><br><span style="color:red;font-size:12px;font-weight:bold;padding-top:20px;" ><img src="images/Delete.png" width="15" height="15">&nbsp;Request already sent!</span></center><br>';
		}
		return $msg;	
	}
	
	
	function add_friend_email()
	{
		$sql_added = "select * from `tbl_friends` where ( userId ='".$_REQUEST['friend_id']."' or friendId ='".$_REQUEST['friend_id']."')
											AND ( userId ='".$_SESSION['varUserID']."' or friendId ='".$_SESSION['varUserID']."')  ";
							$res_friend=mysql_query($sql_added);
							$count_friend=mysql_num_rows($res_friend);
							
		if($count_friend==0)
		{
			$sql ="INSERT INTO `tbl_friends` 
									(`userId` , `friendId`, `requestconfirmed`  )
			 	 
					VALUES ('".$_SESSION['varUserID']."','".$_REQUEST['friend_id']."','0')"; 
			
			mysql_query($sql);
			
			
			$get_email="select * from tbl_user where id='".$_REQUEST['friend_id']."'";
			$get_e=mysql_fetch_array(mysql_query($get_email));	
			
			
			$send_email="select * from tbl_user where id='".$_SESSION['varUserID']."'";
			$send_e=mysql_fetch_array(mysql_query($send_email));
			
				
			$namefrom='info@mygospellove.com';
			
			
			$to = "$get_e[email]";
			
			$friend_name=ucwords($_REQUEST['keywords']);
			$subject = "$send_e[first_name] wants to be friends on MyGospelLove";
			
			$message = '<b>'.ucwords($send_e[first_name]).' wants to be friends with you on MyGospelLove. </b>
						<br>
						<br>
						
							'.ucwords($send_e[first_name]).' is a member of My Gospel Love and would like for you to join and fellowship with like-minded individuals.  
						
						
						<br>
						<br>
						
						<b>Please join: </b><a href="http://www.MyGospelLove.com">MyGospelLove.com </a> <b>and Find your Friends</b>
						'; 
			
			 
			 
			$headers = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
			$headers .= "From: info@mygospellove.com \r\n";
					
				
			mail($to,$subject,$message,$headers);
			
			$msg='<center><br><span style="color:green;font-size:12px;font-weight:bold;padding-top:20px;" ><img src="admin/images/tick_icon35.gif" width="15" height="15">&nbsp;Request Sent Successfully</span></center><br>';
		}
		else 
		{
			$msg='<center><br><span style="color:red;font-size:12px;font-weight:bold;padding-top:20px;" ><img src="images/Delete.png" width="15" height="15">&nbsp;Request already sent!</span></center><br>';
		}
		return $msg;
	}
	
	function confirm_invitation()
	{
		
		$sql="update tbl_friends set requestconfirmed='1' where id='".$_REQUEST['invitationid']."'";
		mysql_query($sql);			
		$msg='<center><br><span style="color:green;font-size:12px;font-weight:bold;padding-top:20px;" ><img src="admin/images/tick_icon35.gif" width="15" height="15">&nbsp;Invitation confirmed successfully!</span></center><br>';
		
		return $msg;
	}

	function update_profile()
	{
		
		 move_uploaded_file($_FILES["user_image"]["tmp_name"], "uploads/userimages/" . $_FILES["user_image"]["name"]);

		 $user_image="";
		 if($_FILES["user_image"]["tmp_name"]!="")
		 {

		 	$user_image="user_image = '".$_FILES['user_image']['name']."',";
		 }
		
		 
		$sql="update tbl_user set first_name='".$_REQUEST['first_name']."',last_name='".$_REQUEST['last_name']."',
			  company_name='".$_REQUEST['company_name']."',
			  birth_date='".$_REQUEST['birthday']."',
			  username='".$_REQUEST['username']."',
			  email='".$_REQUEST['email']."',
			  address='".$_REQUEST['address']."',
			  state='".$_REQUEST['state']."',
			  city='".$_REQUEST['city']."',
			  countries_name='".$_REQUEST['countries_name']."',
			  zip='".$_REQUEST['zip']."',
			  gender='".$_REQUEST['gender']."',
			  $user_image
			  about='".$_REQUEST['about']."'
		 where id='".$_REQUEST['id']."'";
		 mysql_query($sql);	

		 
	    $sql_view="select * from display_fields where user_id='".$_SESSION['varUserID']."'";
		$result=mysql_query($sql_view);
		$exists=mysql_num_rows($result);
		
		if($exists>0)
		{
			   $sql_up_view="UPDATE `display_fields` SET 
				`dob` = '".$_REQUEST['dob1']."',
				`email` = '".$_REQUEST['email1']."',
				`gender` = '".$_REQUEST['gender1']."',
				`state` = '".$_REQUEST['state1']."',
				`country` = '".$_REQUEST['country1']."',
				`zip` = '".$_REQUEST['zip1']."',
				`firstname` = '".$_REQUEST['firstname']."',
				`lastname` = '".$_REQUEST['lastname']."',
				`company_name` = '".$_REQUEST['company_name1']."',
				`city` = '".$_REQUEST['city1']."',
				`address` = '".$_REQUEST['address1']."',
				`about` = '".$_REQUEST['about1']."'
				
				 WHERE `user_id` ='".$_REQUEST['id']."'";
				
				 mysql_query($sql_up_view);
		}
		else
		{
			$sql_in_view="INSERT INTO `display_fields` ( `user_id` , `dob` , `email` , `gender` , `state` , `country` , 
						  `zip` , `firstname` , `lastname` ,`company_name` ,`company_url` ,`address` , `weburl` , `about`,`city` )
							VALUES 
							
							('".$_SESSION['varUserID']."','".$_REQUEST['dob1']."', '".$_REQUEST['email1']."',
							 '".$_REQUEST['gender1']."', '".$_REQUEST['state1']."', 
							 '".$_REQUEST['country1']."', '".$_REQUEST['zip1']."', '".$_REQUEST['firstname']."',
							  '".$_REQUEST['lastname']."', '".$_REQUEST['company_name1']."', '".$_REQUEST['company_name1']."', 
							  '".$_REQUEST['address1']."', '".$_REQUEST['weburl']."', '".$_REQUEST['about1']."'
							  ,'".$_REQUEST['city1']."')";
			
			mysql_query($sql_in_view);
		}
		
		
			
		$msg='<center><br><span style="color:green;font-size:12px;font-weight:bold;padding-top:20px;" ><img src="admin/images/tick_icon35.gif" width="15" height="15">&nbsp;Profile Updated successfully!</span></center><br>';
		
		return $msg;
	}
	
	function save_misc_user()
	{
		$file_name = $_FILES['fileToUpload']['name'];
		$time_stamp=time();
		$new_file_name=$time_stamp.'_Ash_'.$file_name;
		$file= "uploads/".$new_file_name;
		move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $file);
		
		//$_SESSION['varUserID']
		
		if(trim($_REQUEST['pics_comments'])=="Say something about this music.." || trim($_REQUEST['pics_comments'])=="" || trim($_REQUEST['pics_comments'])=="Say something about this video.."|| trim($_REQUEST['pics_comments'])=="Say something about this picture..")
		{
			$comments="";
		}
		else 
		{
			$comments=$_REQUEST['pics_comments'];
		}
		
		
		
		  $sql ="INSERT INTO `tbl_share` 
		   						(`userId` , `type` , `comment`, `file_name`,`date`)
			 	 
					VALUES ('".$_SESSION['varUserID']."', '".$_REQUEST['cmd']."', 
								'".$comments."', '".$new_file_name."',now() )"; 
		 
		   
		 mysql_query($sql);
		 
		$msg='<center><br><span style="color:green;font-size:12px;font-weight:bold;padding-top:20px;" ><img src="admin/images/tick_icon35.gif" width="15" height="15">&nbsp;Uploaded Completed!</span></center><br>';
		
		return $msg;
	}
	
	
	
	function share_post()
	{
		
		$get_post=mysql_query("select * from `tbl_share` where id='".$_REQUEST['shareid']."'");
		
		$row_posts=mysql_fetch_array($get_post);
		

			
		
		  $sql ="INSERT INTO `tbl_share` 
		   						(`userId` , `type` , `comment`, `file_name`,`date`)
			 	 
					VALUES ('".$_SESSION['varUserID']."', '".$row_posts['type']."', 
								'".$row_posts['comment']."', '".$row_posts['file_name']."',now() )"; 
		 
		   
		 mysql_query($sql);
		 
		$msg='<center><br><span style="color:green;font-size:12px;font-weight:bold;padding-top:20px;" ><img src="admin/images/tick_icon35.gif" width="15" height="15">&nbsp;Shared on your wall successfully!</span></center><br>';
		
		return $msg;
	}
	
	
	
	function add_post()
	{
		
		
		
		  $sql ="INSERT INTO `tbl_share` 
		   						(`userId` , `type` , `comment`, `file_name`,`date`)
			 	 
					VALUES ('".$_SESSION['varUserID']."', 'SHARE', 
								'".$_REQUEST['music_comments_add']."', '',now() )"; 
		 
		   
		 mysql_query($sql);
		 
		$msg='<center><br><span style="color:green;font-size:12px;font-weight:bold;padding-top:20px;" ><img src="admin/images/tick_icon35.gif" width="15" height="15">&nbsp;Posted on your wall successfully!</span></center><br>';
		
		return $msg;
	}
	
	function add_post_wall()
	{
		  $sql ="INSERT INTO `tbl_share` 
		   						(`userId` , `type` , `comment`, `posted_userid`,`date` )
			 	 
					VALUES ('".$_REQUEST['userId']."', 'WALL', 
								'".$_REQUEST['music_comments_add']."', '".$_SESSION['varUserID']."'  ,now() )"; 
		 
		   
		 mysql_query($sql);
		 
		$msg='<center><br><span style="color:green;font-size:12px;font-weight:bold;padding-top:20px;" ><img src="admin/images/tick_icon35.gif" width="15" height="15">&nbsp;Posted on your wall successfully!</span></center><br>';
		
		return $msg;
	}
	
	
	function del_misc_user()
	{
		$sql="delete from  tbl_share where id = '".$_REQUEST['id']."'";
		$res = mysql_query($sql) or die(mysql_error());
		
		
		$msg='<center><br><span style="color:green;font-size:12px;font-weight:bold;padding-top:20px;" ><img src="admin/images/tick_icon35.gif" width="15" height="15">&nbsp;Record Deleted successfully!</span></center><br>';
		return $msg;
	}
	function save_comments()
	{
	
		if($_REQUEST['UserIN']!="")
		{
			$sql =" INSERT INTO `tbl_comment` 
   						(`U_ID` , `comment_by` , `share_id`, `C_Description`,`C_date`)
	 	 
			VALUES ('".$_SESSION['varUserID']."', '".$_SESSION['varUserID']."', 
						'".$_REQUEST['share_id']."', '".$_REQUEST['comments']."',now())"; 
			
		}
		else 
		{
			$sql =" INSERT INTO `tbl_comment` 
   						(`U_ID` , `comment_by` , `share_id`, `C_Description`,`C_date`)
	 	 
			VALUES ('".$_REQUEST['userId']."', '".$_SESSION['varUserID']."', 
						'".$_REQUEST['share_id']."', '".$_REQUEST['comments']."',now())"; 
		 
		}  
		 mysql_query($sql);
		 
		//$msg='<center><br><span style="color:green;font-size:12px;font-weight:bold;padding-top:20px;" ><img src="admin/images/tick_icon35.gif" width="15" height="15">&nbsp;Uploaded Completed!</span></center><br>';
		
		return $msg;
	}
	
	function del_comments()
	{
		$sql="delete from  tbl_comment where C_ID = '".$_REQUEST['C_ID']."'";
		$res = mysql_query($sql) or die(mysql_error());
		
		
		$msg='<center><br><span style="color:green;font-size:12px;font-weight:bold;padding-top:20px;" ><img src="admin/images/tick_icon35.gif" width="15" height="15">&nbsp;Comment deleted successfully!</span></center><br>';
		return $msg;
	}
	
	
	function del_posts()
	{
		$sql="delete from  tbl_share where id = '".$_REQUEST['id']."'";
		$res = mysql_query($sql) or die(mysql_error());
		
		
		$msg='<center><br><span style="color:green;font-size:12px;font-weight:bold;padding-top:20px;" ><img src="admin/images/tick_icon35.gif" width="15" height="15">&nbsp;Post deleted successfully!</span></center><br>';
		return $msg;
		
	}
	
	
	function delete_message()
	{
		
		$sql="delete from  tbl_message where m_id = '".$_REQUEST['m_gid']."'";
		$res = mysql_query($sql) or die(mysql_error());
		
		
		$msg='<center><br><span style="color:green;font-size:12px;font-weight:bold;padding-top:20px;" ><img src="admin/images/tick_icon35.gif" width="15" height="15">&nbsp;Message deleted successfully!</span></center><br>';
		return $msg;
	}

	
}



?>