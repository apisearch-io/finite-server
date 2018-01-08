<?php

/*
 * This file is part of the Apisearch Server
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 * @author PuntMig Technologies
 */

declare(strict_types=1);

namespace Apisearch\Tests\Socket;

use Apisearch\Socket\GracefulFiniteServer;
use React\Socket\TcpServer;
use React\Tests\Socket\TestCase;

/**
 * Class GracefulFiniteServerTest
 */
class GracefulFiniteServerTest extends TestCase
{
    public function testSocketConnectionWillBeForwarded()
    {
        $connection = $this->getMockBuilder('React\Socket\ConnectionInterface')->getMock();
        $connection
            ->expects($this->never())
            ->method('on')
            ->with($this->equalTo('close'), $this->anything());


        $loop = $this->getMockBuilder('React\EventLoop\LoopInterface')->getMock();
        $fileName = tempnam('/tmp', 'test');
        unlink($fileName);
        $this->assertFalse(file_exists($fileName));

        $tcp = new TcpServer(0, $loop);
        new GracefulFiniteServer($tcp, $fileName);
        $tcp->emit('connection', array($connection));
    }

    public function testSocketConnectionWillBeClosed()
    {
        $connection = $this->getMockBuilder('React\Socket\ConnectionInterface')->getMock();
        $connection
            ->expects($this->once())
            ->method('on')
            ->with($this->equalTo('close'), $this->anything());

        $loop = $this->getMockBuilder('React\EventLoop\LoopInterface')->getMock();
        $fileName = tempnam('/tmp', 'test');
        $this->assertTrue(file_exists($fileName));

        $tcp = new TcpServer(0, $loop);
        new GracefulFiniteServer($tcp, $fileName);
        $tcp->emit('connection', array($connection));
        $this->assertFalse(file_exists($fileName));
    }
}
