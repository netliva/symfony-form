

## COLLECTION TYPE

Bir form içine tekrarlı olarak başka bir formu eklemek istediğinizde bu formtype'ı kullanabilirsiniz.

webpack yardımıyla gerekli olan dosyaları projeye dahil edebilirsiniz;

Yalnızca bu form tipini kullanacaksanız, aşağıdaki gibi yalnız bu js dosyasını projenize dahil edin.
```javascript
// assets/js/app.js
import '@netliva/symfony-form/src/js/collection_contact'
import '@netliva/symfony-form/src/css/collection_contact.css'
```
Yada diğer form tiplerini de kullanacaksanız,
```javascript
// assets/js/app.js
import '@netliva/symfony-form'
```
Şeklinde tek seferde, tüm js kütüphanesini projenize dahil edebilirsiniz.

## Kullanım

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
