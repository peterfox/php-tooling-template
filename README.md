# :vendor_name Tooling

[![Tests](https://img.shields.io/github/actions/workflow/status/:vendor_slug/:package_slug/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/:vendor_slug/:package_slug/actions/workflows/run-tests.yml)

<!--delete-->
---
This package can be used as to scaffold a PHP tooling package for creating PHPStan and Rector rules. Follow these steps to get started:

1. Press the "Use template" button at the top of this repo to create a new repo with the contents of this PHP Tooling skeleton
2. Run "php ./configure.php" to run a script that will replace all placeholders throughout all the files
3. Start writing your own PHPStan and Rector rules.

---
<!--/delete-->

This is the tooling repository for :vendor_name. In here contains PHPStan rules for :vendor_name PHP development.

## Installation

You can install the package via composer:

```bash
# If it's a private git repository
composer config repositories.:vendor_slug/:package_slug vcs https://github.com/:vendor_slug/:package_slug
composer require :vendor_slug/:package_slug
```

Add to your PHPStan config.

```yaml
includes:
	- vendor/:vendor_slug/:package_slug/src/phpstan.neon
```

Add to your Rector config using import.

```php
$rectorConfig->import(__DIR__ . '/vendor/:vendor_slug/:package_slug/src/rector.php');
```

## Developing Rules

// todo add usage

## Testing

```bash
composer test
```

