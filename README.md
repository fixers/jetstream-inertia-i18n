# Introduction

This package contains a method to easily enable translation of Vue components within [Laravel Jetstream](https://jetstream.laravel.com/) projects built
with the [Inertia](https://jetstream.laravel.com/stacks/inertia.html) stack.

This package is **not** compatible with the [Livewire](https://jetstream.laravel.com/stacks/livewire.html) stack.

The implementation uses [vue-i18n](https://vue-i18n.intlify.dev/).

## Warning

This will overwrite the Vue components, layout and pages that come with Jetstream, and add the necessary code to enable translation. If you have made changes to these files, you will need to merge them manually.

Using this package should be done at the start of a project, before making any changes to the Jetstream components.

## Installation

You can install the package via composer:

```bash
composer require fixers/jetstream-inertia-i18n --dev
```

The package uses Laravel's auto-discovery feature to register the service provider.

When ready to publish the package's config file, run the following command:

```bash
php artisan fixers:publish-components
```

This will introduce you to a warning message, asking you to confirm the publishing of the package's files.

You will also be prompted to include any of the available languages in the package.

You are welcome to contribute with your custom language file as a Pull Request.

## TODO

- [ ] Add something that ensures we are up to date with Jetstream's components
- [ ] Add more languages
