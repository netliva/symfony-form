# NetlivaIconType

İkon seçici form tipi.

## Kullanım

```php
use Netliva\SymfonyFormBundle\Form\Types\NetlivaIconType;

$builder->add('icon', NetlivaIconType::class, [
    'label' => 'İkon Seçin',
    'data' => 'fa fa-home'
]);
```

## Özellikler

- FontAwesome ikon desteği
- İkon önizleme
- Arama özelliği
- Kategorize edilmiş ikonlar

## Parametreler

- `data`: Varsayılan ikon değeri
- `label`: Form etiketi