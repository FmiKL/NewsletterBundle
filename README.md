# NewsletterBundle

## About

This project is an **application bundle** for Symfony, designed to be intrinsically integrated into an existing application. It revolves around a newsletter management system, enabling the efficient dispatch of information and announcements to subscribed users.

## Configuration

### Installation

Clone the repository into your Symfony project:

```shell
git clone https://github.com/mikael-fourre/NewsletterBundle
```

Ensure the bundle is registered in your `config/bundles.php` file:

```php
return [
    // ...
    App\Bundle\NewsletterBundle\NewsletterBundle::class => ['all' => true],
];
```

### Routing Configuration

Add the bundle's routes to your routing configuration.

```yaml
# config/routes.yaml

newsletter_bundle:
    resource: '@NewsletterBundle/config/routes.yaml'
```

The routes provided by the bundle are:

- `newsletter_subscribe`: Handles newsletter subscriptions
- `newsletter_confirm`: Manages subscription confirmations
- `newsletter_unsubscribe`: Manages unsubscriptions
- `newsletter_send`: Sends newsletters to all subscribed users (admin access only)

### Template Configuration

Configure the template paths by adjusting your `twig.yaml` file to recognize the NewsletterBundle's templates.

```yaml
# config/packages/twig.yaml

twig:
    paths:
        '%kernel.project_dir%/src/Bundle/NewsletterBundle/templates': NewsletterBundle
```

### Parameters Configuration

Define custom parameters in your services.yaml file to configure the contact email address.

```yaml
# config/services.yaml

parameters:
    contact_email: contact@domain.fr
```

### Security Configuration

Secure the administration route by adjusting your `security.yaml` file.

```yaml
# config/packages/security.yaml

security:
    access_control:
        - { path: ^/admin, roles: ROLE_ADMIN }
```

## Usage

- **Subscription**: Send a POST request to `/newsletter/subscribe`
- **Confirmation**: Redirect users to `/newsletter/confirm`
- **Unsubscription**: Use `/newsletter/unsubscribe` for unsubscriptions
- **Newsletter Sending** (admin only): A POST request to `/admin/newsletter/send` sends newsletters

## Contributing

Contributions are always welcome! To contribute:

- Fork the project
- Create a branch for your modifications
- Submit a Pull Request

## Support

Should you encounter issues or have questions, feel free to open an issue on GitHub.

## License

This project is licensed under the terms of the [MIT License](LICENSE). For more information, please refer to the file.
