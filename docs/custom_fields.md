# NetlivaCustomFieldsType

Özel alanlar için dinamik form tipi.

## Kullanım

```php
use Netliva\SymfonyFormBundle\Form\Types\NetlivaCustomFieldsType;

$builder->add('customFields', NetlivaCustomFieldsType::class, [
    'label' => 'Özel Alanlar',
    'fields' => [
        'text_field' => ['type' => 'text', 'label' => 'Metin Alanı'],
        'number_field' => ['type' => 'number', 'label' => 'Sayı Alanı'],
        'select_field' => ['type' => 'select', 'label' => 'Seçim', 'options' => ['A', 'B', 'C']]
    ]
]);
```

## Özellikler

- Dinamik alan oluşturma
- Farklı input tipleri
- Validasyon desteği
- JSON formatında veri saklama

## Parametreler

- `fields`: Alan konfigürasyonları
- `label`: Form etiketi