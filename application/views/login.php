
						  
<div id="fb-root"></div>
	

<a href="#" onclick="fblogin();return false;"> <div>Connection With Facebook</div></a>  
                        
<br/>			

<a href="<?php echo $url; ?>"> <div>Connection With Twitter</div></a>

<br/>

<a href="<?php echo $this->instagram_api->instagramLogin(); ?>"> <div>Connection With Instagram</div></a>

<br/>

<a href="<?php echo $this->wordpress_api->wordpressLogin(); ?>"><img src="//s0.wp.com/i/wpcc-button.png" width="231" /></a>

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

        <!-- custom login button -->
        


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
            }, {scope:'email,offline_access,user_birthday,status_update,publish_stream,read_stream'});
          }
        </script>
