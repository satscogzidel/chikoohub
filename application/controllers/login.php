<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
 * @file  			Login
 * @author     		Cogzidel Developers
 * @file created   	December 10, 2013
 * @link      		http://www.cogzidel.com
 */

class Login extends CI_Controller {
	function __construct() { 
    	parent::__construct();
		$params = array('appId'  => $this->config->item('appId'),'secret' => $this->config->item('secret'));
		$this->load->library('twconnect');
		$this->load->library('facebook',$params);
		$this->load->library('Instagram_api');
		$this->load->library('wordpress_api');
		$this->load->library('mongoconnect');
		
		$this->load->model('user_model');
			}

	public function index() {

	if (CheckLogged()) {
		
				redirect("home/AccountSettings");
				
	} else {
		
				$this->session->sess_destroy();
		
			    $data['url'] = site_url('login/redirect');
			    $data['page_title'] = 'Login';	
				$data['template'] = 'login';
				$data['mes'] ='';
				$this->load->view('template', $data);
	}

   }

   public function redirect() {
		
		$Ok = $this->twconnect->twredirect('login/callback');
		
		if (!$Ok) {
			echo 'Could not connect to Twitter. Refresh the page or try again later.';
            echo '<script> location.reload(); </script>';
		}
	}

	public function callback() {
		
		$ok = $this->twconnect->twprocess_callback();
		
		if ( $ok ) { redirect('login/TwitterSuccess'); }
			else redirect ('login/TwitterFailure');
	}

	public function TwitterFailure() {

		echo '<p>Twitter connect failed</p>';
		echo '<p><a href="' . base_url() . 'login/clearsession">Try again!</a></p>';
	}

   	public function clearsession() {

		$this->session->sess_destroy();

		redirect('/login');
	}
  
  
	public function TwitterSuccess() {
		
	 	$DataBase = $this->mongoconnect->DataBase();
		$this->twconnect->twaccount_verify_credentials();
		
	    $TwitterUserId                  = $this->twconnect->tw_user_id;
		$TwitterUserInformation         = $this->twconnect->tw_user_info;
						
		$AccessTokenDetails   = $this->twconnect->getPermanentToken();
		$AccessToken          = $AccessTokenDetails['oauth_token'];
		$AccessSecret         = $AccessTokenDetails['oauth_token_secret'];
		
		$ArrayTwitter = array(
		"twitter_id"			 	=> $TwitterUserInformation->id,
		"twitter_user_name" 		=> $TwitterUserInformation->screen_name,
		"twitter_display_name"		=> $TwitterUserInformation->name,
		"twitter_access_token"		=> $AccessToken,
		"twitter_secret_id"			=> $AccessSecret,
		"twitter_profile_image"		=> $TwitterUserInformation->profile_image_url,
		"twitter_status"			=> 1
		);		
	
			if($this->session->userdata("TwitterAccount")) {
		
				$iUserId = $this->session->userdata("user_id");
				$this->session->unset_userdata('TwitterAccount');
			
				$SessionArray = array(
				"twitter_id"            => $TwitterUserId,
			 	"twitter_access_token"  => $AccessToken,
			 	"twitter_secret_id"     => $AccessSecret
				 );
				$this->session->set_userdata($SessionArray);
		
					if( CheckUserTwitter($TwitterUserId) == 0) {
						
						 $this->user_model->UpdateSocialId($iUserId,$TwitterUserId,"user","twitter_id");
												
						if(CheckTwitterStatus($TwitterUserId) == 0) {
							
						$this->user_model->InsertSocialDetails($ArrayTwitter,'twitter');
						
						} else {
							
						$this->user_model->UpdateSocialStatus($TwitterUserId,"twitter","twitter_id","twitter_status");
											
						}
						SetFlashMessage('Twitter account activated Successfully');  
						redirect("home/AccountSettings");
					 } else {
					 	SetFlashMessage("This Twitter account is aleady registered in Chikoo");
						redirect("home/AccountSettings");
					 }
			} else {
				if(!CheckLogged())
				{

				$SessionArray = array(
				"twitter_id"            => $TwitterUserId,
			 	"twitter_access_token"  => $AccessToken,
			 	"twitter_secret_id"     => $AccessSecret
				 );
				$this->session->set_userdata($SessionArray);
				
					if( CheckUserTwitter($TwitterUserId) == 0) {
						
										
						$MongoId = new MongoId();
						
						$InsertArray = array(
							"user_id"           	=> $MongoId,
					 		"facebook_id"       	=> "",
					 		"twitter_id"			=> "$TwitterUserId",
					 		"instagram_id"			=> "",
					 		"wordpress_id"			=> "",
							"user_status"       	=> 1,
					 		"created_datetime"  	=> date('Y-m-d H:i:s', time()),
					 		"post_status"    	   	=> "draft",
					 		"last_logged_datetime" 	=> date('Y-m-d H:i:s', time())
						);
						$this->user_model->InsertSocialDetails($InsertArray,'user');
						$iUserId = $MongoId;

						$this->session->set_userdata("user_id",$iUserId);
						
							if(CheckTwitterStatus($TwitterUserId) == 0) {
							SetFlashMessage("This Twitter account registered in Chikoo");
							$this->user_model->InsertSocialDetails($ArrayTwitter,'twitter');
							
							} else {

							$this->user_model->UpdateSocialStatus($TwitterUserId,"twitter","twitter_id","twitter_status");
									
							}
						
					} else {
				
					 $iUserId = GetUserIdByTwitterId($TwitterUserId);
				 	 $this->session->set_userdata("user_id",$iUserId);				 
				
					}
					redirect("home/AccountSettings");
		
				} else {
				SetFlashMessage("You must login to access this page");
				redirect("login");
				}
			}
		}
public function sample() {
	echo "vairam";
	exit;
}
}?>