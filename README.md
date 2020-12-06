# Netliva Symfony Form Bundle
Symfony için kullanılabilecek form tipleri


Installation
============

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
$ composer require netliva/symfony-form
```

Applications that don't use Symfony Flex
----------------------------------------

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require netliva/symfony-form
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

_NetlivaFileTypeBundle has been installed as a dependency of NetlivaMediaLibBundle.
 If you have not installed **NetlivaFileTypeBundle** before,
 you must enable it because of the dependency._


```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Netliva\SymfonyFormBundle\NetlivaSymfonyFormBundle(),
        );

        // ...
    }

    // ...
}
```


Assetleri Projeye Dahil Edin
----------------------------
### Install assets

Aşağıdaki komut ile assets'lerin kurulumunu gerçekleştirin

`$ yarn add @netliva/symfony-form` 

Bu komut ile gerekli js ve css dosyalarını projenize dahil ediniz.


Form Type Kullanımı
----------------------------

- [Ajax Submit](docs/ajax_submit.md)
- [Autocomplete](docs/autocomplete.md) (Kullanma Klavuzu Hazırlanacak)
- [Collection](docs/collection.md) 
- [Collection Contact](docs/collection_contact.md) (Kullanma Klavuzu Hazırlanacak)
- [Tags Input](docs/tags_input.md)
- [Color Picker](docs/color_picker.md) (Kullanma Klavuzu Hazırlanacak)
- [Custom Fields](docs/custom_fields.md) (Kullanma Klavuzu Hazırlanacak)
- [Dependent Entity](docs/dependent_entity.md)
- [Date Picker](docs/date_picker.md) (Kullanma Klavuzu Hazırlanacak)
- [Icon Type](docs/icon_type.md) (Kullanma Klavuzu Hazırlanacak)
- [Numeric Spin](docs/numeric_spin.md) (Kullanma Klavuzu Hazırlanacak)
- [Tree Select](docs/date_picker.md) (Kullanma Klavuzu Hazırlanacak)

