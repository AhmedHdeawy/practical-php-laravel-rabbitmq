# RabbitMQ

## Practical examples using RabbitMQ in php and laravel

# Plain PHP

- ### Work Queues
  - Distributing tasks among workers

  ![Distributing tasks among workers.](https://www.rabbitmq.com/img/tutorials/python-two.png)

- ### Publish/Subscribe

  - Sending messages to many consumers at once

  ![Sending messages to many consumers at once.](https://www.rabbitmq.com/img/tutorials/python-three.png)

- ### Routing (Exchange)

  - Receiving messages selectively

  ![Receiving messages selectively.](https://www.rabbitmq.com/img/tutorials/python-four.png)

- ### RPC

  - Request/reply pattern

  ![Request/reply pattern.](https://www.rabbitmq.com/img/tutorials/python-six.png)



# Laravel

### Use RabbitMQ as a default queue to handle all internal Jobs

- Dispatch all jobs on one rabbitmq queue
```
  sail artisan rabbitmq:consume
```


### Pub/Sub 

- Use rabbitmq to consume all published messages from any external servers, and this done through a command that is configure rabbitmq to consume messages

```
sail artisan app:rabbit-mq-subscriber
```


## Enjoy
