# NetlivaNumericSpinType

Sayısal değer girişi için spin form tipi.

## Kullanım

```php
use Netliva\SymfonyFormBundle\Form\Types\NetlivaNumericSpinType;

$builder->add('quantity', NetlivaNumericSpinType::class, [
    'label' => 'Miktar',
    'data' => 1,
    'min' => 0,
    'max' => 100,
    'step' => 1
]);
```

## Özellikler

- Artırma/azaltma butonları
- Min/max değer sınırları
- Adım değeri ayarlama
- Klavye desteği

## Parametreler

- `min`: Minimum değer
- `max`: Maksimum değer
- `step`: Adım değeri
- `data`: Varsayılan değer