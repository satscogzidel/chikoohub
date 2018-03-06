var http = require('http');
var express = require('express');
var path = require('path');
var request = require('request');
var bodyParser = require('body-parser');
var unixTime = require('unix-time');
var session = require('express-session');
var cron = require('node-cron');
var fs = require('fs');
var app = express();


//set session 
app.use(session({
    secret: '2C44-4D44-WppQ38S',
    resave: true,
    saveUninitialized: true,
    cookie: { secure: true },
    cookie: { maxAge: 60000 }
}));

var settings = require('./settings');
if(process.env.env === undefined )
{
  console.log("Please set the environment");
}
else{

database_url = settings[process.env.env].database_url;
database_name = settings[process.env.env].database_name;
const MongoClient = require('mongodb').MongoClient;//mongo connection
const Mongo = require('mongodb');
const assert = require('assert'); 
const mdurl = database_url; // Connection URL 
const dbName =  database_name; // Database Name
const {insertDocuments,findDocumentsone} = require('./db/util');


// console.log(process.env);
var insta_auth = require('./insta/auth');
var {insta_image_upload} = require('./insta/get-insta-data');
const port = process.env.PORT || 3000;
const publicPath = path.join(__dirname, '../public');

app.use(express.static(publicPath,{urls:settings[process.env.env].wp_redirect_url}));

app.get('/wp_auth/',(req,res)=>{
   
  task.stop();
  var code = req.param('code');
  var data = {
    "client_id": settings[process.env.env].wp_client_id,
    "client_secret": settings[process.env.env].wp_client_secret,
    "redirect_uri": settings[process.env.env].wp_redirect_url,
    "code": code,
    "grant_type": "authorization_code"
  };


  var url = "https://public-api.wordpress.com/oauth2/token";
  request.post({url : url ,json :true, formData : data}, (err, res, body) => {

    if (err) {
      return  console.log(err);
    }else{
     
    var wpcom = require('wpcom')(body.access_token);
    wpcom.req.get('/me/sites', function(err, data){
        // data response
        // console.log();
    datas=[{
    user_name : data.sites[0].name,
    site_url : data.sites[0].URL,
    wp_id : data.sites[0].ID,
    access_token : body.access_token,
    created_at : unixTime(new Date())
    }];
    check={wp_id : data.sites[0].ID};     

    MongoClient.connect(mdurl, function(err, client) {
    assert.equal(null, err);
    console.log("Connected successfully to server");           
    const db = client.db(dbName);
          
    findDocumentsone(db, function(docs) {
    if(docs == ""){
    insertDocuments(db, function(result) {
    findDocumentsone(db, function(docss){session.wp_id = docss[0]._id;},'users',check);
    client.close();
    },'users',datas);
    }else
    {
    session.wp_id = docs[0]._id;
    }
    },'users',check);
    });
    }); 
  }
  });
  res.redirect('/'); 
});



var task = cron.schedule(`*/${settings[process.env.env].cron_duration} * * * *`, function() {
   // console.log('will execute every minute until stopped');
  insta_image_upload();
});
 


app.get('/authorize_user', insta_auth.authorize_user,(req,res)=>{});
app.get('/handleauth', insta_auth.handleauth,(req,res)=>{});

app.get('/settings', function (req, res) {
  let rawdata = fs.readFileSync('server/settings.json');  
  // let student = JSON.parse(rawdata);
  res.send(rawdata);  
});

app.get('/user_status', function (req, res) {
  if(session.wp_id){
  MongoClient.connect(mdurl, function(err, client) {
  assert.equal(null, err);        
  const db = client.db(dbName);
  findDocumentsone(db, function(docs) {
     console.log(docs);
     if(docs =="")
     {
      let rawdata = {'status':0}; res.send(rawdata);  
     }else{
      let rawdata = {'status':1}; res.send(rawdata);
     }
  },'insta_auth',{'user_id' : new  Mongo.ObjectID(session.wp_id) });
  });}else{
    let rawdata = {'status':0}; res.send(rawdata);
  }
});

app.get('/logout', function (req, res) {
  req.session.destroy();
  res.send("logout success!");
});

http.createServer(app).listen(port, function(){
  console.log(`Express server listening on port ${port}` );
});

}



