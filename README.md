# NewsletterBundle

A simple newsletter bundle for Symfony applications.

## Compatibility

- Symfony 7.4 / 8.0

## Setup

Clone the repository into your Symfony project and register the bundle:

```php
return [
    App\Bundle\NewsletterBundle\NewsletterBundle::class => ['all' => true],
];
```

Import the bundle routes:

```yaml
newsletter_bundle:
    resource: '@NewsletterBundle/config/routes.yaml'
```

Register the bundle templates:

```yaml
twig:
    paths:
        '%kernel.project_dir%/src/Bundle/NewsletterBundle/templates': NewsletterBundle
```

Set the contact email:

```yaml
parameters:
    contact_email: contact@example.com
```

## Routes

- `POST /newsletter/subscribe`
- `GET /newsletter/confirm`
- `GET /newsletter/unsubscribe`
- `POST /admin/newsletter/send`

## License

[MIT](LICENSE)
