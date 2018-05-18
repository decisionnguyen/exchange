<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.4/socket.io.js"></script>
	<script>
		const PRIVATE_CHANNEL = 'private-9'

		var host = window.location.host.split(':')[0]
		var socket = io.connect('https://' + host + ':8000', {secure: true, rejectUnauthorized: false})

		socket.on('connect', function () {
		    console.log('CONNECT')

		    socket.on('event', function (data) {
		        console.log('EVENT', data)
		    })

		    socket.on('new-order', function (data) {
		        console.log('NEW PRIVATE MESSAGE', data)
		    })

		    socket.on('new-my-order', function (data) {
		    	console.log("NEW MY ORDER FOR ME");
		    	console.log(data);
		    })

		    socket.on('disconnect', function () {
		        console.log('disconnect')
		    })

		    // Kick it off
		    // Can be any channel. For private channels, Laravel should pass it upon page load (or given by another user).
		    socket.emit('subscribe-to-channel', {channel: PRIVATE_CHANNEL, auth: "vksla86029jwbkh1j5wca368lplmb9ig"})
		    console.log('SUBSCRIBED TO <' + PRIVATE_CHANNEL + '>');
		})

	</script>
	<title>Test Ssocket</title>
</head>
<body>
	
</body>
</html>