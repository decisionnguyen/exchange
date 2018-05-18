// database settings
const { Pool, Client } = require('pg');
const dbConfig = require('./db-config.json');
var pool = new Pool(dbConfig);
var fs = require('fs');

pool.connect(function () {
	console.log("\x1b[32m%s\x1b[0m", "Database connected");
});

// socket settings
var socket = require('socket.io-client')('https://socket.geniota.com:8000',{secure: true, rejectUnauthorized: false});
const PRIVATE_CHANNEL = 'trade';
const SOCKET_PASSWORD = '900150983cd24fb0d6963f7d28e17f72';

var hash = "";
var isProccessing = [];

socket.on('connect', function () {
	
	console.log("\x1b[32m%s\x1b[0m", "Connected to Socket server");

	socket.emit('subscribe-to-channel', {channel: PRIVATE_CHANNEL})

	socket.on('new-buy-order', function(data){
		
		var data = JSON.parse(data.message);

		if(data.hash == hash) return;
		hash = data.hash;

		console.log("NEW "+data.type.toUpperCase()+" BUY ORDER : " + hash);

		// check market status
		var market_id = data.market_id;

		checkMarketStatus(market_id, function () {
			checkProccessing(data.user_id, function  () {
				var value = data.price * data.amount;
				var total = value + (value * data.fee / 100);
				checkWallet(data.user_id,data.market.coin_id_second,total, function (err,avaiable) {
					if(err) {
						console.log(err + "");
						fs.writeFileSync('./market_status/' + market_id + '.txt', 'true');
						isProccessing[data.user_id] = false;
						return;
					}

					if(data.type == 'limit' || data.type == 'market') {
						getOrderAvaiable(data);
					}

					if(data.type == 'stop-limit') {
						setOrderStopLimit(data);
					}
					
				})
			})
		})
	});	
})

function checkMarketStatus (market_id, callback) {
	var path = "./market_status/" + market_id + ".txt";

	if (fs.existsSync(path)) {
	    var market_status = fs.readFileSync(path);

	    // console.log("Market status: " + market_status);

	    if(market_status == 'true') {
	    	fs.writeFileSync(path,'false');
	    	return callback();
	    } else {
	    	
	    	setTimeout(function () {
	    		checkMarketStatus(market_id,callback);
	    	},500);
    		
	    }

	} else {
		fs.writeFileSync(path, 'true');
		return callback();
	}

}

function checkWallet(user_id,coin_id, amount, callback) {
	pool.query("SELECT * FROM wallets WHERE user_id="+user_id+" AND coin_id=" + coin_id, function (err, res) {

		if(err) return callback(err,null);
		if(res.rows.length == 0) return callback(new Error("No wallet"), null);

		var rows = res.rows[0];

		if(parseFloat(rows.amount) < parseFloat(amount)) return callback(new Error("Not enough balance"), null);

		return callback(null,true);
	})
}

function checkProccessing(user_id, callback) {
	if(isProccessing.indexOf(user_id) != -1) {
		if(isProccessing[user_id] == true) {
			checkProccessing(user_id,callback);
		} else {
			isProccessing[user_id] = true;
			return callback();
		}
	} else {
		isProccessing[user_id] = true;
		return callback();
	}
}

function subtractWallet(user_id,coin_id,amount,symbol,callback) {
	pool.query("SELECT * FROM wallets WHERE user_id="+user_id+" AND coin_id=" + coin_id, function (err,res) {
		if(err) return console.log(err + "");
		var currentAmount = parseFloat(res.rows[0].amount);
		var subtractAmount = parseFloat(amount);

		if(currentAmount < subtractAmount) {
			return callback(new Error('Insufficient balance'),currentAmount);
		}

		var updateAmount = currentAmount - subtractAmount;
		pool.query("UPDATE wallets SET amount="+updateAmount+", updated_at='"+getNowFormat()+"' WHERE id=" + res.rows[0].id, function () {
			console.log("\x1b[32m%s\x1b[0m", "BALANCE "+currentAmount+" "+symbol+" SUBTRACT WALLET "+subtractAmount+" "+symbol+" and AFTER BALANCE IS "+updateAmount+" "+symbol);
			var message = {};
			message[symbol] = updateAmount;
			socket.emit('change-balance', {channel: 'private-' + user_id, password: SOCKET_PASSWORD, message: message});
			return callback(null,updateAmount);
		});

		
	});
}

function plusWallet(user_id,coin_id,amount,symbol, callback) {

	pool.query("SELECT * FROM wallets WHERE user_id="+user_id+" AND coin_id=" + coin_id, function(err, res) {

		if(err) return callback(err,null);

		if(res.rows.length == 0) {
			pool.query("INSERT INTO wallets(user_id,coin_id,amount,new_address_pending,created_at,updated_at) VALUES("+user_id+","+coin_id+","+amount+",false, '"+getNowFormat()+"','"+getNowFormat()+"')");
			// emit change balance in private
			var message = {};
			message[symbol] = parseFloat(amount).toFixed(8);

			socket.emit('change-balance', {channel: 'private-' + user_id, password: SOCKET_PASSWORD, message: message});
			return callback(null,true);
		}

		var currentAmount = parseFloat(res.rows[0].amount);
		var plusAmount = parseFloat(amount);
		var amountReceive = currentAmount + plusAmount;

		pool.query("UPDATE wallets SET amount="+amountReceive+",updated_at='"+getNowFormat()+"' WHERE id=" + res.rows[0].id, function () {
			console.log("\x1b[32m%s\x1b[0m", "BALANCE "+currentAmount+" "+symbol+" PLUS WALLET "+plusAmount+" "+symbol+" and AFTER BALANCE IS "+amountReceive+" "+symbol);
			// emit change balance in private
			var message = {};
			message[symbol] = parseFloat(amountReceive).toFixed(8);

			socket.emit('change-balance', {channel: 'private-' + user_id, password: SOCKET_PASSWORD, message: message});
			return callback(null, true);
		});

		
	});
}

function deleteOrder (id, market_id, user_id, callback) {
	pool.query("DELETE FROM orders WHERE id=" + id, function (err) {
		if(err) console.log("DELETE ORDER ERROR " + err + "");
		// emit all my order in private
		pool.query("SELECT * FROM orders WHERE market_id="+market_id+" AND user_id="+user_id+" ORDER BY id desc LIMIT 100", function(err, res) {
			socket.emit('change-my-order', {channel: 'private-' + user_id, password: SOCKET_PASSWORD, message: {data: res.rows, market_id: market_id}});
			callback();
		});
	});
}

function updateOrder (id,amount,value,total,market_id, user_id, callback) {
	pool.query("UPDATE orders SET amount="+amount+",value="+value+",total="+total+" WHERE id=" + id, function (err) {
		if(err) console.log("UPDATE ORDER ERROR :" + err + "");
		// emit all my order in private
		pool.query("SELECT * FROM orders WHERE market_id="+market_id+" AND user_id="+user_id+" ORDER BY id desc LIMIT 100", function(err, res) {
			socket.emit('change-my-order', {channel: 'private-' + user_id, password: SOCKET_PASSWORD, message: {data: res.rows, market_id: market_id}});
			callback();
		});
	});
}

function emitTradeData(market_id,buyData,sellData) {

	if(buyData == true) {
		// emit buy order data in public room
		pool.query("SELECT price,sum(amount) as amount, sum(value) as value FROM orders WHERE market_id="+market_id+" AND type='buy' GROUP BY price ORDER BY price desc LIMIT 100", function (err,res) {
			socket.emit('change-buy-order', {channel: 'trade-' + market_id, password: SOCKET_PASSWORD, message: res.rows});
		})

		// emit total buy order in public room
		pool.query("SELECT sum(value) as sum_buy_order FROM orders WHERE type='buy' AND market_id=" + market_id, function (err,res) {
			if(res.rows[0].sum_buy_order == null) res.rows[0].sum_buy_order = 0;
			socket.emit('change-sum-buy-order', {channel: 'trade-' + market_id, password: SOCKET_PASSWORD, message: res.rows[0].sum_buy_order});
		})
	}
	
	if(sellData == true) {
		// emit sell order data in public room
		pool.query("SELECT price,sum(amount) as amount, sum(value) as value FROM orders WHERE market_id="+market_id+" AND type='sell' GROUP BY price ORDER BY price LIMIT 100", function (err,res) {
			socket.emit('change-sell-order', {channel: 'trade-' + market_id, password: SOCKET_PASSWORD, message: res.rows});
		})

		// emit total sell order in public room
		pool.query("SELECT sum(amount) as sum_sell_order FROM orders WHERE type='sell' AND market_id=" + market_id, function (err,res) {
			if(res.rows[0].sum_sell_order == null) res.rows[0].sum_sell_order = 0;
			socket.emit('change-sum-sell-order', {channel: 'trade-' + market_id, password: SOCKET_PASSWORD, message: res.rows[0].sum_sell_order});
		})
	}
	
}

function insertMarketTradeHistory (market_id,price,amount,value,fee,total,market_symbol) {
	pool.query("INSERT INTO market_trade_histories(market_id,type,price,amount,value,fee,total,created_at,updated_at) VALUES ("+market_id+",'buy',"+price+","+amount+","+value+","+fee+","+total+", '"+getNowFormat()+"', '"+getNowFormat()+"')", function () {
		
		var data = {
			"market_id" : market_id,
			"type" : 'buy',
			"price" : price,
			"amount" : amount,
			"value" : value,
			"fee" : fee,
			"total" : total,
			"market_symbol": market_symbol
		};

		socket.emit('new-market-trade-history', {channel: 'trade-' + market_id, password: SOCKET_PASSWORD, message: data});
		socket.emit('new-all-market-trade-history', {channel: 'market', password: SOCKET_PASSWORD, message: data});

		// check stop-limit
		pool.query("SELECT * FROM order_stop_limit WHERE stop=" + price, function (err,res) {
			if(err) return console.log(err + "");

			if(res && res.rows && res.rows.length > 0) {
				solveStopLimit(res.rows,0);
			}
		});
	});
}

function solveStopLimit (data,index) {

	if(data.length == index) return;

	var arr = data[index];

	pool.query("INSERT INTO orders(market_id,user_id,type,price,amount,value,fee,total,created_at,updated_at) VALUES("+arr.market_id+","+arr.user_id+",'buy',"+arr.price+","+arr.amount+","+arr.value+","+arr.fee+","+arr.total+",'"+getNowFormat()+"', '"+getNowFormat()+"')", function (err) {
		
		if(err) return console.log(err + " ");

		pool.query("DELETE FROM order_stop_limit WHERE id=" + arr.id, function () {
			// emit all my stop limit order in private
			pool.query("SELECT * FROM order_stop_limit WHERE market_id="+arr.market_id+" AND user_id="+arr.user_id+" ORDER BY id desc LIMIT 100", function(err, res) {
				socket.emit('change-my-stop-limit-order', {channel: 'private-' + arr.user_id, password: SOCKET_PASSWORD, message: {data: res.rows, market_id: data.market_id}});
			});

			// emit all my order in private
			pool.query("SELECT * FROM orders WHERE market_id="+arr.market_id+" AND user_id="+arr.user_id+" ORDER BY id desc LIMIT 100", function(err, res) {
				socket.emit('change-my-order', {channel: 'private-' + arr.user_id, password: SOCKET_PASSWORD, message: {data: res.rows,market_id: data.market_id}});
			});

			emitTradeData(arr.market_id, true, false);

			solveStopLimit(data,index+1);
		})
	});
}

function solve (data, index, order, callback) {

	if(order.amount == 0) {

		console.log("END ORDER : " + order.hash);

		fs.writeFileSync('./market_status/' + order.market_id + '.txt', 'true');
		isProccessing[order.user_id] = false;
		return emitTradeData(order.market_id,true,true);
	}

	if(data.length == index) {

		console.log("END ORDER : " + order.hash);

		emitTradeData(order.market_id,false,true);

		if(data.type == 'limit' && order.amount != 0) {
			var value = parseFloat(order.price) * parseFloat(order.amount);
			var total = value + (value * order.fee / 100);
			return addToOrder(order, value, total);
		} else {
			fs.writeFileSync('./market_status/' + order.market_id + '.txt', 'true');
			isProccessing[order.user_id] = false;
		}

		return;
	}

	var arr = data[index];

	if(arr.amount == order.amount) {
		// process buyer
		var value = arr.value;
		var total = value + (value * order.fee / 100);
		
		subtractWallet(order.user_id,order.market.coin_id_second,total,order.coinSecond.symbol, function (err, balance) {

			if(err) return callback();

			plusWallet(order.user_id,order.market.coin_id_first,order.amount,order.coinFirst.symbol, function () {
				socket.emit('notification', {channel: 'private-' + order.user_id, password: SOCKET_PASSWORD, message: {data: "Bought "+order.amount+" "+order.coinFirst.symbol+" at "+arr.price+" "+order.coinSecond.symbol+" and minus "+total+" " + order.coinSecond.symbol, market_id: order.market_id}});
			
				// process seller
				plusWallet(arr.user_id,order.market.coin_id_second,arr.total,order.coinSecond.symbol, function () {
					socket.emit('notification', {channel: 'private-' + arr.user_id, password: SOCKET_PASSWORD, message: {data : "Sold "+arr.amount+" "+order.coinFirst.symbol+" at "+arr.price+" "+order.coinSecond.symbol+" and plus "+arr.total+" " + order.coinSecond.symbol, market_id: order.market_id}});

					// insert market trade history
					value = value.toFixed(8);
					total = total.toFixed(8);
					insertMarketTradeHistory(order.market_id,arr.price,order.amount,value,order.fee,total, order.coinSecond.symbol);

					order.amount = 0;

					deleteOrder(arr.id, order.market_id, arr.user_id, function () {
						solve(data,index+1,order,callback);
					});
				});
				
			});
			
		});

		return;
	}

	if(arr.amount > order.amount) {
		// process buyer
		var value = arr.price * order.amount;
		var total = value + (value * order.fee / 100);

		subtractWallet(order.user_id,order.market.coin_id_second,total,order.coinSecond.symbol, function (err, balance) {

			if(err) return callback();

			plusWallet(order.user_id,order.market.coin_id_first,order.amount,order.coinFirst.symbol, function () {
				socket.emit('notification', {channel: 'private-' + order.user_id, password: SOCKET_PASSWORD, message: {data: "Bought "+order.amount+" "+order.coinFirst.symbol+" at "+arr.price+" "+order.coinSecond.symbol+" and minus "+total.toFixed(8)+" " + order.coinSecond.symbol, market_id: order.market_id}});
				// process seller
				var totalSellerReceive = value - (value * order.fee / 100);

				plusWallet(arr.user_id,order.market.coin_id_second,totalSellerReceive,order.coinSecond.symbol, function () {
					var newAmount = parseFloat(arr.amount) - parseFloat(order.amount);
					var newValue = parseFloat(newAmount) * parseFloat(arr.price);
					var newTotal = newValue - (newValue * order.fee / 100);
					
					socket.emit('notification', {channel: 'private-' + arr.user_id, password: SOCKET_PASSWORD, message: {data: "Sold "+order.amount+" "+order.coinFirst.symbol+" at "+arr.price+" "+order.coinSecond.symbol+" and plus "+totalSellerReceive.toFixed(8)+" " + order.coinSecond.symbol, market_id: order.market_id}});

					// insert market trade history
					value = value.toFixed(8);
					total = total.toFixed(8);
					insertMarketTradeHistory(order.market_id,arr.price,order.amount,value,order.fee,total, order.coinSecond.symbol);

					order.amount = 0;
					updateOrder(arr.id,newAmount,newValue,newTotal, order.market_id, arr.user_id, function () {
						solve(data,index+1,order,callback);
					});
				});
				
			});
			
		});

		return;
	}

	if(arr.amount < order.amount) {
		// process buyer
		var value = arr.price * arr.amount;
		var total = value + (value * order.fee / 100);

		subtractWallet(order.user_id,order.market.coin_id_second,total,order.coinSecond.symbol, function (err, balance) {

			if(err) return callback();
		
			plusWallet(order.user_id,order.market.coin_id_first,arr.amount,order.coinFirst.symbol, function () {
				order.amount -= arr.amount;
				socket.emit('notification', {channel: 'private-' + order.user_id, password: SOCKET_PASSWORD, message: {data: "Bought "+arr.amount+" "+order.coinFirst.symbol+" at "+arr.price+" "+order.coinSecond.symbol+" and minus "+total.toFixed(8)+" " + order.coinSecond.symbol, market_id: order.market_id}});
				// process seller
				plusWallet(arr.user_id,order.market.coin_id_second,arr.total,order.coinSecond.symbol, function () {
					socket.emit('notification', {channel: 'private-' + arr.user_id, password: SOCKET_PASSWORD, message: {data: "Sold "+arr.amount+" "+order.coinFirst.symbol+" at "+arr.price+" "+order.coinSecond.symbol+" and plus "+arr.total+" " + order.coinSecond.symbol, market_id: order.market_id}});

					// insert market trade history
					value = value.toFixed(8);
					total = total.toFixed(8);
					insertMarketTradeHistory(order.market_id,arr.price,arr.amount,value,order.fee,total, order.coinSecond.symbol);

					deleteOrder(arr.id, order.market_id, arr.user_id, function () {
						solve(data,index+1,order,callback);
					});
				});
			});
			
		});

		return;
	}
}

// set order stop Limit
function setOrderStopLimit (data) {
	var value = parseFloat(data.price) * parseFloat(data.amount);
	var total = value + (value * data.fee / 100);

	subtractWallet(data.user_id,data.market.coin_id_second, total, data.coinSecond.symbol, function(err, res) {

		if(err) {
			fs.writeFileSync('./market_status/' + data.market_id + '.txt', 'true');
			isProccessing[data.user_id] = false;
			return 0;
		}

		pool.query("INSERT INTO order_stop_limit(market_id,user_id,type,stop,price,amount,value,fee,total,created_at,updated_at) VALUES("+data.market_id+","+data.user_id+", 'buy' ,"+data.stop+","+data.price+", "+data.amount+", "+value+", "+data.fee+", "+total+", '"+getNowFormat()+"', '"+getNowFormat()+"')", function () {
			// emit all my stop limit order in private
			pool.query("SELECT * FROM order_stop_limit WHERE market_id="+data.market_id+" AND user_id="+data.user_id+" ORDER BY id desc", function(err, res) {
				socket.emit('change-my-stop-limit-order', {channel: 'private-' + data.user_id, password: SOCKET_PASSWORD, message: {data: res.rows, market_id: data.market_id}});
			});
		});
	});
} 

// get all order avaiable to sell for that order pending
function getOrderAvaiable (data) {

	if(data.type == 'limit') {
		var query = "SELECT * from orders WHERE type='sell' AND price <= "+data.price+" AND market_id="+data.market_id+" order by price, id";
	} else if (data.type == 'market') {
		var query = "SELECT * from orders WHERE type='sell' AND market_id="+data.market_id+" order by price, id";
	}

	pool.query(query, function (err, res) {

		if(err) return console.log(err + "");

		if(res.rows.length == 0) {

			if(data.type == 'limit') {

				var value = parseFloat(data.price) * parseFloat(data.amount);
				var total = value + (value * data.fee / 100);

				subtractWallet(data.user_id, data.market.coin_id_second, total, data.coinSecond.symbol, function (err,avaiable) {

					if(err) {
						console.log(err + "");
						fs.writeFileSync('./market_status/' + data.market_id + '.txt', 'true');
						isProccessing[data.user_id] = false;
						return;
					}

					return addToOrder(data, value, total);
				});

			} else {
				fs.writeFileSync('./market_status/' + data.market_id + '.txt', 'true');
				isProccessing[data.user_id] = false;
			}

			return;
			
		}

		solve(res.rows, 0, data, function () {
			fs.writeFileSync('./market_status/' + data.market_id + '.txt', 'true');
			isProccessing[data.user_id] = false;
			return 0;
		});
		
	});
}

// add to table orders if no order avaiable
function addToOrder (data, value, total) {

	pool.query("INSERT INTO orders(market_id,user_id,type,price,amount,value,fee,total,created_at,updated_at) VALUES("+data.market_id+","+data.user_id+",'buy',"+data.price+","+data.amount+","+value+","+data.fee+","+total+",'"+getNowFormat()+"', '"+getNowFormat()+"')", function (err) {
		
		if(err) return console.log(err + " ");

		isProccessing[data.user_id] = false;
		fs.writeFileSync('./market_status/' + data.market_id + '.txt', 'true');

		// emit all my order in private
		console.log("END ORDER : " + data.hash);
		pool.query("SELECT * FROM orders WHERE market_id="+data.market_id+" AND user_id="+data.user_id+" ORDER BY id desc LIMIT 100", function(err, res) {
			socket.emit('change-my-order', {channel: 'private-' + data.user_id, password: SOCKET_PASSWORD, message: {data: res.rows, market_id: data.market_id}});
		});

		emitTradeData(data.market_id, true, false);
		
	});
	
}

// get exchage fee of a market
function getFee (market_id,callback) {
	pool.query("SELECT value FROM exchange_fees WHERE market_id=" + market_id, function (err, res) {
		if(err) return console.log(err + " ");
		callback(res.rows[0].value);
	});
}

// get auth socket
function getAuthSocket (user_id,callback) {
	pool.query("SELECT auth FROM users WHERE id=" + user_id, function (err, res) {
		if(err) return console.log(err + " ");
		callback(res.rows[0].auth);
	});
}

// format number
function formatNumber(number) {
	if(number < 10) return "0" + number;
	return number;
}

function getNowFormat () {
	var now = new Date();
	return now.getFullYear() + "-"+formatNumber(now.getMonth() + 1)+"-"+formatNumber(now.getDate())+" "+formatNumber(now.getHours())+":"+formatNumber(now.getMinutes())+":" + formatNumber(now.getSeconds());
}

function formatNumber(number) {
	if(number < 10) return "0" + number;
	return number;
}