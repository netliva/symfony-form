

## TAGS INPUT TYPE

Text input içinde etiket oluşturmak için kullanılır.

webpack yardımıyla gerekli olan dosyaları projeye dahil edebilirsiniz;

Yalnızca bu form tipini kullanacaksanız, aşağıdaki gibi yalnız bu js dosyasını projenize dahil edin.
```javascript
// assets/js/app.js
import '@netliva/symfony-form/src/js/tagsiput'
```
Yada diğer form tiplerini de kullanacaksanız,
```javascript
// assets/js/app.js
import '@netliva/symfony-form'
```
Şeklinde tek seferde, tüm js kütüphanesini projenize dahil edebilirsiniz.

## Kullanım

TagsInput'u kullanmak istediğiniz formda aşağıdaki gibi tanımlayınız. 

```php
<?php
//...
public function buildForm (FormBuilderInterface $formBuilder, array $options)
{
	//...
	$formBuilder->add(
        'field',
        \Netliva\SymfonyFormBundle\Form\Types\NetlivaTagsInputType::class, 
        [
            'label' => 'Etiketler', 
        ]
    );

    //...
}
//...
?>
```
