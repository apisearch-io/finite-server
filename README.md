# Finite Server for ReactPHP Socket

This is our middleware package for ReactPHP Socket project. In our road to make
this world a better place, we met ReactPHP. And our lives changed completely. We
discovered how ReactPHP can make your project a extremely fast service.

So, in order to help them a little bit, we added here some middleware classes
created by our team. You can find a little bit more about the ReactPHP project
in their [website](https://reactphp.org/)

## Install

You can install the package by using composer.

```
composer apisearch-io/react-socket-middleware-finite-server
```

## Finite Server

How many iterations should your server deliver?
This may be a strange question, but remember than between every server
iteration, used memory could be stored forever and ever. It is your
responsibility to make this scenario a rare one, but reality is not always the
most perfect one.

So, in order to flush all this *in perpetuum* memory, the best action we should
consider is to kill the thread. Then, you should consider using a thread manager
like [Supervisor](http://supervisord.org/) to make sure the thread is always
created again.

We introduce FiniteServer, a n-loops server.
This server works exactly the same way than other ones. In fact, this is a
simple wrapper that, after n loops, the server will close itself. Something like
a self-graceful suicide action.

The wrapper is created by using an extra parameter called $iterations.

``` php
$server = new FiniteServer($socketServer, 1000);
```

## Greaceful Finite Server

What happens if the server itself wants to kill your server? For example, when
cache changes and want your servers to serve new features as soon as possible.
Here you have some options you have in front of this scenario.

- Supervisor service restart - If the supervisor configuration is the same, that
options seems to be the worst. All processes then will be stopped and restarted.
If any thread is being served, will be killed and no response will be served.

- Kill manually the processes. Same as last option, but without supervisor. Same
thread effects.

- Use Finite Server, and wait for thread auto-die. If you expect real-time 
changes in production, then you should add a low iterations number, or a high 
production petitions. Neither a valid option.

With this implementation, a new deploy could be something like

- pull project
- clear cache
- touch(file/s)

The wrapper is created by using an extra parameter called $file.

``` php
$server = new GracefulFiniteServer($socketServer, '/tmp/socket_xxx.tmp');
```