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

namespace Apisearch\Socket;

use Evenement\EventEmitter;
use React\Socket\ConnectionInterface;
use React\Socket\ServerInterface;

/**
 * The `GracefulFiniteServer` decorator wraps a given `ServerInterface` and is
 * responsible to die in a graceful way when an specific file exists.
 *
 * Before dying, this file is properly removed.
 *
 * ```php
 * $server = new GracefulFiniteServer($server, '/tmp/server.txt);
 * $server->on('connection', function (ConnectionInterface $connection) {
 *     $connection->write('hello there!' . PHP_EOL);
 *     …
 * });
 * ```
 *
 * See also the `ServerInterface` for more details.
 *
 * @see ServerInterface
 * @see ConnectionInterface
 */
class GracefulFiniteServer extends EventEmitter implements ServerInterface
{
    private $server;
    private $file;

    /**
     * @param ServerInterface $server
     * @param string $file
     */
    public function __construct(ServerInterface $server, $file)
    {
        $this->file = $file;
        $this->server = $server;
        $this->server->on('connection', array($this, 'handleConnection'));
        $this->server->on('error', array($this, 'handleError'));
    }

    public function getAddress()
    {
        return $this->server->getAddress();
    }

    public function pause()
    {
        $this->server->pause();
    }

    public function resume()
    {
        $this->server->resume();
    }

    public function close()
    {
        $this->server->close();
    }

    /** @internal */
    public function handleConnection(ConnectionInterface $connection)
    {
        if (
            file_exists($this->file) &&
            is_writable($this->file)
        ) {
            unlink($this->file);
            $connection->on('close', function () {
                $this->server->close();
            });
        }

        $this->emit('connection', array($connection));
    }

    /** @internal */
    public function handleError(\Exception $error)
    {
        $this->emit('error', array($error));
    }
}
