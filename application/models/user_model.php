<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//Disable error reporting

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

/**
 * Short description for file
 *
 * PHP version 5
 *
 * User Model file
 *
 * @file  			User Model
 * @author     		Cogzidel Developers
 * @file created   	December 18, 2013
 * @link      		http://www.cogzidel.com
 */

 class User_model extends CI_Model {
    
    function __construct()
    {
        parent::__construct(); 
        
		$params = array('appId'  => $this->config->item('appId'),'secret' => $this->config->item('secret'));
		$this->load->library('facebook',$params);
		$this->load->library('twconnect');
		$this->load->library('wordpress_api');
		$this->load->library('mongoconnect');
       
    }
	
	public function FaceBookOff()
	{
		$DataBase     = $this->mongoconnect->DataBase();
		$iUserId      = $this->session->userdata("user_id");
		$FacebookId   = $this->session->userdata("facebook_id");

		$DataBase->user->update(array("user_id" => $iUserId ), array( '$set' => array ("facebook_id" => "")));
		$DataBase->facebook->update(array("facebook_id" => $FacebookId ), array( '$set' => array ("facebook_status" => 0)));
		
		$this->session->unset_userdata("facebook_id");
		$this->session->unset_userdata("facebook_access_token");
		
		$this->checkUserStatus($iUserId);
		
	}
	public function TwitterOff()
	{
		$DataBase     = $this->mongoconnect->DataBase();
		$iUserId      = $this->session->userdata("user_id");
		$TwitterId    = $this->session->userdata("twitter_id");
		
		$DataBase->user->update(array("user_id" => $iUserId ), array( '$set' => array ("twitter_id" => "")));
		$DataBase->twitter->update(array("twitter_id" => $TwitterId ), array( '$set' => array ("twitter_status" => 0)));
		
		$this->session->unset_userdata("twitter_id");
		$this->session->unset_userdata("twitter_access_token");
		$this->session->unset_userdata("twitter_secret_id");
		
		$this->twconnect->unsettoken();
		$this->checkUserStatus($iUserId);
		
	}
	
		public function InstagramOff()
	{
	    $DataBase     = $this->mongoconnect->DataBase();
		$iUserId      = $this->session->userdata("user_id");
		$InstagramId  = $this->session->userdata("instagram_id");
		
		$DataBase->user->update(array("user_id" => $iUserId ), array( '$set' => array ("instagram_id" => "")));
		$DataBase->instagram->update(array("instagram_id" => $InstagramId ), array( '$set' => array ("instagram_status" => 0)));
		
		$this->session->unset_userdata("instagram_id");
		$this->session->unset_userdata("instagram_access_token");
		
		$this->checkUserStatus($iUserId);
	
	}
	
		public function WordPressOff()
	{
		$DataBase      = $this->mongoconnect->DataBase();
		$iUserId       = $this->session->userdata("user_id");
		$WordpressId   = $this->session->userdata("wordpress_id");
		
		$DataBase->user->update(array("user_id" => $iUserId ), array( '$set' => array ("wordpress_id" => "")));
		$DataBase->wordpress->update(array("wordpress_id" => $WordpressId ), array( '$set' => array ("wordpress_status" => 0)));
		
		$this->session->unset_userdata("wordpress_id");
		$this->session->unset_userdata("wordpress_access_token");
		
		$this->checkUserStatus($iUserId);
		
	}
	
	public function checkUserStatus($iUserId) {
		

		$DataBase = $this->mongoconnect->DataBase();
		$Where    = $DataBase->user->find(array('$and' => array ( array("user_id" =>  new MongoId($iUserId) ), array("facebook_id" => ""),array ( "twitter_id" => "") ,array( "instagram_id" => ""),array( "wordpress_id" => ""))));
		$NumRows  = $Where->count();

		if($NumRows != 0) {
			
			$DataBase->user->remove(array('user_id' => new MongoId($iUserId)));
			
				$this->session->unset_userdata('user_id');
				$this->session->unset_userdata("FacebookAccount");
				$this->session->unset_userdata('TwitterAccount');
				$this->session->unset_userdata('InstagramAccount');
				$this->session->unset_userdata('WordpressAccount');
				
			SetFlashMessage('Your chikoo account Deactivated Successfully'); 
			redirect("login");	
		} 
	}
public function UpdateFacebookPost($iUserId, $FacebookId) {
	
		$Date 	    = strtotime(date("Y-m-d  h:i:s", strtotime("-1 hours")));
		$Via        = "facebook";
		
		$Fql = "SELECT created_time, post_id, actor_id, target_id, message_tags,description, message, attachment, type, permalink FROM stream WHERE source_id = $FacebookId AND created_time > $Date AND is_hidden != 'true' AND ( type = 247 OR type = 46 OR type = 128 OR type = 80 OR type = '')  ORDER BY  created_time DESC LIMIT 100";

     	$Parameter  =   array(
        'method'    => 'fql.query',
        'query'     => $Fql
         );
		  
	     $Config = array(
		 'appId'  => $this->config->item('appId'),
		 'secret' => $this->config->item('secret')
		 );
					 
		  $Facebook         = new Facebook($Config);
		  $FqlResult        = $this->facebook->api($Parameter);
		  $FacebookUsername = GetUsernameByFacebookId($FacebookId);
		  
		  
	if($FqlResult) {
		
		foreach($FqlResult as $FqlValue) {
			
			$ArrayImage = Array();
			    $PostId = $FqlValue['post_id'];
			
			if(CheckPost($PostId,$iUserId) == 0) {
				
				$ArrayPostId  = explode("_",$PostId);
				$SourcePostId = $ArrayPostId[1];				
				$Content      = "https://www.facebook.com/".$FacebookUsername."/posts/".$SourcePostId."?stream_ref=10";
				
				if(!(isset($FqlValue['attachment']['fb_object_type']))) {
					
					if($FqlValue['type'] == 46)
					{
						if(strlen($FqlValue['message']) > 10 ) {
							
							$Title = substr($FqlValue['message'],0,10)."...";
							
						} else {
							
							$Title = $FqlValue['message'];
						}

						$this->WordpressPost($Title,$Content,$iUserId,"status",$PostId,$Via);
					}
				}
	
				if($FqlValue['type'] == 80 && $FqlValue['attachment']['fb_object_type'] != "video" && $FqlValue['attachment']['fb_object_type'] != "photo")
				{
					
					$this->WordpressPost($Message,$Content,$iUserId,"link",$PostId,$Via);
					
				}	
				if(isset($FqlValue['attachment']['fb_object_type'])) {
					
					if($FqlValue['attachment']['fb_object_type'] == "video")
					{
						$Title     = $FqlValue['message'];
						$this->WordpressPost($Title,$Content,$iUserId,"video",$PostId,$Via);
						
					} else if($FqlValue['attachment']['fb_object_type'] == "photo" && $FqlValue['type'] == 247 ) {

						$Title         = $FqlValue['message'];
						$Type          = "image";
					
						$this->WordpressPost($Title,$Content,$iUserId,$Type,$PostId,$Via);
						
					}  else if($FqlValue['attachment']['fb_object_type'] == "photo" &&  $FqlValue['type'] == 80 ) {

						$Title         = $FqlValue['description'];
						$Type          = "image";
					
						$this->WordpressPost($Title,$Content,$iUserId,$Type,$PostId,$Via);
						
					} else if($FqlValue['attachment']['fb_object_type'] == "album" && ( $FqlValue['type'] == "" || $FqlValue['type'] == 46 || $FqlValue['type'] == 247 ) ) {
						
						if($FqlValue['message'] == "") {
							
						$Message = $FqlValue['attachment']['name'];
	
						} else {
							
						$Message = $FqlValue['message'];
							
						}
						$Title         = $Message;
						$Type          = "gallery";
												
						$this->WordpressPost($Title,$Content,$iUserId,$Type,$PostId,$Via);
					}
				}
			}
		}
	}
}
public function UpdateInstagramPost($iUserId,$InstagramId,$InstagramToken) {
	
		$Date 	 = strtotime(date("Y-m-d  h:i:s", strtotime("-1 hours")));
		$Via     = "instagram";
		
		$TrendsUrl = "https://api.instagram.com/v1/users/". $InstagramId ."/media/recent/?access_token=".$InstagramToken."&min_timestamp=".$Date;
		
		$Curl = curl_init();
		curl_setopt($Curl, CURLOPT_URL, $TrendsUrl);
		curl_setopt($Curl, CURLOPT_RETURNTRANSFER, 1);
		$CurlResult = curl_exec($Curl);
		curl_close($Curl);
	
    	   $Feeds   = json_decode($CurlResult, true);
		$FeedsCount = count($Feeds['data']);
		
		for($ImageId = 0; $ImageId < $FeedsCount; $ImageId++) {
			
			$PostId = $Feeds['data'][$ImageId]['id'];
		
			if(CheckPost($PostId,$iUserId) == 0) {

					
						if(strlen($Feeds['data'][$ImageId]['caption']['text']) > 10 ) {
							
							$Title = substr($Feeds['data'][$ImageId]['caption']['text'],0,10)."...";
							
						} else {
							
							$Title = $Feeds['data'][$ImageId]['caption']['text'];
						}

				  $Content = $Feeds['data'][$ImageId]['link'];
					 $Type = $Feeds['data'][$ImageId]['type'];
					 
				$this->WordpressPost($Title,$Content,$iUserId,$Type,$PostId,$Via);
		
		}
	
	}

}
public function WordpressPost($Title,$Content,$iUserId,$Type,$PostId,$Via) {
	
		 	$DataBase       = $this->mongoconnect->DataBase();
	$WordpressDetails 	    = GetWordpressTokenBlogId($iUserId);
	$WordpressBlogId 	    = $WordpressDetails["wordpress_id"];
	$WordpressAccessToken   = $WordpressDetails["wordpress_access_token"];
	$WordpressPostStatus    = $WordpressDetails["post_status"];
	
		$Options  = array (
		  'http' =>
				  array (
					    'ignore_errors' => true,
					    'method' => 'POST',
					    'header' =>
								    array (
								      0 => 'authorization: Bearer '.$WordpressAccessToken,
								      1 => 'Content-Type: application/x-www-form-urlencoded',
								    ),
					    'content' => http_build_query(  
								      array (
								        'title'   => $Title,
								        'content' => $Content,
								        'status'  => $WordpressPostStatus,
								        'format'  => $Type
								      )
					    ),
				  ),
		);
		
		$Context  = stream_context_create( $Options );
		
		$Response = file_get_contents(
		  'https://public-api.wordpress.com/rest/v1/sites/'.$WordpressBlogId.'/posts/new/',
		  false,
		  $Context
		);
		$Response = json_decode( $Response );
		
		if($Response)
		{
			$InsertArray    		= array (
			"user_id"      			=> $iUserId,
			"post_id"      			=> $PostId,
			"post_title"   			=> $Title,
			"post_content" 			=> $Content,
			"post_via"     			=> $Via,
			"post_type"           	=> $Type,
			"imported_datetime"  	=> date('Y-m-d H:i:s', time()),
			);
			
			$DataBase->post->insert($InsertArray);
		}
	}

public function MediaImagePostToWordpress($Title,$Content,$ArrayImage,$iUserId,$Type,$PostId,$Via) {
	
	    $DataBase   = $this->mongoconnect->DataBase();
		$ImageCount = count($ArrayImage);
		$MediaArray = Array();
		
		for($ImageId = 0; $ImageId < $ImageCount; $ImageId++)
		{
				  $ImageFullName = $ArrayImage[$ImageId];
					   $ImageURL = urldecode($ImageFullName);
              	   $FeedImageURL = str_replace(" ",'%20', $ImageURL);	
					 
             if ($FeedImageURL) {				 

                $FilePermission = fopen($FeedImageURL,"rb");
				 
                if ($FilePermission) {

                    	          		   $FilePath = "images/post/"; // Directory to upload files to.
                    	           $ImageInformation = pathinfo($ImageFullName);
								   $ImageName        = $ImageInformation["basename"];
					                $NewFile         = fopen($FilePath . $ImageName, "wb"); // creating new file on local server
	                        
									 if ($NewFile) {
	                        	
	                          			  while (!feof($FilePermission)) {                   
	                                		fwrite($NewFile,fread($FilePermission,1024 * 8),1024 * 8); 
	                           			  }
	
	 								$PulledFile = file_get_contents($ImageURL,$ImageName);
										$Result = file_put_contents('images/post/'.$ImageName,$PulledFile);
										
										$MediaArray['media['.$ImageId.']'] = "@images/post/".$ImageName; // Array of Feeds images
	
									}
					}
				}
		}

		if(isset($MediaArray)) {
	
								$WordpressDetails 	    = GetWordpressTokenBlogId($iUserId);
								$WordpressBlogId 	    = $WordpressDetails["wordpress_id"];
								$WordpressAccessToken   = $WordpressDetails["wordpress_access_token"];
								$WordpressPostStatus    = $WordpressDetails["post_status"];
									
								$Headers = array(
							    				'Content-type: multipart/form-data',
							    				'Authorization: Bearer '.$WordpressAccessToken,
							    				'Keep-Alive: 1'
								);
							
								$Post = array(
											'title'  => $Title,
							 				'format' => $Type,
							 				'status' => $WordpressPostStatus,
								);
							
								$Post = array_merge($Post, $MediaArray);
				
							
								$CurlSet = curl_init();
								curl_setopt($CurlSet, CURLOPT_URL, "https://public-api.wordpress.com/rest/v1/sites/".$WordpressBlogId."/posts/new/" );

								curl_setopt($CurlSet, CURLOPT_HTTPHEADER, $Headers );
								curl_setopt($CurlSet, CURLOPT_RETURNTRANSFER, 1);
								curl_setopt($CurlSet, CURLOPT_CONNECTTIMEOUT,2);
								curl_setopt($CurlSet, CURLOPT_VERBOSE, 1);
								curl_setopt($CurlSet, CURLOPT_POSTFIELDS,$Post);
								curl_setopt($CurlSet, CURLOPT_SSL_VERIFYHOST, 0);
								curl_setopt($CurlSet, CURLOPT_SSL_VERIFYPEER, 0);
							
								$Response = json_decode( curl_exec( $CurlSet ) );
								curl_close ($CurlSet);

								if($Response)
									{
										$InsertArray = array (
										"user_id"      			=> $iUserId,
										"post_id"      			=> $PostId,
										"post_title"  			=> $Title,
										"post_content" 			=> $Content,
										"post_via"     			=> $Via,
										"post_type"    			=> $Type,
										"imported_datetime"  	=> date('Y-m-d H:i:s', time()),
										);
										
										$DataBase->post->insert($InsertArray);
										

									}
 			}
}
public function UpdateSocialId($iUserId,$SocialId,$Table,$Field) {
	
		$DataBase   = $this->mongoconnect->DataBase();
		$DataBase->$Table->update(array("user_id" =>  new MongoId($iUserId) ), 
										array( '$set'   => 
														array ($Field => $SocialId ,
														       "last_logged_datetime" 	=> date('Y-m-d H:i:s', time()))
											 )
										);
}

public function UpdateSocialStatus($SocialId,$Table,$IdField,$StatusField) {
	
		$DataBase   = $this->mongoconnect->DataBase();
		$DataBase->$Table->update(array($IdField => $SocialId ), 
											array( '$set' =>
															array ($StatusField => 1)
												 )
											);
}
public function InsertSocialDetails($SocialUserDetails,$Table) {
	
		$DataBase   = $this->mongoconnect->DataBase();
		$DataBase->$Table->insert($SocialUserDetails);
}
public function ChangePostStatus($iUserId, $Status) {
	
		$DataBase   = $this->mongoconnect->DataBase();
		$DataBase->user->update(array("user_id" => $iUserId ), 
							     array( '$set' =>
												array ("post_status" => $Status)
									   )
								);
}
public function GetUserIdBasedWordpress() {
	
		$DataBase = $this->mongoconnect->DataBase();
		$Where    = $DataBase->user->find(array("wordpress_id" => array('$ne' => "")),array('user_id'));
		
		return $Where;
}
} ?>