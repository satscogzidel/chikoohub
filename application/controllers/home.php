<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
//Disable error reporting
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
/**
 * Short description for file
 *
 * PHP version 5
 *
 * home file
 *
 * @file  			Home
 * @author     		Cogzidel Developers
 * @file created   	December 10, 2013
 * @link      		http://www.cogzidel.com
 */
class Home extends CI_Controller {
	function __construct() {
    	parent::__construct();	 
		$params = array('appId'  => $this->config->item('appId'),'secret' => $this->config->item('secret'));
			
		$this->load->library('facebook',$params);
		$this->load->library('twconnect');
		$this->load->library('Instagram_api'); 
		$this->load->library('wordpress_api');
		$this->load->library('mongoconnect');
		
		$this->load->model('user_model');
	    }
 
function facebook_connect() {
						   
				              $Token = $this->facebook->getAccessToken();
			   $Data['user_profile'] = $this->facebook->api('/me'); 
			   $ProfileImage         = "https://graph.facebook.com/".$Data['user_profile']['id']."/picture?type=large";
			   $FacebookId 		     = $Data['user_profile']['id'];

               $FacebookUserDetails = array(  	'facebook_id'        	 => $FacebookId,
               									'facebook_email'     	 => $Data['user_profile']['email'],
               									'facebook_user_name' 	 => $Data['user_profile']['username'],
               									'facebook_first_name' 	 => $Data['user_profile']['first_name'],
               									'facebook_last_name' 	 => $Data['user_profile']['last_name'],
               									'facebook_display_name'  => $Data['user_profile']['name'],
               									'facebook_location'      => $Data['user_profile']['location']['name'],
               									'facebook_access_token'  => $Token,
               									'facebook_profile_image' => $ProfileImage,
               									'facebook_gender'        => $Data['user_profile']['gender'],
               									'facebook_birthday'      =>	$Data['user_profile']['birthday'],
               									'facebook_status'        => 1
               								 );				 

	if($this->session->userdata("FacebookAccount")) {
		
		$iUserId = $this->session->userdata("user_id");
		$this->session->unset_userdata('FacebookAccount');
		
			$sess_array = array(	
			"facebook_id"            => $FacebookId,
			"facebook_access_token"  => $Token,
			);
			
		$this->session->set_userdata($sess_array);
		
		if(CheckUserFaceBook($FacebookId) == 0) {
			
			    $this->user_model->UpdateSocialId($iUserId,$FacebookId,"user","facebook_id");
			
			if(CheckFacebookStatus($FacebookId) == 0) {
				
				$this->user_model->InsertSocialDetails($FacebookUserDetails,"facebook");
				
			} else {
				
				$this->user_model->UpdateSocialStatus($FacebookId,"facebook","facebook_id","facebook_status");
	
			}
			
			if(CheckWordPress($iUserId) == 0) {
				$this->user_model->UpdateFacebookPost($iUserId,$FacebookId);
			}
			SetFlashMessage('Facebook account activated Successfully');  
			redirect("home/AccountSettings");

		} else {
			SetFlashMessage("This Facebook account is aleady registered in Chikoo");
			redirect("home/AccountSettings");
		}

	
	} else {
								
		if(!CheckLogged())	{
				
			$SessionArray = array(	
			"facebook_id"            => $FacebookId,
			"facebook_access_token"  => $Token,
			);
			$this->session->set_userdata($SessionArray);
	
			if(CheckUserFaceBook($FacebookId) == 0) {
				
				$MongoId = new MongoId();
				
				$InsertArray = array(
					"user_id"           	=> $MongoId,
			 		"facebook_id"       	=> $Data['user_profile']['id'],
			 		"twitter_id"			=> "",
			 		"instagram_id"			=> "",
			 		"wordpress_id"			=> "",
					"user_status"       	=> 1,
			 		"post_status"    	   	=> "draft",
			 		"created_datetime"  	=> date('Y-m-d H:i:s', time()),
			 		"last_logged_datetime" 	=> date('Y-m-d H:i:s', time())
				);
				$this->user_model->InsertSocialDetails($InsertArray,'user');
				$iUserId = $MongoId;

					if(CheckFacebookStatus($FacebookId) == 0) {
						
						SetFlashMessage("This Facebook account registered in Chikoo");
						$this->user_model->InsertSocialDetails($FacebookUserDetails,'facebook');
						
					} else {

						$this->user_model->UpdateSocialStatus($FacebookId,"facebook","facebook_id","facebook_status");
					}
				
				$this->session->set_userdata("user_id",$iUserId);
				
			} else {
				
	 			$iUserId = GetUserIdByFaceBookId($FacebookId);
	 			$this->session->set_userdata("user_id",$iUserId);
			}
			
			redirect("home/AccountSettings");
			
		} else {
			
		SetFlashMessage("You must login to access this page");
		redirect("login");
		
		}
	}						   
}

function instagram_detail()
{
	if(isset($_GET['code']) && $_GET['code'] != '') {
		
		$InstagramDetails = $this->instagram_api->authorize($_GET['code']);

		$InstagramUserDetails = array(
			"instagram_id"			 	=> $InstagramDetails->user->id,
			"instagram_username" 		=> $InstagramDetails->user->username,
			"instagram_full_name"		=> $InstagramDetails->user->full_name,
			"instagram_access_token"	=> $InstagramDetails->access_token,
			"instagram_profile_image"	=> $InstagramDetails->user->profile_picture,
			"instagram_status"			=> 1
		);		

		   $InstagramId = $InstagramDetails->user->id;
		$InstagramToken = $InstagramDetails->access_token;

		if($this->session->userdata("InstagramAccount")) {
			
			$this->session->unset_userdata('InstagramAccount');
			$iUserId = $this->session->userdata("user_id");	
		
			$SessionArray = array(
				"instagram_id"    => $InstagramDetails->user->id,
			 	"instagram_token" => $InstagramDetails->access_token
			);
			$this->session->set_userdata($SessionArray);
	
			if(CheckUserInstagram($InstagramId) == 0) {
				
				    $this->user_model->UpdateSocialId($iUserId,$InstagramId,"user","instagram_id");
				
				if(CheckInstagramStatus($InstagramId) == 0) {
					
					$this->user_model->InsertSocialDetails($InstagramUserDetails,'instagram');
					
				} else {
					
					$this->user_model->UpdateSocialStatus($InstagramId,"instagram","instagram_id","instagram_status");
						
				}
								
				if(CheckWordPress($iUserId) == 0) {
					$this->user_model->UpdateInstagramPost($iUserId,$InstagramId,$InstagramToken);
				}
				SetFlashMessage('Instagram account activated Successfully');  
				redirect("home/AccountSettings");
			} else {
				SetFlashMessage("This Instagram account is aleady registered in Chikoo");
				redirect("home/AccountSettings");
			}
			
		} else { 
				
			if(!CheckLogged()) {	
			
				$SessionArray = array(
		 		"instagram_id"    => $InstagramDetails->user->id,
		 		"instagram_token" => $InstagramDetails->access_token
				);
				$this->session->set_userdata($SessionArray);
	
				if( CheckUserInstagram($InstagramId) == 0) {
						
					$MongoId = new MongoId();
				
					$InsertArray = array(
					"user_id"           	=> $MongoId,
			 		"facebook_id"       	=> "",
			 		"twitter_id"			=> "",
			 		"instagram_id"			=> $InstagramDetails->user->id,
			 		"wordpress_id"			=> "",
					"user_status"       	=> 1,
			 		"post_status"    	   	=> "draft",
			 		"created_datetime"  	=> date('Y-m-d H:i:s', time()),
			 		"last_logged_datetime" 	=> date('Y-m-d H:i:s', time())
					);
					$this->user_model->InsertSocialDetails($InsertArray,'user');
					$iUserId = $MongoId;
					
					if(CheckInstagramStatus($InstagramId) == 0) {
						
					SetFlashMessage("This Instagram account registered in Chikoo");
					$this->user_model->InsertSocialDetails($InstagramUserDetails,'instagram');
						
					} else {

					$this->user_model->UpdateSocialStatus($InstagramId,"instagram","instagram_id","instagram_status");
								
					}
					$this->session->set_userdata("user_id",$iUserId);
							
				}else {
	
					 $iUserId = GetUserIdByInstagramId($InstagramId);
	 				 $this->session->set_userdata("user_id",$iUserId);
				}

					redirect("home/AccountSettings");
			
			} else {
				SetFlashMessage("You must login to access this page");
				redirect("login");
		
			}
		} 
	}
}

function wordpress_detail()
{
	if(isset($_GET['error']) && $_GET['error'] == 'access_denied') {
		
		redirect("login");
		
	} else {
		
	if(isset($_GET['code']) && $_GET['code'] != '') {
		
		$WordpressUserDetails = $this->wordpress_api->authorize($_GET['code']);
		$UserDetails          = $this->wordpress_api->getUserdetails($WordpressUserDetails->access_token);
	
		$UserArray = array(
		"wordpress_id"          	=> $WordpressUserDetails->blog_id,
		"wordpress_user_id"			=> $UserDetails->ID,
		"wordpress_email"			=> $UserDetails->email,
		"wordpress_display_name"	=> $UserDetails->display_name,
		"wordpress_user_name"		=> $UserDetails->username,
		"wordpress_access_token"	=> $WordpressUserDetails->access_token,
		"wordpress_profile_image"	=> $UserDetails->avatar_URL,
		"wordpress_status"			=> 1
		); 
	
		if($this->session->userdata("WordpressAccount")) {
		
			$iUserId = $this->session->userdata("user_id");
			$this->session->unset_userdata('WordpressAccount');
			
			$WordpressToken =  $WordpressUserDetails->access_token;
			$WordpressBlogId = $WordpressUserDetails->blog_id;
			
			$sess_array = array(
			"wordpress_access_token"  => $WordpressToken,
			"wordpress_id"            => $WordpressBlogId
			);
			$this->session->set_userdata($sess_array);
		
				if( CheckUserWordPress($WordpressBlogId) == 0) {
					
					    $this->user_model->UpdateSocialId($iUserId,$WordpressBlogId,"user","wordpress_id");
													
						if(CheckWordPressStatus($WordpressBlogId) == 0) {
							
						$this->user_model->InsertSocialDetails($UserArray,'wordpress');
							
						} else {
							
							$this->user_model->UpdateSocialStatus($WordpressBlogId,"wordpress","wordpress_id","wordpress_status");
																		
							if(CheckFacebook($iUserId) == 0) {
								
								$FacebookId = GetFaceBookIdByUserId($iUserId);
								
								$this->user_model->UpdateFacebookPost($iUserId,$FacebookId);
							}
							
																		
							if(CheckInstagram($iUserId) == 0) {
								
								$InstagramDetails = GetInstagramDetailsByUserId($iUserId);
								$InstagramId      = $InstagramDetails['instagram_id'];
								$InstagramToken   = $InstagramDetails['instagram_access_token'];
								
								$this->user_model->UpdateInstagramPost($iUserId,$InstagramId,$InstagramToken);
							}

						}
					SetFlashMessage('Wordpress account activated Successfully');  
					redirect("home/AccountSettings");
				} else {
					SetFlashMessage("This Wordpress account is aleady registered in Chikoo");
					redirect("home/AccountSettings");
				}
		
	} else { 

		if(!CheckLogged()) {
			
			$WordpressToken =  $WordpressUserDetails->access_token;
			$WordpressBlogId = $WordpressUserDetails->blog_id;	
			
			$sess_array = array(
			"wordpress_access_token"  => $WordpressToken,
			"wordpress_id"            => $WordpressBlogId
			);
			$this->session->set_userdata($sess_array);
	
			if( CheckUserWordPress($WordpressBlogId) == 0) {
				
						$MongoId = new MongoId();
						
						$InsertArray = array(
							"user_id"           	=> $MongoId,
					 		"facebook_id"       	=> "",
					 		"twitter_id"			=> "",
					 		"instagram_id"			=> "",
					 		"wordpress_id"			=> $WordpressBlogId,
							"user_status"       	=> 1,
					 		"post_status"    	   	=> "draft",
					 		"created_datetime"  	=> date('Y-m-d H:i:s', time()),
					 		"last_logged_datetime" 	=> date('Y-m-d H:i:s', time())
						);
						$this->user_model->InsertSocialDetails($InsertArray,'user');
						$iUserId = $MongoId;
				
					if(CheckWordPressStatus($WordpressBlogId) == 0) {
					SetFlashMessage("This Wordpress account registered in Chikoo");
					$this->user_model->InsertSocialDetails($UserArray,'wordpress');
						
					} else {
					$this->user_model->UpdateSocialStatus($WordpressBlogId,"wordpress","wordpress_id","wordpress_status");
					
					}

				$this->session->set_userdata("user_id",$iUserId);
		    } else {

	 			$iUserId = GetUserIdByBlogId($WordpressBlogId);
	 			$this->session->set_userdata("user_id",$iUserId);
				
			}
			redirect("home/AccountSettings");
			
		} else {

			redirect("login");
		
		}
  	}
  }	
  }
}

public function WordPressOff()
{
	if(CheckLogged()){
		$this->user_model->WordPressOff();
		SetFlashMessage('Wordpress Account Deactivated Successfully'); 		
	} else {
		SetFlashMessage("You must login to access this page");
	}
	redirect("home/AccountSettings");
	
}

public function FaceBookOff()
{
	if(CheckLogged()){
		$this->user_model->FaceBookOff();
		SetFlashMessage('Facebook Account Deactivated Successfully'); 		
	} else {
		SetFlashMessage("You must login to access this page");
	}
	redirect("home/AccountSettings");
}

public function TwitterOff()
{
	if(CheckLogged()){
			$this->user_model->TwitterOff();
			$this->twconnect->unsettoken();
			
			SetFlashMessage('Twitter Account Deactivated Successfully'); 
	} else {
			SetFlashMessage("You must login to access this page");
	}
	redirect("home/AccountSettings");
	
}

public function InstagramOff()
{
	if(CheckLogged()){
		$this->user_model->InstagramOff();
		SetFlashMessage('Instagram Account Deactivated Successfully'); 
	} else {
		SetFlashMessage("You must login to access this page");
	}
    redirect("home/AccountSettings");
}

public function ChangePostStatus()
{
	if(CheckLogged()){
		
	$Status     = $this->input->get("Status");
	$iUserId    =  $this->session->userdata("user_id");
		
	$this->user_model->ChangePostStatus($iUserId, $Status);
	
		if($Status == "publish") {
			SetFlashMessage("Publish status activated Successfully in Chikoo");
		} else {
			SetFlashMessage("Draft status activated Successfully in Chikoo");
		}
													  
	}else{
		SetFlashMessage("You must login to access this page");
	}
	
	redirect("home/AccountSettings");
	
}

public function AccountSettings() {
	
	if(CheckLogged()){
		
			  $data['page_title'] = 'Account Settings';	
				$data['template'] = 'account_settings';

				$this->load->view('template', $data);
	}else{
		SetFlashMessage("You must login to access this page");
		redirect("login");	
	}
}
public function logout(){
	$this->session->sess_destroy();
	redirect("login");
}
}
?>