$(function () {
    var socket = io('http://' + document.domain + ':2020');
    
    socket.on('login', function (data) {
        connected = true;
        $('#users').text(data.numUsers);
    });
    socket.on('logout', function (data) {
        connected = false;
        $('#users').text(data.numUsers);
    });
    
    socket.on('new click', function (data) {
        $('#clicks').text(data.clicks);
    });

    $('#click').click(function () {
        socket.emit('new click');
    });
});
