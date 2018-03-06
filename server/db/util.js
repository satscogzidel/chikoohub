var settings = require('./../settings');
if(process.env.env === undefined )
{
  console.log("Please set the environment");
}else{
  database_url = settings[process.env.env].database_url;
  database_name = settings[process.env.env].database_name; 
}

const MongoClient = require('mongodb').MongoClient;
const assert = require('assert');
const url = database_url;
const dbName = database_name;



// Use connect method to connect to the server
// MongoClient.connect(url, function(err, client) {
//   assert.equal(null, err);
//   console.log("Connected successfully to server");

//   const db = client.db(dbName);
//   // var dataf=[{
//   // database_url:'mongodb://admin:admin123@ds237967.mlab.com:37967/heroku_k719h00b',
//   // database_name:'heroku_k719h00b',
//   // wp_client_id: "56629",
//   // wp_client_secret: "KE3hoCcMFAelCkLJkrOlINzf5rOgezHa9vjKtqPm33OLOtdtTYD9IKBYCR99qEWm",
//   // wp_redirect_url: "http://sapphirecamera.herokuapp.com/wp_auth",
//   // insta_client_id: '1be3df9051974b54ae3a69a1c77a7424',
//   // insta_secret_id:'22645dc9d65e4b54bad20e4fa8223a03',
//   // insta_redirect_url:'http://sapphirecamera.herokuapp.com/handleauth',
//   // mode: 'production'
//   // }];

//    var datal=[{
//   database_url:'mongodb://localhost:27017',
//   database_name:'bot',
//   wp_client_id: "56659",
//   wp_client_secret: "5W0jMuxBTsAFvU47IDWar2CtWniMfzq1Ilg9pOteEIcsTgE1b4XMTduu46ZLHtg9",
//   wp_redirect_url: "http://localhost:3000/wp_auth",
//   insta_client_id: '4a4f07d4070748cfb34e15ada1756561',
//   insta_secret_id:'59f07611c23d49dfad75b934dff0d951',
//   insta_redirect_url:'http://localhost:3000/handleauth',
//   mode: 'development'
//   }];

//   // insertDocuments(db, function(result) {          
//   // },'settings',datal);
//   client.close();
// });





//insert data
const insertDocuments = function(db, callback, table, data) {
  // Get the documents collection
  const collection = db.collection(table);
  // Insert some documents //data should be array -> [{a:1,b:2}]
  collection.insertMany(data, function(err, result) {
    assert.equal(err, null);
    // console.log("Sucessfully Inserted");
    callback(result);
  });
}

//findall
const findDocuments = function(db, callback, table) {
  // Get the documents collection
  const collection = db.collection(table);
  // Find some documents
  collection.find({}).toArray(function(err, docs) {
    assert.equal(err, null);
    // console.log("Found the following records");
    // console.log(docs)
    callback(docs);
  });
}

//findone
const findDocumentsone = function(db, callback, table, data) {
  // Get the documents collection
  const collection = db.collection(table);
  // Find some documents
  collection.find(data).toArray(function(err, docs) {
    assert.equal(err, null);

    callback(docs);
  });
}

//update
const updateDocument = function(db, callback , table, data, id) {
  // Get the documents collection
  const collection = db.collection(table);
  // Update document where a is 2, set b equal to 1
  collection.updateOne(id
    , { $set: data }, function(err, result) {
    assert.equal(err, null);
    // console.log("Updated the document with the field a equal to 2");
    callback(result);
  });
}

//remove
const removeDocument = function(db, callback) {
  // Get the documents collection
  const collection = db.collection('documents');
  // Delete document where a is 3
  collection.deleteOne({ a : 3 }, function(err, result) {
    assert.equal(err, null);
    assert.equal(1, result.result.n);
    // console.log("Removed the document with the field a equal to 3");
    callback(result);
  });
}

//Index a Collection
const indexCollection = function(db, callback) {
  db.collection('documents').createIndex(
    { "a": 1 },
      null,
      function(err, results) {
        // console.log(results);
        callback();
    }
  );
};


module.exports={
  insertDocuments,
  findDocuments,
  findDocumentsone,
  updateDocument,
  removeDocument,
  indexCollection
};
