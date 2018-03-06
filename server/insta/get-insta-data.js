const api = require('instagram-node').instagram();
var request = require('request');
const unixTime = require('unix-time');

var settings = require('./../settings');
if(process.env.env === undefined )
{
  console.log("Please set the environment");
}else{
  database_url = settings[process.env.env].database_url;
  database_name = settings[process.env.env].database_name; 
}


const MongoClient = require('mongodb').MongoClient;//mongo connection
const Mongo = require('mongodb');
const assert = require('assert'); 
const url = database_url; // Connection URL 
const dbName = database_name; // Database Name
const {insertDocuments, findDocuments, findDocumentsone, updateDocument, removeDocument,	indexCollection} = require('./../db/util');


var insta_image_upload = ()=> { 
 MongoClient.connect(url, function(err, client) {
    assert.equal(null, err);
    // console.log("Connected successfully to server");
   
        	const db = client.db(dbName);
            var data ={ active : 1 };
    		findDocumentsone(db, function(docs) {
    		findDocumentsone(db, function(users) {
    		api.use({
		  	client_id:  settings[process.env.env].insta_client_id,
		  	client_secret:  settings[process.env.env].insta_secret_id,
		  	access_token: docs[0].auth_token
			});
    	    api.user(docs[0].insta_user_id, function(err, result, remaining, limit) {    	    
	    	var updatedata = { media : 0};
	    	api.user_media_recent(docs[0].insta_user_id,function(err, medias, pagination, remaining, limit) {
				  	
	      	if(err){
				console.log(err);
			}else{
				  	
			  	for(var i=docs[0].media ;i < result.counts.media;i++)
			{
				  		
			if(medias[i].type == 'image' && docs[0].last_update <  medias[i].created_time){
				  	
			var wpcom = require('wpcom')(users[0].access_token);
				  			   
			var uri="/sites/"+ users[0].wp_id +"/media/new";
 			var media_urls=[medias[i].images.standard_resolution.url];
			wpcom.req.post(uri,{media_urls}, function(err, data){
			if(err){
			console.log(err);
			}else{
			console.log(data);
			datas ={last_update: unixTime(new Date())};
			updateDocument(db, function(result) {
			// client.close();
			},'insta_auth',datas,{'user_id' : new Mongo.ObjectID(docs[0].user_id)});
			}
			})
				  			
			}
			}
			 
		    }

		});
	    	
    	});
    	
       },'users',{'_id' : new Mongo.ObjectID(docs[0].user_id)});  
     },'insta_auth',data);
 }); 
};



module.exports ={
	insta_image_upload
};
 

