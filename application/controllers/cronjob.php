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
 * Chikoo cronjob file
 *
 * @file  			Cron job
 * @author     		Cogzidel Developers
 * @file created   	Feb 13, 2014
 * @link      		http://www.cogzidel.com
 */

class Cronjob extends CI_Controller {

	/**
	* Constructor function
	*
	* @Load facebook library class params : appId,secret
	**/

	public function __construct() {
    	parent::__construct();
		$params = array('appId' => $this->config->item('appId'), 'secret' => $this->config->item('secret'));
		
		$this->load->library('facebook',$params);
		$this->load->library('twconnect');
		$this->load->library('Instagram_api');
		$this->load->library('wordpress_api');
		$this->load->library('mongoconnect');
		
		$this->load->model('user_model');
	}
	
	public function SocialMediaUpdateToWordpress() {
		
		$GetUser = $this->user_model->GetUserIdBasedWordpress();
		
		foreach($GetUser as $User) {
			
			$iUserId = $User['user_id'];
			
														
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
	
		echo "<h1> Social media update cronjob Running successfully...</h1>";

		$sToday = date("Y-m-d H:i:s");
		$mail_ranking_content = "Cronjob run successfully for SocialMedia at ".$sToday;
		$content = $mail_ranking_content;
		$email = "vairamuthu@cogzidel.com";
		$name = "";
		$subject = 'Chikoo update';
		$this->sendEmailToUsers($email,$name,$content,$subject);  
		
	}

	public function sendEmailToUsers($email,$name,$content,$subject) {
		$config = Array(
        	'mailtype' => 'html',
        );
		$this->load->library('email',$config);
		$this->email->set_newline('\r\n');
		$this->email->from("vairamuthu@cogzidel.com", 'Chikoo');
		$this->email->to($email);
		
		$this->email->subject($subject);
		$str = '<p>Hi '.$name.',</p>';
		$end_msg = "<p>Regards </p><p> The Chikoo Team <p> --- </p>";
		$message = $str.$content.$end_msg;
		$this->email->message($message);
		return $this->email->send();
	}
}
/* End of the file Cronjob */
?>