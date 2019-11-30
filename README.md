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
require('../../public/bundles/netlivasymfonyform/netliva_ajax_submit');
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

## COLLECTION TYPE

Bir form içine tekrarlı olarak başka bir formu eklemek istediğinizde bu formtype'ı kullanabilirsiniz.

Gerekli dosyaları gibi assetic yardımıyla projeye dahil edebilirsiniz; 

```html
<script src="{{ asset('bundles/netlivasymfonyform/netliva_collection.js' }}"></script>
```

yada encore kullanarak projeye dahil edebilirsiniz;

```javascript
// assets/js/app.js
require('../../public/bundles/netlivasymfonyform/netliva_collections');
```

CollectionType'ı kullanmak istediğiniz formda aşağıdaki gibi tanımlayınız. 

```php
<?php
//...
public function buildForm (FormBuilderInterface $formBuilder, array $options)
{
	//...
	$formBuilder->add(
        'field',
        \Netliva\SymfonyFormBundle\Form\Types\NetlivaCollectionType::class, 
        [
            'label' => 'Birden fazla bilgi ekleyebilirsiniz', 
            'entry_type' => EntryType::class, // forma tekrarlı olarak ekleyeeceğiniz formu tanımlayın
            'allow_add' => true,
            'allow_delete' => true
        ]
    );

    //...
}
//...
?>
```

Tekrarlı eklenen formda her eklemede bir js kodu çalışması gerekiyor ise, bu çalışacak kodu belli isimdeki bir fonksiyon içerisine yerleştirmeniz gerekmektedir.

fonksiyon adı, tekrarlı form tanımının id'si arkasına _collect_function eklenerek oluşturulur. Bu foksiyon her bir eklemede veya form yüklendiğinde ekli olan her bir form için bir sefer çalıştırılır.

```javascript
function field_id_collect_function() {
    //...
}
```
Bu tanımlamayı twig içinde şu şekilde yapabilirsiniz;

```twig
<script type="text/javascript">
    function {{ form.field_name.vars.id }}_collect_function (form_container)
    {
        console.log(form_container); // ekleme yapılan li etiketi jQuery objesi olarak döner
        form_container.find(".field_id").datepicker(/*...*/); // gibi bir jquery scripti çalıştırabilrisiniz.
    }
</script>
```

## DEPENDENT ENTITY

Bir entity type'ı başka bir ChoiceType veya EntityType'a bağımlı olarak güncellenmesini isterseniz aşağıdaki DependentType'ı kullanabilirsiniz.

Gerekli dosyaları aşağıdaki gibi assetic yardımıyla projeye dahil edebilirsiniz; 

```html
<script src="{{ asset('bundles/netlivasymfonyform/netliva_dependent.js' }}"></script>
```

yada encore kullanarak projeye dahil edebilirsiniz;

```javascript
// assets/js/app.js
require('../../public/bundles/netlivasymfonyform/netliva_dependent');
```
Aşağıdaki gibi bağımlılık tanımlanabilir. Burada Kullanıcılar, bir üsteki gruplara göre bağlı olarak listelenmektedir. Gruplar Choice olabilieceği gibi veritabanından gelen EntityType'ta oalbilir
```php
<?php
//...
public function buildForm (FormBuilderInterface $formBuilder, array $options)
{
	//...
	$formBuilder			
        ->add('user_group', ChoiceType::class, [
            'label' => 'Kullanıcı Grupları',
            'choices' => [
                'Grup 1' => '1',
                'Grup 2' => '2',
                'Grup 3' => '3',
                'Grup 4' => '4',
            ],
        ])
        ->add(
            'users', 
            \Netliva\SymfonyFormBundle\Form\Types\NetlivaDependentType::class, 
            [
                "label" => "Kullanıcılar",
                'entity_alias' => 'user', // Config dosyasına eklenecek bilgiler ile ilgili tanımlama
                'depend_to' => 'user_group', // bu değerin hangi dieğere bağlı olarak güncelleneceğinin tanımı, bu örnekte üstteki kullanıcı gruplarına bağlı
                'transformer' => false // Veritabanına kaydolacak değer formadan gelenID ise false olmalı, true olduğunda değer Entity'ye çevrilir.
            ]
        );

    //...
}
//...
?>
```

Yukarıda eklenen NetlivaDependentType'ta belirtilen entity_alias tanımı config dosyasında tanımlanır.
Symfony 4 öncesi `./app/config/config.yml` yeni versinyonlarda `./config/packages`klasörü altında `netliva_form.yaml`
 adlı dosya oluşturarak içine aşağıdaki örnek kodlara göre ekleme yapın.
```yaml
# ...
netliva_symfony_form:
  dependent_entities: # tüm tanımlamalarınız ayrı ayrı dependent_entities altında tanımlanacaktır  
    # ...
    user:
      class: App\Entity\User
      where: group
      value: [ name ] # listede görünecekbilgiler
      # aşağıdaki değerler opsiyoneldir ihtiyaca göre tanımlayın
      static_only_show_with_results: true # statik değerlerin bağımlı gelen sonuç ile birlikte gösterilmesi 
      static_values: # burda belirtilen her bir değer, bağımlı gelen öğelerle birlikte listelenir 
         0: ---- SEÇİLMEDİ ----        

      em: ~ # varsayılan entity manager dışında bir tanım kullanmak için
      other_values: [ username ] # burda belirtilen değerler, listede data olarak listelenir <option data-username="value"></option> 
    # ...
# ...
```
 
Yuarıdaki örnekteki tanımlama, kullanıcıyı guruplarına göre arama yaparak listelemeye yönelik bir örnektir. Listede kullanıcı isimleri gözükürken, data olarak da kullanıcıadları eklenir. Listenin en üstünde ise ---SEÇİLMEDİ--- bilgisi yer alır.

####JOIN DEPENDENT

Belirtilen class'ta group alanında arama yapmak için bu yöntem kullanılırken, join olan diğer tablolarda arama yapmakta mümkündür.

Bunun için farklı bir örnek kurgulayabiliriz; Diyelim ki kitap listesi oluşturacaksınız. Kitaplar kitaplıklara onlarda kütüphanelere bağlı ve siz kütüphanelere bağlı olarak kütüphanedeki tüm kitapların listelenmesini istiyorsunuz. O durumda yapımız şu şekilde olacak;


 ```php
<?php
 //...
 public function buildForm (FormBuilderInterface $formBuilder, array $options)
 {
 	//...
 	$formBuilder			
         ->add('library', EntityType::class, [
             'label' => 'Kütüphane Seçiniz',
             'class' => Library::class, 
             'property' => 'name'
             #...
         ])
         ->add(
             'books', 
             \Netliva\SymfonyFormBundle\Form\Types\NetlivaDependentType::class, 
             [
                 "label" => "Kitaplar",
                 'entity_alias' => 'book_alias', 
                 'depend_to' => 'library', 
                 'transformer' => true 
             ]
         );
 
     //...
 }
 //...
 ?>
 ```

Config dosyamızda join tanımlaması şu şekilde yapılacak

```yaml
# ...
netliva_symfony_form:
  dependent_entities: # tüm tanımlamalarınız ayrı ayrı dependent_entities altında tanımlanacaktır  
    # ...
    book_alias:
      class: App\Entity\Books
      value: [ a.book_name, b.shelf_no ] # listede "Kitap adı - 112" şeklinde kirap adları ve kitaplık numaraları listelenir 
      other_values: [ b.id ] # data olarak kitaplık ID'si gönderilir
      where: c.id
      join:
          - a.bookshelf
          - b.library
   # ...
# ...
```

Bu noktada tablo harflendirmelerine dikkat edilmelidir. class'ta belirtilen ana tablonun tanımı a harfidir. Daha sonra sırayla join edilen her bir tablo b,c,d,e.. şeklinde tnaımlar almaya devam eder.
Eğer join kulanıyorsanız, wherei value, other_values alanlarında tanımlamalarınızı tablo ekleriyle yapmanız karışıklığı önleyecektir. 
