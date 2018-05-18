var fs = require('fs');
var moment = require('moment');
var express = require('express');
var https = require('https');
var privateKey  = fs.readFileSync('./socket.key', 'utf8');
var certificate = fs.readFileSync('./socket.crt', 'utf8');
var credentials = {key: privateKey, cert: certificate};
var app = express();

// database settings
const { Pool, Client } = require('pg');
const dbConfig = require('./db-config.json');
var pool = new Pool(dbConfig);

pool.connect(function () {
    console.log("\x1b[32m%s\x1b[0m", "Database connected");
});

//pass in your express app and credentials to create an https server
var httpsServer = https.createServer(credentials, app);
httpsServer.listen(8001, function () {
    console.log("\x1b[32m%s\x1b[0m", "WebSocket for Chart is opened");
});

var WebSocketServer = require('ws').Server;
var wss = new WebSocketServer({
    server: httpsServer
});

var subscribeBars = {};

wss.on('connection', function connection(ws) {

    // setInterval(function () {
    //     try { if (ws.readyState != WebSocket.CLOSED) { console.log("WebSocket is CLOSED"); }} catch(e) {console.log(e + "");}
    // },100);

    ws.on('message', function incoming(message) {
        try {
            var json = JSON.parse(message);
        } catch (e) {
            return console.log("ERROR incoming message isn't JSON :" + message);
        }

        var event = json.event;
        var message = json.message;

        if(event == 'getSymbolInfo') {
            getSymbolInfo(message,ws);
        }

        if(event == 'getBars') {
            getBars(message,ws);
        }

        if(event == 'subscribeBars') {
            subscribeBars[message.subscriberUID] = true;
            console.log(subscribeBars);
            setTimeout(function () {
                startSubscribeBars(message,ws);
            },1000)
        }

        if(event == 'unsubscribeBars') {
            subscribeBars[message.subscriberUID] = false;
        }

        console.log("\x1b[32m%s\x1b[0m", "NEW EVENT : " + event);

    });

    ws.on('error', function(err) {
        console.log(err + "");
    });

    ws.on('close', function () {
        console.log("WebSocket is closed");
    })

});

function getSymbolInfo(symbol, ws) {
    try {
        ws.send(JSON.stringify({
            event : "returnGetSymbolInfo",
            message : {
                name : symbol,
                ticker : symbol,
                description : symbol,
                type : 'coin',
                exchange : "Geniota",
                listed_exchange : "Geniota",
                timezone : "UTC",
                session: "24x7",
                pricescale: 100000000,
                minmov: 1,
                has_intraday: true,
                has_no_volume: false,
                supported_resolutions: ["1", "2", "3", "5", "10", "15", "30", "60", "120", "240", "480", "D"],
                volume_precision: 8,
                data_status : 'streaming'
            }

        }));
    } catch (e) {
        console.log("WS send :" + e + "");
    }
    
}

function getDistance (resolution) {
    if(resolution == 'D') return 24 * 60 * 60;
    else {
        return parseInt(resolution) * 60;
    }
}

function getData(start,end,market_id,callback) {
    // get close
    var result = {};
    pool.query("SELECT price FROM market_trade_histories WHERE market_id="+market_id+" AND created_at >= '"+start+"' AND created_at <= '"+end+"' ORDER BY id desc", function (err,res) {
        if(err) return console.log(err + "");
        if(!res || res.rows.length == 0) {
            result.close = 0;
            result.open = 0;
        } else {
            result.open = res.rows[res.rows.length - 1].price;
            result.close = res.rows[0].price;
        }

        pool.query("SELECT max(price) as high, min(price) as low, sum(total) as volume FROM market_trade_histories WHERE market_id="+market_id+" AND created_at >= '"+start+"' AND created_at <= '"+end+"'", function (err,res) {
            if(err) return console.log(err + "");
            if(!res || res.rows.length == 0) {
                result.high = 0;
                result.low = 0;
                result.volume = 0;
            } else {
                result.high = res.rows[0].high == null ? 0 : res.rows[0].high;
                result.low = res.rows[0].low == null ? 0 : res.rows[0].low;
                result.volume = res.rows[0].volume == null ? 0 : res.rows[0].volume;
            }
            
            callback(null,result);
        });
    })
}

// function solve(from,to,distance,market_id,bars,callback) {
//     if(from > to) return callback();

//     var time = to;
//     var end = moment(to,"X").format("YYYY-MM-DD HH:mm:ss");
//     to -= distance;
//     var start = moment(to,"X").format("YYYY-MM-DD HH:mm:ss");

//     getData(start,end, market_id, function(err,data) {
//         data.time = (time + (7 * 60)) * 1000;
//         bars.push(data);
//         solve(from,to,distance,market_id,bars,callback);
//     });
    
// }
 
function solve (from, to, distance, data, bars, callback) {

    var index = 0;
    var temp = 0;

    for(var i = 0; i < data.length; i++) {

        var time = new Date(data[i].created_at) / 1000;

        if(time >= to) {

            if(temp == 0) {
                bars[index] = {};
                bars[index]['time'] = (to + (7 * 60)) * 1000;
                bars[index]['close'] = data[i].price;
                bars[index]['high'] = data[i].price;
                bars[index]['low'] = data[i].price;
                bars[index]['volume'] = 0;
            }

            bars[index]['open'] = data[i].price;

            if(data[i].price > bars[index]['high']) bars[index]['high'] = data[i].price
            if(data[i].price < bars[index]['low']) bars[index]['low'] = data[i].price
            bars[index]['volume'] += data[i].total;

            temp++;
        } else {    

            if(temp == 0) {
                bars[index] = {};
                bars[index]['time'] = (to + (7 * 60)) * 1000;
                bars[index]['close'] = 0;
                bars[index]['open'] = 0;
                bars[index]['high'] = 0;
                bars[index]['low'] = 0;
                bars[index]['volume'] = 0;
            }

            to -= distance;
            index++;
            temp = 0;
            i--;
        }
    }

    while(to > from) {
        to -= distance;
        bars[index] = {};
        bars[index]['time'] = (to + (7 * 60)) * 1000;
        bars[index]['close'] = 0;
        bars[index]['open'] = 0;
        bars[index]['high'] = 0;
        bars[index]['low'] = 0;
        bars[index]['volume'] = 0;
        index++;
    }

    console.log(to,from);

    callback();
}

function getBars (data,ws) {
    var distance = getDistance(data.resolution);
    var to = data.to;
    var from = data.from;
    var bars = [];

    // get market id
    pool.query("SELECT id FROM markets WHERE name='"+data.symbolInfo.ticker+"'", function (err,res) {
        var fromFormat = moment(from,"X").format("YYYY-MM-DD HH:mm:ss");
        var toFormat = moment(new Date().getTime() / 1000,"X").format("YYYY-MM-DD HH:mm:ss");
        var market_id = res.rows[0].id;
        pool.query("SELECT price,total,created_at FROM market_trade_histories WHERE market_id="+market_id+" AND created_at >= '"+fromFormat+"' AND created_at <= '"+toFormat+"' ORDER BY created_at desc", function (err,res){
            solve(from,new Date().getTime() / 1000,distance,res.rows,bars,function () {
                var bar = bars.reverse();
                try {
                    ws.send(JSON.stringify({
                        event : "returnGetBars",
                        message : {
                            bar : bar,
                            meta : {
                                noData : false
                            }
                        }
                    }))
                } catch (e) {
                    console.log("SEND error :" + e + " ");
                }
                
            });
        });
        
    })
}

function searchTime (end,distance,now, callback) {
    while(1) {

        if(end >= now - distance && end <= now) {
            return callback(end);
        }

        end += distance;
    }
}

function startSubscribeBars (data,ws,lastID = 0) {
    if(subscribeBars.hasOwnProperty(data.subscriberUID) && subscribeBars[data.subscriberUID] == true) {
        var end = (data.end / 1000) - (7 * 60);
        var distance = getDistance(data.resolution);

        pool.query("SELECT id FROM markets WHERE name='"+data.symbolInfo.ticker+"'", function (err,res) {
            var market_id = res.rows[0].id;
            pool.query("SELECT id FROM market_trade_histories WHERE market_id=" + market_id + " ORDER BY id desc LIMIT 1", function (err,res) {
                if(err) console.log(err + "");
                if(res.rows.length == 0) return startSubscribeBars(data,ws);
                if(res.rows[0].id == lastID) return startSubscribeBars(data,ws,res.rows[0].id);
                var now = new Date().getTime();
                now /= 1000;

                if(end + distance <= now) {
                    getData(moment(end - distance,"X").format("YYYY-MM-DD HH:mm:ss"),moment(end,"X").format("YYYY-MM-DD HH:mm:ss"),market_id,function (err,result) {
                        console.log("FIRST");
                        console.log(result);
                        result.time = data.end;
                        try {
                            ws.send(JSON.stringify({
                                event : "returnSubscribeBars",
                                message : result,
                                subscriberUID: data.subscriberUID
                            }))
                        } catch (e) {
                            console.log("SEND ERROR " + e + "");
                        }
                        
                        return startSubscribeBars(data,ws,res.rows[0].id);
                    })
                } else {
                    getData(moment(now - distance,"X").format("YYYY-MM-DD HH:mm:ss"),moment(now,"X").format("YYYY-MM-DD HH:mm:ss"),market_id,function (err,result) {
                        searchTime(end,distance,now, function (time) {
                            result.time = (time + (7 * 60)) * 1000;
                            data.end = result.time;
                            try {
                                ws.send(JSON.stringify({
                                    event : "returnSubscribeBars",
                                    message : result,
                                    subscriberUID: data.subscriberUID
                                }))
                            } catch (e) {
                                console.log("SEND ERROR " + e + "");
                            }
                            return startSubscribeBars(data,ws,res.rows[0].id);
                        })
                        
                    })
                }
            })
        })
    } else {
        return;
    }
}