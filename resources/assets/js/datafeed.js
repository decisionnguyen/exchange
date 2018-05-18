var Datafeeds = {};

Datafeeds.UDFCompatibleDatafeed = function () {
	this.config = {};
	this.connection = {};
	this._connectWebsocket();
	this._supported_resolutions = ["1", "2", "3", "5", "10", "15", "30", "60", "120", "240", "480", "D"];
	this.lastBars = {};
	this.lastBars.end = 0;
	this.socketStatus = false;
}

Datafeeds.UDFCompatibleDatafeed.prototype._checkSocketStatus = function (callback) {
	var _this = this;
	if(this.socketStatus == false) {
		setTimeout(function () {
			_this._checkSocketStatus(callback);
		},1000);
	} else {
		callback(_this.config);
	}
}

Datafeeds.UDFCompatibleDatafeed.prototype.onReady = function (callback) {
	var _this = this;
	this._checkSocketStatus(callback);
}

Datafeeds.UDFCompatibleDatafeed.prototype.defaultConfiguration = function () {
	return {
		supports_search: false,
		supports_group_request: false,
		supported_resolutions: this._supported_resolutions,
		supports_marks: true,
		volume_precision: 8,
		exchanges: [],
		symbolsTypes: [{
			name: "Geniota",
			value: "Geniota"
		}]
	}
}

Datafeeds.UDFCompatibleDatafeed.prototype.resolveSymbol = function (symbolName, onSymbolResolvedCallback, onResolveErrorCallback) {

	var _this = this;

	this.connection.send(JSON.stringify({
		'event': 'getSymbolInfo',
		'message' : symbolName,
	}));

	this.connection.onmessage = function (message) {
		try {
		  var json = JSON.parse(message.data);
		} catch (e) {
		  console.log('This doesn\'t look like a valid JSON: ',
		      message.data);
		  return;
		}

		if(json.event == 'returnGetSymbolInfo') {
			onSymbolResolvedCallback(json.message);
		}
	};
}

Datafeeds.UDFCompatibleDatafeed.prototype.getBars = function (symbolInfo, resolution, from, to, onHistoryCallback, onErrorCallback, firstDataRequest) {

	var _this = this;

	_this.connection.send(JSON.stringify({

		event : 'getBars',
		message : {
			symbolInfo : symbolInfo,
			resolution: resolution,
			from : from,
			to: to,
			firstDataRequest: firstDataRequest
		}

	}));

	_this.connection.onmessage = function (message) {
		try {
		  var json = JSON.parse(message.data);
		} catch (e) {
		  console.log('This doesn\'t look like a valid JSON: ',
		      message.data);
		  return;
		}

		if(json.event == 'returnGetBars') {
			_this.lastBars.end = json.message.bar[json.message.bar.length - 1].time;
			onHistoryCallback(json.message.bar,json.message.meta);
		}
	};


}

Datafeeds.UDFCompatibleDatafeed.prototype.subscribeBars = function (symbolInfo, resolution, onRealtimeCallback, subscriberUID, onResetCacheNeededCallback) {
	var _this = this;

	_this.connection.send(JSON.stringify({

		event : 'subscribeBars',
		message : {
			symbolInfo : symbolInfo,
			resolution: resolution,
			subscriberUID : subscriberUID,
			end: _this.lastBars.end
		}

	}));

	_this.connection.onmessage = function (message) {
		try {
		  var json = JSON.parse(message.data);
		} catch (e) {
		  console.log('This doesn\'t look like a valid JSON: ',
		      message.data);
		  return;
		}

		if(json.event == 'returnSubscribeBars' && json.subscriberUID == subscriberUID) {
			onRealtimeCallback(json.message);
		}
	};
}

Datafeeds.UDFCompatibleDatafeed.prototype.unsubscribeBars  = function (subscriberUID) {
	var _this = this;

	_this.connection.send(JSON.stringify({

		event : 'unsubscribeBars',
		message : {
			subscriberUID : subscriberUID,
		}

	}));

}

Datafeeds.UDFCompatibleDatafeed.prototype._connectWebsocket = function () {
	var _this = this;
	_this.connection = new WebSocket('wss://socket.geniota.com:8001');

	_this.connection.onopen = function () {
		_this.socketStatus = true;
	};

	_this.connection.onerror = function (error) {
		console.log("ERROR : " + error + "");
	};
}

module.exports = Datafeeds;