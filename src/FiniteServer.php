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
 * The `FiniteServer` decorator wraps a given `ServerInterface` and is
 * responsible for limiting the server to serve, maximum, n times.
 *
 * This is crucial if there is a master system member, like Supervisord,
 * always checking that there's an instance running, by creating always a new
 * one if the last one dies.
 *
 * In that case, the Server will close itself once served n responses.
 *
 * In this example, once the server has served 1000 instances, will close
 * itself.
 *
 * ```php
 * $server = new FiniteServer($server, 1000);
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
class FiniteServer extends EventEmitter implements ServerInterface
{
    const NEVER_STOP = 0;
    private $server;
    private $iterations = 1;
    private $maxIterations;

    /**
     * @param ServerInterface $server
     */
    public function __construct(ServerInterface $server, $maxIterations = self::NEVER_STOP)
    {
        $maxIterations = is_int($maxIterations)
            ? $maxIterations
            : self::NEVER_STOP;

        $this->server = $server;
        $this->maxIterations = $maxIterations;
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
            $this->maxIterations > 0 &&
            $this->iterations >= $this->maxIterations
        ) {
            $connection->on('close', function () {
                $this->server->close();
            });
        }
        $this->emit('connection', array($connection));
        $this->iterations++;
    }

    /** @internal */
    public function handleError(\Exception $error)
    {
        $this->emit('error', array($error));
    }
}
