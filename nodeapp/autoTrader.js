// socket settings
var socket = require('socket.io-client')('https://socket.geniota.com:8000',{secure: true, rejectUnauthorized: false});
const PRIVATE_CHANNEL = 'trade';
const SOCKET_PASSWORD = '900150983cd24fb0d6963f7d28e17f72';
// database settings
const { Pool, Client } = require('pg');
const dbConfig = require('./db-config.json');
var pool = new Pool(dbConfig);
var fs = require('fs');

pool.connect(function () {
    console.log("\x1b[32m%s\x1b[0m", "Database connected");
});

var WebSocketClient = require('websocket').client;

var client = new WebSocketClient();
var canResume = true;
var market_id = 1;

// ETH BTC
var eth = new autoTrader({
	wss_name: "ethbtc",
	market_id: 1,
	user_id: 9,
	market_name: "ETH/BTC",
	coin_id_first: 5,
	coin_id_second: 4,
	coin_symbol_first : "ETH",
	coin_symbol_second: "BTC"
});
// EOS BTC
var eos = new autoTrader({
	wss_name: "eosbtc",
	market_id: 4,
	user_id: 9,
	market_name: "EOS/BTC",
	coin_id_first: 6,
	coin_id_second: 4,
	coin_symbol_first : "EOS",
	coin_symbol_second: "BTC"
});
// TRX BTC
var trx = new autoTrader({
	wss_name: "trxbtc",
	market_id: 5,
	user_id: 9,
	market_name: "TRX/BTC",
	coin_id_first: 7,
	coin_id_second: 4,
	coin_symbol_first : "TRX",
	coin_symbol_second: "BTC"
});

// OMG BTC
var omg = new autoTrader({
	wss_name: "omgbtc",
	market_id: 7,
	user_id: 9,
	market_name: "OMG/BTC",
	coin_id_first: 9,
	coin_id_second: 4,
	coin_symbol_first : "OMG",
	coin_symbol_second: "BTC"
});

// VEN BTC
var ven = new autoTrader({
	wss_name: "venbtc",
	market_id: 11,
	user_id: 9,
	market_name: "VEN/BTC",
	coin_id_first: 13,
	coin_id_second: 4,
	coin_symbol_first : "VEN",
	coin_symbol_second: "BTC"
});

// ICX BTC
var icx = new autoTrader({
	wss_name: "icxbtc",
	market_id: 12,
	user_id: 9,
	market_name: "ICX/BTC",
	coin_id_first: 14,
	coin_id_second: 4,
	coin_symbol_first : "ICX",
	coin_symbol_second: "BTC"
});

// PPT BTC
var ppt = new autoTrader({
	wss_name: "pptbtc",
	market_id: 13,
	user_id: 9,
	market_name: "PPT/BTC",
	coin_id_first: 15,
	coin_id_second: 4,
	coin_symbol_first : "PPT",
	coin_symbol_second: "BTC"
});

// ZIL BTC
var zil = new autoTrader({
	wss_name: "zilbtc",
	market_id: 15,
	user_id: 9,
	market_name: "ZIL/BTC",
	coin_id_first: 17,
	coin_id_second: 4,
	coin_symbol_first : "ZIL",
	coin_symbol_second: "BTC"
});

// DGD BTC
var dgd = new autoTrader({
	wss_name: "dgdbtc",
	market_id: 16,
	user_id: 9,
	market_name: "DGD/BTC",
	coin_id_first: 18,
	coin_id_second: 4,
	coin_symbol_first : "DGD",
	coin_symbol_second: "BTC"
});

// ZRX BTC
var zrx = new autoTrader({
	wss_name: "zrxbtc",
	market_id: 17,
	user_id: 9,
	market_name: "ZRX/BTC",
	coin_id_first: 19,
	coin_id_second: 4,
	coin_symbol_first : "ZRX",
	coin_symbol_second: "BTC"
});

// SNT BTC
var snt = new autoTrader({
	wss_name: "sntbtc",
	market_id: 18,
	user_id: 9,
	market_name: "SNT/BTC",
	coin_id_first: 20,
	coin_id_second: 4,
	coin_symbol_first : "SNT",
	coin_symbol_second: "BTC"
});

// BAT BTC
var bat = new autoTrader({
	wss_name: "batbtc",
	market_id: 19,
	user_id: 9,
	market_name: "BAT/BTC",
	coin_id_first: 21,
	coin_id_second: 4,
	coin_symbol_first : "BAT",
	coin_symbol_second: "BTC"
});

// EOS ETH
var eos = new autoTrader({
	wss_name: "eoseth",
	market_id: 20,
	user_id: 9,
	market_name: "EOS/ETH",
	coin_id_first: 6,
	coin_id_second: 5,
	coin_symbol_first : "EOS",
	coin_symbol_second: "ETH"
});

// TRX ETH
var trx = new autoTrader({
	wss_name: "trxeth",
	market_id: 20,
	user_id: 9,
	market_name: "TRX/ETH",
	coin_id_first: 7,
	coin_id_second: 5,
	coin_symbol_first : "TRX",
	coin_symbol_second: "ETH"
});

// OMG ETH
var omg = new autoTrader({
	wss_name: "omgeth",
	market_id: 22,
	user_id: 9,
	market_name: "OMG/ETH",
	coin_id_first: 9,
	coin_id_second: 5,
	coin_symbol_first : "OMG",
	coin_symbol_second: "ETH"
});

// VEN ETH
var ven = new autoTrader({
	wss_name: "veneth",
	market_id: 23,
	user_id: 9,
	market_name: "VEN/ETH",
	coin_id_first: 13,
	coin_id_second: 5,
	coin_symbol_first : "VEN",
	coin_symbol_second: "ETH"
});

// ICX ETH
var icx = new autoTrader({
	wss_name: "icxeth",
	market_id: 24,
	user_id: 9,
	market_name: "ICX/ETH",
	coin_id_first: 14,
	coin_id_second: 5,
	coin_symbol_first : "ICX",
	coin_symbol_second: "ETH"
});

// PPT ETH
var ppt = new autoTrader({
	wss_name: "ppteth",
	market_id: 25,
	user_id: 9,
	market_name: "PPT/ETH",
	coin_id_first: 15,
	coin_id_second: 5,
	coin_symbol_first : "PPT",
	coin_symbol_second: "ETH"
});

// ZIL ETH
var zil = new autoTrader({
	wss_name: "zileth",
	market_id: 27,
	user_id: 9,
	market_name: "ZIL/ETH",
	coin_id_first: 17,
	coin_id_second: 5,
	coin_symbol_first : "ZIL",
	coin_symbol_second: "ETH"
});

// DGD ETH
var dgd = new autoTrader({
	wss_name: "dgdeth",
	market_id: 28,
	user_id: 9,
	market_name: "DGD/ETH",
	coin_id_first: 18,
	coin_id_second: 5,
	coin_symbol_first : "DGD",
	coin_symbol_second: "ETH"
});

// ZRX ETH
var zrx = new autoTrader({
	wss_name: "zrxeth",
	market_id: 29,
	user_id: 9,
	market_name: "ZRX/ETH",
	coin_id_first: 19,
	coin_id_second: 5,
	coin_symbol_first : "ZRX",
	coin_symbol_second: "ETH"
});

// SNT ETH
var snt = new autoTrader({
	wss_name: "snteth",
	market_id: 30,
	user_id: 9,
	market_name: "SNT/ETH",
	coin_id_first: 20,
	coin_id_second: 5,
	coin_symbol_first : "SNT",
	coin_symbol_second: "ETH"
});

// BAT ETH
var bat = new autoTrader({
	wss_name: "bateth",
	market_id: 31,
	user_id: 9,
	market_name: "BAT/ETH",
	coin_id_first: 21,
	coin_id_second: 5,
	coin_symbol_first : "BAT",
	coin_symbol_second: "ETH"
});

function autoTrader (option) {

	var _this = this;
	this.option = option;
	this.client = new WebSocketClient();
	this.timeout = 500;
	this.canResume = true;

	_this.client.connect("wss://stream.binance.com:9443/ws/"+_this.option.wss_name+"@trade");

	this.client.on('connect', function(connection) {

	    console.log('WebSocket for '+_this.option.market_name+' Client Connected');

	    connection.on('error', function(error) {
	        console.log("Connection Error: " + error.toString());
	    });

	    connection.on('close', function() {
	        console.log('echo-protocol Connection Closed');
	        setTimeout(function () {
	        	console.log("\x1b[32m%s\x1b[0m", "Reconnect");
	        	_this.client.connect("wss://stream.binance.com:9443/ws/"+_this.option.wss_name+"@trade");
	        },5000);
	    });

	    connection.on('message', function(message) {
	    	var data = JSON.parse(message.utf8Data);
	    	var price = data.p;
	    	var amount = data.q;

	    	var market_status = fs.readFileSync('./market_status/' + _this.option.market_id + ".txt");

	    	if(canResume == true && market_status == 'true') {

	    		canResume = false;

	    		setTimeout(function () {

	    			canResume = true;

	    		}, _this.timeout);

	    		_this.emmitOrder(price,amount);
	    	}


	    });
	   
	});

	this.makeHash = function (length = 32) {
	  	var text = "";
	  	var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

	  	for (var i = 0; i < length; i++)
	    	text += possible.charAt(Math.floor(Math.random() * possible.length));

	  	return text;
	}

	this.randomType = function () {
		var random_boolean = Math.random() >= 0.5;
		if(random_boolean) return 'sell';
		return 'buy';
	}

	this.emmitOrder = function (price,amount) {
		var orderType = _this.randomType();
		var hash = _this.makeHash();

		var data = {
			hash : hash,
			type : 'limit',
			market_id : _this.option.market_id,
			user_id : _this.option.user_id,
			price: price,
			amount: amount,
			fee: 0.25,
			market : {
				id: _this.option.market_id,
				coin_id_first: _this.option.coin_id_first,
				coin_id_second: _this.option.coin_id_second,
				name: _this.option.market_name
			},
			coinFirst: {
				symbol : _this.option.coin_symbol_first
			},
			coinSecond : {
				symbol: _this.option.coin_symbol_second
			}
		}

		console.log("\x1b[32m%s\x1b[0m", "NEW "+orderType.toUpperCase()+" ORDER "+_this.option.market_name+" with price=" + price + " amount=" + amount + " ");

		socket.emit('new-'+orderType+'-order', {channel: "trade",password: SOCKET_PASSWORD, message:JSON.stringify(data)});
	}
}