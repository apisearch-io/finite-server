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

use Apisearch\Socket\FiniteServer;
use React\Tests\Socket\TestCase;

/**
 * Class FiniteServerTest
 */
class FiniteServerTest extends TestCase
{
    public function testDefaultTimes()
    {
        $socketServer = $this->getMockBuilder('\React\Socket\ServerInterface')->getMock();
        $tcpConnection = $this->getMockBuilder('React\Socket\ConnectionInterface')->getMock();
        $tcpConnection->expects($this->exactly(0))->method('on');
        $server = new FiniteServer($socketServer);
        $server->handleConnection($tcpConnection);
        $server->handleConnection($tcpConnection);
        $server->handleConnection($tcpConnection);
        $server->handleConnection($tcpConnection);
        $server->handleConnection($tcpConnection);
    }

    public function testInvalidTimesWorkingAsNeverStop()
    {
        $socketServer = $this->getMockBuilder('\React\Socket\ServerInterface')->getMock();
        $tcpConnection = $this->getMockBuilder('React\Socket\ConnectionInterface')->getMock();
        $tcpConnection->expects($this->exactly(0))->method('on');
        $server = new FiniteServer($socketServer, -1);
        $server->handleConnection($tcpConnection);
        $server->handleConnection($tcpConnection);
    }

    public function testNeverStop()
    {
        $socketServer = $this->getMockBuilder('\React\Socket\ServerInterface')->getMock();
        $tcpConnection = $this->getMockBuilder('React\Socket\ConnectionInterface')->getMock();
        $tcpConnection->expects($this->exactly(0))->method('on');
        $server = new FiniteServer($socketServer, FiniteServer::NEVER_STOP);
        $server->handleConnection($tcpConnection);
        $server->handleConnection($tcpConnection);
        $server->handleConnection($tcpConnection);
        $server->handleConnection($tcpConnection);
        $server->handleConnection($tcpConnection);
    }

    public function testRunAndStop()
    {
        $socketServer = $this->getMockBuilder('\React\Socket\ServerInterface')->getMock();
        $tcpConnection = $this->getMockBuilder('React\Socket\ConnectionInterface')->getMock();
        $tcpConnection->expects($this->exactly(1))->method('on');
        $server = new FiniteServer($socketServer, 1);
        $server->handleConnection($tcpConnection);
    }

    public function testRunNTimes()
    {
        $socketServer = $this->getMockBuilder('\React\Socket\ServerInterface')->getMock();
        $tcpConnection = $this->getMockBuilder('React\Socket\ConnectionInterface')->getMock();
        $tcpConnection->expects($this->exactly(1))->method('on');
        $server = new FiniteServer($socketServer, 3);
        $server->handleConnection($tcpConnection);
        $server->handleConnection($tcpConnection);
        $server->handleConnection($tcpConnection);
    }
}
