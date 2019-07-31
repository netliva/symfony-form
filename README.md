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

`$ php bin/console assets:install` 

Bu komut ile; *public/bundles/netlivamedialib* klasörü içerisinde 
oluşan js ve css dosyalarını projenize dahil ediniz.


## AJAX SUBMIT

Oluşturduğunuz formun ajax ile post edilmesini sağlayan form type nesnesidir.
Kullanımını aşağıdaki gibi yapabilirsiniz.

Dosyaları aşağıdaki gibi assetic yardımıyla projeye dahil edebilirsiniz; 

```html
<script src="{{ asset('bundles/netlivasymfonyform/netliva_ajax_submit.js' }}"></script>
```

yada encore kullanarak projeye dahil edebilirsiniz;

```javascript
// assets/js/app.js
require('../../public/bundles/netlivasymfonyform/netliva_ajax_submit.js');
```

Ajax submit kullanmak istediğiniz formda submit butonunu aşağıdaki gibi tanımlayınız. 

```php
<?php
//...
public function buildForm (FormBuilderInterface $formBuilder, array $options)
{
	//...
	$formBuilder->add('submit', \Netliva\SymfonyFormBundle\Form\Types\NetlivaAjaxSubmitType::class, [ 'label' => 'Kaydet' ]);
	//...
}
//...
?>
```
