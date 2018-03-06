var api = require('instagram-node').instagram();
var express = require('express');
var app = express();
var session = require('express-session');
var unixTime = require('unix-time');

var settings = require('./../settings');
if(process.env.env === undefined )
{
  console.log("Please set the environment");
}
else{
  database_url = settings[process.env.env].database_url;
  database_name = settings[process.env.env].database_name; 
  
  const MongoClient = require('mongodb').MongoClient;//mongo connection
  const assert = require('assert'); 
  const url = database_url; // Connection URL 
  const dbName =  database_name; // Database Name
  const {insertDocuments,findDocumentsone} = require('./../db/util');

  app.use(session({
      secret: '2C44-4D44-WppQ38S',
      resave: true,
      saveUninitialized: true,
      cookie: { secure: true },
      cookie: { maxAge: 60000 }
  }));


  api.use({
    client_id: settings[process.env.env].insta_client_id,
    client_secret: settings[process.env.env].insta_secret_id
  });

  var redirect_uri = settings[process.env.env].insta_redirect_url;

  exports.authorize_user = function(req, res) {
      res.redirect(api.get_authorization_url(redirect_uri, { scope: ['likes'], state: 'a state' }));
  };

exports.handleauth = function(req, res) {
  api.authorize_user(req.query.code, redirect_uri, function(err, result) {
    if (err) {
      res.send("Didn't work");
    } else {
      var check = {user_id : session.wp_id};
      MongoClient.connect(url, function(err, client) {
      assert.equal(null, err);
                                        
      const db = client.db(dbName);
      findDocumentsone(db, function(docs) {
      if(docs == ""){
      var datas= [{
      user_id : session.wp_id,
      insta_user_id:  result.user['id'],
      auth_token : result.access_token,
      media : 0, 
      active : 1,
      last_update : unixTime(new Date())
      }];  

      insertDocuments(db, function() {
      client.close();
      },'insta_auth',datas); 
      }
      client.close();
      },'insta_auth',check);                        
      });  
       res.redirect('/');     
    }
  });
};
}