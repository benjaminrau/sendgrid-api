# SendGrid Bundle - SendGrid library integration with Symfony Framework.

## Dependencies

- Symfony 3
- SendGrid 5.1

## Setup

### composer.json

```
{
    ...
    "repositories": [
        ...
        {
            "type": "git",
            "url": "git@bitbucket.org:krukowski_net/sendgridbundle.git"
        }
        ...
    ],
    ... 
    "require": {
            ...
            "ins/sendgrid-bundle": "dev-master",
            ...
    },
    ...
    
    
```

### app/AppKernel.php

```
public function registerBundles()
    {
        $bundles = [
            ...
            new Ins\SendGridBundle\SendGridBundle(),
            ...			
        ];

        ...
    }
```

### app/config/config.yml

```
send_grid:
    apikey:                 "apikey"
    enable_restriction:     false
    delivery_address:       "test@example.com"
    patterns:
      - "pattern1"
      - "pattern2"

```
