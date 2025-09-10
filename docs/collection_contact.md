# NetlivaCollectionContactType

İletişim bilgileri için collection form tipi.

## Kullanım

```php
use Netliva\SymfonyFormBundle\Form\Types\NetlivaCollectionContactType;

$builder->add('contacts', NetlivaCollectionContactType::class, [
    'label' => 'İletişim Bilgileri',
    'allow_add' => true,
    'allow_delete' => true,
    'by_reference' => false
]);
```

## Özellikler

- Otomatik iletişim türü seçimi
- Telefon numarası validasyonu
- Email validasyonu
- Dinamik ekleme/çıkarma

## Parametreler

- `allow_add`: Yeni öğe ekleme
- `allow_delete`: Öğe silme
- `by_reference`: Referans ile çalışma