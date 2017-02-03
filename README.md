# order-cms-bundle

this README is currently in progress. Thank you for your understanding...

Designed to work fine with the DyweeCoreBundle providing great administration features

##Installing

just run
```bash
$ composer require dywee/order-cms-bundle
```

add the bundle to the kernel
```php
new Dywee\OrderCMSBundle\DyweeOrderCMSBundle(),
```

add the routing informations
```yml
dywee_order_cms:
    resource: "@DyweeOrderCMSBundle/Controller"
    type: annotation
    prefix:   /
```

no more configuration needed
