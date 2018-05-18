const SERVER_PORT = 8000

const { Pool, Client } = require('pg');
const dbConfig = require('./db-config.json');
var fs = require('fs')
var https = require('https')
var express = require('express')
var app = express()

var options = {
    key: fs.readFileSync('./socket.key'),
    cert: fs.readFileSync('./socket.crt'),
}

var server = https.createServer(options, app)
var io = require('socket.io').listen(server)
var middleware = require('socketio-wildcard')();

io.use(middleware);

var redis = require('redis')
var ioredis = require('socket.io-redis')

// Multi-server socket handling allowing you to scale horizontally 
// or use a load balancer with Redis distributing messages across servers.
// io.adapter(ioredis({host: 'localhost', port: 6379}))

//

/*
 * Redis pub/sub
 */

// Listen to local Redis broadcasts
var sub = redis.createClient()

sub.psubscribe("*");

sub.on('error', function (error) {
    console.log('ERROR ' + error)
})

sub.on('psubscribe', function (channel, count) {
    console.log('PSUBSCRIBE', channel, count)
})

// Handle messages from channels we're subscribed to
sub.on('pmessage', function (partner, channel, payload) {
    console.log('INCOMING MESSAGE', channel, payload)

    payload = JSON.parse(payload)
        
    // Merge channel into payload
    payload.data._channel = channel
    
    // Send the data through to any client in the channel room (!)
    // (i.e. server room, usually being just the one user)
    io.sockets.in(channel).emit(payload.event, payload.data)
})

/*
 * Server
 */

 var count = 0;

// Start listening for incoming client connections
io.sockets.on('connection', function (socket) {

    count++;
    
    console.log('NEW CLIENT CONNECTED and TOTAL: ' + count);

    // socket.on('new-my-order', function (data) {
    //     console.log("NEW MY ORDER");
    //     console.log(data);
    // });

    socket.on('*', function(packet){
        // NEW EVENT
        if(packet.data.length != 2) return socket.disconnect();
        if(!packet.data[1].channel) return socket.disconnect();

        var event = packet.data[0];

        console.log("NEW EVENT : " + event);

        var channel = packet.data[1].channel;
        var message = packet.data[1].message || null;
        var auth = packet.data[1].auth || null;
        var password = packet.data[1].password || null;

        if(event == 'subscribe-to-channel') return subscribeChannel(channel,auth,socket);

        if (!password) return socket.disconnect();

        if(password == '900150983cd24fb0d6963f7d28e17f72') return io.sockets.in(channel).emit(event, {message:message});

        // client.emit('foo', 'bar', 'baz')
        // packet.data === ['foo', 'bar', 'baz']
    });
    
    socket.on('disconnect', function () {
        count--;
        socket.disconnect();
        console.log('DISCONNECT')
    })
    
})

// Start listening for client connections
server.listen(SERVER_PORT, function () {
    console.log('Listening to incoming client connections on port ' + SERVER_PORT)
})

function subscribeChannel (channel,auth,socket) {
    console.log('NEW SUBSCRIBE TO CHANNEL : ' + channel);

        // check is private
    if(channel.search('private') != -1) {

        if(!auth) return socket.disconnect();

        var arr = channel.split('-');

        if(arr.length != 2) return socket.disconnect();

        var user_id = arr[1];

        checkAuth(user_id, auth, channel, socket);

    } else {
        socket.join(channel);
    }
}

function checkAuth (user_id, auth, channel, socket) {
    const pool = new Pool(dbConfig);
    pool.query('SELECT auth FROM users WHERE id=' + user_id, (err, res) => {
        
        if(err) return console.log(err + " ");

        if(res.rows[0].auth != null && res.rows[0].auth != auth) {
            return socket.disconnect();
        }

        // Subscribe to the Redis channel using our global subscriber
        // sub.psubscribe(data.channel)
        
        // Join the (somewhat local) server room for this channel. This
        // way we can later pass our channel events right through to 
        // the room instead of broadcasting them to every client.
        socket.join(channel);
        pool.end();

    });

}