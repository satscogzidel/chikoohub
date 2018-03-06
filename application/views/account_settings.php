<html>
	<head>
		
		
	</head>
	
	<body>
		
	<?php
	
	$iUserId    =  $this->session->userdata("user_id");
	$Instagram  =  CheckInstagram($iUserId);
	$FaceBook   =  CheckFacebook($iUserId);
	$Twitter    =  CheckTwitter($iUserId);
	$WordPress  =  CheckWordPress($iUserId);
	$PostStatus =  CheckPostStatus($iUserId);

	?>
	
       <div id="fb-root"></div>
       
       <?php if($WordPress > 0) {?>
       	
       	<a href="<?php echo $this->wordpress_api->wordpressLogin(); ?>"><div>ON Wordpress</div></a>
       	
       	 <?php $this->session->set_userdata('WordpressAccount',TRUE); ?>
       	
       	<br/>
       	
       	
       <?php } else { ?>
       		
       	 <a href="<?php echo site_url('home/WordPressOff'); ?>"> <div>OFF Wordpress</div></a> 
       	 
       	 <br/>
       		
       <?php } ?>
       
       
       
	  <?php if($FaceBook > 0) {?>
	  	
        <a href="#" onclick="fblogin();return false;"> <div>ON Facebook</div></a>  
                 
       <?php $this->session->set_userdata('FacebookAccount',TRUE); ?>
       
		<br/>

      <?php } else { ?>
	
	         <a href="<?php echo site_url('home/FaceBookOff'); ?>"> <div>OFF Facebook</div></a> 
	         
	         <br/> 
	
      <?php } ?>	
      

	 <?php if($Twitter > 0) {?>		

			     <a href="<?php echo site_url('login/redirect'); ?>"> <div>ON Twitter</div></a>
	  <?php $this->session->set_userdata('TwitterAccount',TRUE); ?>

<br/>

<?php } else { ?>
	
				     <a href="<?php echo site_url('home/TwitterOff'); ?>"> <div>OFF Twitter</div></a>
				     
				 <br/>
				     
<?php }  if($Instagram > 0) {?>	
					     
                  <a href="<?php echo $this->instagram_api->instagramLogin(); ?>"> <div>ON Instagram</div></a>
                  
      <?php $this->session->set_userdata('InstagramAccount',TRUE); ?>
                  
<br/> 

<?php } else { ?>

                  <a href="<?php echo site_url('home/InstagramOff') ?>"> <div>OFF Instagram</div></a>
                  
<br/> 

<?php }  if($PostStatus == 1 ) { ?>
	
	 <a href="<?php echo site_url('home/ChangePostStatus?Status=publish') ?>"> <div>Publish</div></a>
	
<?php } else { ?>
	
	 <a href="<?php echo site_url('home/ChangePostStatus?Status=draft') ?>"> <div>Draft</div></a>
	
<?php } ?>

<br/> 

	 <a href="<?php echo site_url('home/logout') ?>"> <div>Logout</div></a>

	</body>
	
<script>
         
          window.fbAsyncInit = function() {
            FB.init({appId: '<?php echo $this->facebook->getAppId(); ?>', status: true, cookie: true,
                     xfbml: true});
          };
          (function() {
            var e = document.createElement('script'); e.async = true;
            e.src = document.location.protocol +
              '//connect.facebook.net/en_US/all.js';
            document.getElementById('fb-root').appendChild(e);
          }());
 </script>

<script>
          //your fb login function
          function fblogin() {
            FB.login(function(response) {
            	if(response.authResponse)
            	{
            		 window.location.href = '<?php echo base_url()."home/facebook_connect"; ?>';
            	}
            else
            {
            	
            }
        
              //...
            }, {scope:'email,offline_access,user_birthday,status_update,publish_stream'});
          }
</script>
</html>