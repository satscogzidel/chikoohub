jQuery(".on-off-insta").on('click',function(){
  window.location.href='/authorize_user';
});

jQuery(".wordpress-on-off").on('click',function(){
  // window.location.href='/authorize_user';
  window.location.href=jQuery('.wp_request').attr('href');
});


function loadJSON(callback) {   

 var xhr = new XMLHttpRequest();
 var uri='http://'+window.location.host+'/settings';
 xhr.open("GET", uri, true);
 xhr.onreadystatechange = function() {
  if (xhr.readyState == 4) {
    // WARNING! Might be evaluating an evil script!
   callback(xhr.responseText);
   
  }
}
xhr.send();
 }




  loadJSON(function(response) {
  // Parse JSON string into object
    var actual_JSON = JSON.parse(response);
    // console.log(actual_JSON);
    if(window.location.hostname == "localhost"){
    	var client_id = JSON.parse(response).development.wp_client_id;
    	var redirect_uri = JSON.parse(response).development.wp_redirect_url;
    }else{
     	var client_id = JSON.parse(response).production.wp_client_id;
    	var redirect_uri = JSON.parse(response).production.wp_redirect_url;
    }
  // console.log(response);
    var url='https://public-api.wordpress.com/oauth2/authorize?client_id='+client_id+'&redirect_uri=http://'+window.location.host+'/wp_auth&response_type=code&scope=global';
    jQuery('.wp_request').attr('href',url);
 });


  function user_status(callback) {   

 var xhr = new XMLHttpRequest();
 var uri='http://'+window.location.host+'/user_status';
 xhr.open("GET", uri, true);
 xhr.onreadystatechange = function() {
  if (xhr.readyState == 4) {
    // WARNING! Might be evaluating an evil script!
   callback(xhr.responseText);
   
  }
}
xhr.send();
 }




  user_status(function(response) {
  // Parse JSON string into object
    var actual_JSON = JSON.parse(response);
    console.log(actual_JSON.status);
    if(actual_JSON.status === 1){
      jQuery('.link-symbol').show();
      jQuery('.unlink-symbol').hide();
    }else{
      jQuery('.link-symbol').hide();
      jQuery('.unlink-symbol').show();
    }
   
 });