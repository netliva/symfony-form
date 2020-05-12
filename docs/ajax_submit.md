# AJAX SUBMIT

Oluşturduğunuz formun ajax ile post edilmesini sağlayan form type nesnesidir.
Kullanımını aşağıdaki gibi yapabilirsiniz.

webpack yardımıyla gerekli olan dosyaları projeye dahil edebilirsiniz;

Yalnızca bu form tipini kullanacaksanız, aşağıdaki gibi yalnız bu js dosyasını projenize dahil edin.
```javascript
// assets/js/app.js
import '@netliva/symfony-form/src/js/ajax_submit'
```
Yada diğer form tiplerini de kullanacaksanız,
```javascript
// assets/js/app.js
import '@netliva/symfony-form'
```
Şeklinde tek seferde, tüm js kütüphanesini projenize dahil edebilirsiniz.

## Kullanım

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
