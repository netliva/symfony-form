# NetlivaColorPickerType

Renk seçici form tipi.

## Kullanım

```php
use Netliva\SymfonyFormBundle\Form\Types\NetlivaColorPickerType;

$builder->add('color', NetlivaColorPickerType::class, [
    'label' => 'Renk Seçin',
    'data' => '#ff0000'
]);
```

## Özellikler

- HTML5 color input kullanır
- Hex renk formatı
- Varsayılan renk değeri
- Responsive tasarım

## Parametreler

- `data`: Varsayılan renk değeri (hex format)
- `label`: Form etiketi