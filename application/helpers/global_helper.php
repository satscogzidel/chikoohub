<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


	function CheckLogged() {  
		
	$CI =& get_instance();	
	if($CI->session->userdata('user_id'))
    return true;
    else
    return false;
    }
	 
	function CheckUserWordPress($BlogId)
	{
			  $CI = & get_instance();
		$DataBase = $CI->mongoconnect->DataBase();
		$Where    = $DataBase->user->find( array( "wordpress_id" => $BlogId ), array("wordpress_id"));
		$NumRows  = $Where->count();
		return $NumRows;	
		
	}
	
	function CheckUserTwitter($TwitterId)
	{
			$CI = & get_instance();
		$DataBase = $CI->mongoconnect->DataBase();
		$Where    = $DataBase->user->find( array( "twitter_id" => $TwitterId ), array("twitter_id"));
		$NumRows  = $Where->count();
		return $NumRows;

	}
	
		function CheckUserInstagram($InstagramId)
	{
			  $CI = & get_instance();
		$DataBase = $CI->mongoconnect->DataBase();
		$Where    = $DataBase->user->find( array( "instagram_id" => $InstagramId ), array("instagram_id"));
		$NumRows  = $Where->count();
		return  $NumRows;
	}
		
		function CheckUserFaceBook($FaceBookId)
	{
		
			  $CI = & get_instance();
		$DataBase = $CI->mongoconnect->DataBase();
		$Where    = $DataBase->user->find( array( "facebook_id" => $FaceBookId ), array("facebook_id"));
		$NumRows  = $Where->count();
		return $NumRows;
		
	}
	
	
	function CheckWordPress($iUserId)
	{
			  $CI = & get_instance();
		$DataBase = $CI->mongoconnect->DataBase();
		$Where    = $DataBase->user->find( array ('$and' => array ( array( "user_id" => new MongoId($iUserId)  ),array( "wordpress_id" => ""))), array("wordpress_id"));
		$NumRows  = $Where->count();
		return $NumRows;		
	}
	
	function CheckInstagram($iUserId)
	{
			  $CI = & get_instance();
		$DataBase = $CI->mongoconnect->DataBase();
		$Where    = $DataBase->user->find( array ('$and' => array ( array( "user_id" => new MongoId($iUserId)  ),array( "instagram_id" => ""))), array("instagram_id"));
		$NumRows  = $Where->count();
		return $NumRows;
		
	}
	
		function CheckFacebook($iUserId)
	{
			  $CI = & get_instance();
		$DataBase = $CI->mongoconnect->DataBase();
		$Where    = $DataBase->user->find( array ('$and' => array (array( "user_id" =>  new MongoId($iUserId) ), array( "facebook_id" => ""))), array("facebook_id"));
		$NumRows  = $Where->count();
		return $NumRows;
	}
	
		function CheckTwitter($iUserId)
	{
			  $CI = & get_instance();
		$DataBase = $CI->mongoconnect->DataBase();
		$Where    = $DataBase->user->find( array ('$and' => array (array( "user_id" =>  new MongoId($iUserId) ), array( "twitter_id" => ""))), array("twitter_id"));
		$NumRows  = $Where->count();
		return $NumRows;
	}
	
		function CheckPostStatus($iUserId)
	{
		      $CI = & get_instance();
		$DataBase = $CI->mongoconnect->DataBase();
		$Where    = $DataBase->user->find( array ('$and' => array (array( "user_id" =>  new MongoId($iUserId) ), array( "post_status" => "draft"))), array("post_status"));
		$NumRows  = $Where->count();
		return $NumRows;
	}
	
	function GetUserIdByBlogId($BlogId)
	{
				
			  $CI = & get_instance();
		$DataBase = $CI->mongoconnect->DataBase();
		$Where    = $DataBase->user->find( array( "wordpress_id" =>  $BlogId ), array("user_id"));
		
		foreach($Where as $Result) {
			$iUserId = $Result['user_id'];
		}
		return $iUserId;
		
	}
	
	function GetUserIdByTwitterId($TwitterId)
	{
		  	  $CI = & get_instance();
		$DataBase = $CI->mongoconnect->DataBase();
		$Where    = $DataBase->user->find( array( "twitter_id" =>  $TwitterId ), array("user_id"));
		
		foreach($Where as $Result) {
			$iUserId = $Result['user_id'];
		}
		return $iUserId;

	}
	
		function GetUserIdByInstagramId($InstagramId)
	{
		
  			  $CI = & get_instance();
		$DataBase = $CI->mongoconnect->DataBase();
		$Where    = $DataBase->user->find( array( "instagram_id" =>  $InstagramId ), array("user_id"));
		
		foreach($Where as $Result) {
			$iUserId = $Result['user_id'];
		}
		return $iUserId;

	}
	
		function GetUserIdByFaceBookId($FacebookId)
	{
  			  $CI = & get_instance();
		$DataBase = $CI->mongoconnect->DataBase();
		$Where    = $DataBase->user->find( array( "facebook_id" =>  $FacebookId ), array("user_id"));
		
		foreach($Where as $Result) {
			$iUserId = $Result['user_id'];
		}
		return $iUserId;

	}
	
	function CheckFacebookStatus($FacebookId) {

			  $CI = & get_instance();
		$DataBase = $CI->mongoconnect->DataBase();
		$Where    = $DataBase->facebook->find(array('$and' => array ( array( "facebook_id" => $FacebookId ), array('facebook_status' => 0))), array("facebook_id"));
		$NumRows  = $Where->count();
		return $NumRows;

	}
	
	function CheckWordPressStatus($BlogId) {
		
			  $CI = & get_instance();
		$DataBase = $CI->mongoconnect->DataBase();
		$Where    = $DataBase->wordpress->find(array('$and' => array ( array( "wordpress_id" => $BlogId ), array('wordpress_status' => 0))),array("wordpress_id"));
		$NumRows  = $Where->count();
		return $NumRows;
				
	}
	
	function CheckInstagramStatus($InstagramId) {
		
			  $CI = & get_instance();
		$DataBase = $CI->mongoconnect->DataBase();
		$Where    = $DataBase->instagram->find(array('$and' => array ( array( "instagram_id" => $InstagramId ), array('instagram_status' => 0))),array("instagram_id"));
		$NumRows  = $Where->count();
		return $NumRows;
		
	}
	
	function CheckTwitterStatus($TwitterId) {
		
			  $CI = & get_instance();
		$DataBase = $CI->mongoconnect->DataBase();
		$Where    = $DataBase->twitter->find(array('$and' => array ( array( "twitter_id" => $TwitterId ), array('twitter_status' => 0))), array("twitter_id"));
		$NumRows  = $Where->count();
		return $NumRows;

	}
	
	function GetWordpressTokenBlogId($iUserId) {
		
		$CI       = & get_instance();
		$DataBase = $CI->mongoconnect->DataBase();
		$Where    = $DataBase->user->find( array( "user_id" =>  new MongoId($iUserId)));
		
		foreach($Where as $Result) {
			$WordpressId = $Result['wordpress_id'];
			$PostStatus  = $Result['post_status'];  
		}
		
			  $CI = & get_instance();
		$DataBase = $CI->mongoconnect->DataBase();
		$Where    = $DataBase->wordpress->find( array( "wordpress_id" =>  $WordpressId));
		
		foreach($Where as $Result) {
			$Result['post_status'] = $PostStatus;
			return $Result;
		}
	}
	
	function CheckPost($PostId, $iUserId) {
		
			  $CI = & get_instance();
		$DataBase = $CI->mongoconnect->DataBase();
		$Where    = $DataBase->post->find(array ('$and' => array ( array( "user_id" => new MongoId($iUserId)  ),array( "post_id" => $PostId))), array("post_id"));
		$NumRows  = $Where->count();
		return $NumRows;

	}
	
	function GetUsernameByFacebookId($FacebookId) {
		
			  $CI = & get_instance();
		$DataBase = $CI->mongoconnect->DataBase();
		$Where    = $DataBase->facebook->find( array( "facebook_id" =>  $FacebookId));
		
		foreach($Where as $Result) {
			$FacebookUsername = $Result['facebook_user_name'];
			return $FacebookUsername;
		}
		
	}
	
		
	function GetFaceBookIdByUserId($iUserId)  {
		
  			  $CI = & get_instance();
		$DataBase = $CI->mongoconnect->DataBase();
		$Where    = $DataBase->user->find( array( "user_id" =>  new MongoId($iUserId)),array('facebook_id'));

		foreach($Where as $Result) {
			$FacebookId = $Result['facebook_id'];
		}

		return $FacebookId;

	}
	
	function GetInstagramDetailsByUserId($iUserId) {
	
  			  $CI = & get_instance();
		$DataBase = $CI->mongoconnect->DataBase();
		$Where    = $DataBase->user->find( array( "user_id" =>  new MongoId($iUserId)),array('instagram_id'));
		
		foreach($Where as $Result) {
			$InstagramId = $Result['instagram_id'];
		}
		
		$Where    = $DataBase->instagram->find( array( "instagram_id" =>  $InstagramId),array('instagram_id','instagram_access_token'));
		
		foreach($Where as $Result) {
		return $Result;
		}
	}
	function SetFlashMessage($Message, $Type="Information") {
		  
	    $CI =& get_instance();	  
	    $CI->session->set_flashdata('Message', array('Message' => $Message, 'Type' => $Type) );	    
    }
	function GetFlashMessage() {
				
    $CI  = & get_instance();	
    $Message = $CI->session->flashdata('Message');	
		
	    if( $Message != FALSE ) {			
	    return '
	<div class="MessageBox '.$Message['Type'].'"><span>'.$Message['Message'].'</span></div>
	';			
	    }		
    }
?>