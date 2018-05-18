const { Pool, Client } = require('pg');
const dbConfig = require('./db-config.json');
var pool = new Pool(dbConfig);

pool.connect(function () {
	console.log("\x1b[32m%s\x1b[0m", "Database connected");
});

function getLastPrice (market_id, callback) {
	pool.query("SELECT price FROM market_trade_histories WHERE market_id="+market_id+" ORDER BY id desc LIMIT 1", function (err,res) {
		if(err) return callback(err,null);

		if(res.rows.length == 0) return callback(null, 0);

		return callback(null,parseFloat(res.rows[0].price));
	})
}

function getChange (market_id, lastPrice, callback) {
	pool.query("SELECT price FROM market_trade_histories WHERE market_id="+market_id+" AND created_at >= '"+getLast24hFormat()+"' LIMIT 1", function (err,res) {
		if(err) return callback(err,null);

		if(res.rows.length == 0) return callback(null, 0);

		var oldPrice = parseFloat(res.rows[0].price);
		var change = ((lastPrice - oldPrice) / oldPrice) * 100;

		return callback(null,parseFloat(change).toFixed(2));
	})
}

function getVolume (market_id, callback) {
	pool.query("SELECT sum(total) as volume FROM market_trade_histories WHERE market_id="+market_id+" AND created_at >= '"+getLast24hFormat()+"' AND created_at <= '"+getNowFormat()+"'", function (err,res) {
		if(err) return callback(err,null);

		if(res.rows.length == 0) return callback(null, 0);

		if(res.rows[0].volume == null) return callback(null, 0);

		return callback(null,parseFloat(res.rows[0].volume));
	})
}

function getHigh (market_id, callback) {
	pool.query("SELECT max(price) as high FROM market_trade_histories WHERE market_id="+market_id+" AND created_at >= '"+getLast24hFormat()+"' AND created_at <= '"+getNowFormat()+"'", function (err,res) {
		if(err) return callback(err,null);

		if(res.rows.length == 0) return callback(null, 0);

		if(res.rows[0].high == null) return callback(null, 0);

		return callback(null,parseFloat(res.rows[0].high));
	})
}

function getLow (market_id, callback) {
	pool.query("SELECT min(price) as low FROM market_trade_histories WHERE market_id="+market_id+" AND created_at >= '"+getLast24hFormat()+"' AND created_at <= '"+getNowFormat()+"'", function (err,res) {
		if(err) return callback(err,null);

		if(res.rows.length == 0) return callback(null, 0);

		if(res.rows[0].low == null) return callback(null, 0);

		return callback(null,parseFloat(res.rows[0].low));
	})
}

function updateData (market_id, lastPrice, change, volume, high, low, callback) {
	pool.query("SELECT * FROM data_markets WHERE market_id=" + market_id, function (err,res) {
		if(err) return console.log(err + "");

		if(res.rows.length == 0) {

			pool.query("INSERT INTO data_markets(market_id,last_price,change,volume,high,low,created_at,updated_at) VALUES("+market_id+", "+lastPrice+", "+change+", "+volume+", "+high+", "+low+", '"+getNowFormat()+"', '"+getNowFormat()+"')", function (err) {

				if(err) return console.log(err + "");

				callback();
			})
		} else {
			pool.query("UPDATE data_markets SET last_price="+lastPrice+", change="+change+", volume="+volume+", high="+high+", low="+low+" WHERE market_id=" + market_id, function (err) {
				if(err) return console.log(err + "");

				callback();
			})
		}
	});
}

function solve(data,index,callback) {
	if(data.length == index) return callback();

	var arr = data[index];

	getLastPrice(arr.id, function (err,lastPrice) {

		if(err) return console.log(err + "");

		getChange(arr.id, lastPrice, function (err,change) {

			if(err) return console.log(err + "");

			getVolume(arr.id, function(err, volume) {

				if(err) return console.log(err + "");

				getHigh(arr.id, function (err, high) {

					if(err) return console.log(err + "");

					getLow(arr.id, function (err, low) {

						if(err) return console.log(err + "");

						console.log("\x1b[32m%s\x1b[0m", "ID : " + arr.id + " lastPrice: " + lastPrice + " change: " + change + " volume: " + volume + " high: " +high+ " low: " + low);

						updateData(arr.id, lastPrice, change, volume, high, low, function () {
							solve(data,index+1,callback);
						});
					});

				});

			});

		});

	});
}

function getAllMarkets () {
	pool.query("SELECT * FROM markets", function (err,res) {

		if(err) return console.log(err + "");

		solve(res.rows,0, function () {
			setTimeout(function () {
				getAllMarkets();
			},1000);
		});

	});
}

getAllMarkets();

function getNowFormat () {
	var now = new Date();
	return now.getFullYear() + "-"+formatNumber(now.getMonth() + 1)+"-"+formatNumber(now.getDate())+" "+formatNumber(now.getHours())+":"+formatNumber(now.getMinutes())+":" + formatNumber(now.getSeconds());
}

function getLast24hFormat () {
	var now = new Date().getTime();
	var last24h = new Date(now - (24 * 60 * 60 * 1000));
	return last24h.getFullYear() + "-"+formatNumber(last24h.getMonth() + 1)+"-"+formatNumber(last24h.getDate())+" "+formatNumber(last24h.getHours())+":"+formatNumber(last24h.getMinutes())+":" + formatNumber(last24h.getSeconds());
}

function formatNumber(number) {
	if(number < 10) return "0" + number;
	return number;
}