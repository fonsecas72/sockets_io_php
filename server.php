<?php

include __DIR__ . '/vendor/autoload.php';

use Workerman\Worker;
use Workerman\WebServer;
use PHPSocketIO\SocketIO;

$io = new SocketIO(2020);
$io->on('connection', function ($socket) use ($io) {
    global $numUsers;
    global $clicks;
    $clicks = 0;
    ++$numUsers;
    // update for self
    $socket->emit('login', array(
        'numUsers' => $numUsers
    ));
    // needed because it wont refresh on update from other browsers
    $socket->broadcast->emit('login', array(
        'numUsers' => $numUsers
    ));


    $socket->on('new click', function () use($socket) {
        global $clicks;
        ++$clicks;
        $socket->emit('new click', array(
            'clicks' => $clicks
        ));
        $socket->broadcast->emit('new click', array(
            'clicks' => $clicks
        ));
    });


    $socket->on('disconnect', function () use($socket) {
        global $numUsers;
        --$numUsers;
        $socket->broadcast->emit('logout', array(
            'numUsers' => $numUsers
        ));
    });
});

$socketUrl = 'http//0.0.0.0:'.(getenv('PORT') ? : '2022');
echo PHP_EOL.$socketUrl.PHP_EOL;
$web = new WebServer($socketUrl);
$web->addRoot('localhost', __DIR__ . '/public');

Worker::runAll();
