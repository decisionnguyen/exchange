const { Pool, Client } = require('pg');
const dbConfig = require('./db-config.json');
var pool = new Pool(dbConfig);

pool.connect(function () {
	console.log("\x1b[32m%s\x1b[0m", "Database connected");
});

var marketProccessing = [];

function checkProccessing (market_id, callback) {
	if(marketProccessing.indexOf(data.market_id) == -1) {
		marketProccessing[data.market_id] = false;
		callback();
	} else {
		if(marketProccessing[market_id] == true) {
			callback();
		} else {
			checkProccessing(market_id,callback);
		}
	}
}

function solvePending (data) {
	var market_id = data.market_id;
	checkProccessing(market_id, function () {
		// get data
		pool.query("SELECT value FROM exchange_fees WHERE market_id=" + market_id, function (err,res) {

			if(err) return console.log("ERR GET FEE : " + err + "");

		})
	});
}

function getPending (market_id) {
	pool.query("SELECT * FROM order_pendings", function (err,res) {

		if(err) return console.log("ERR GET PENDING" + err + "");

		if(res.rows.length == 0) return getPending();

		for (var i = 0; i < res.rows.length; i++) {
			solvePending(res.rows[i]);
		}

	});
}