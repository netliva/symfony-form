# NetlivaAutocompleteType

Entity tabanlı autocomplete form tipi.

## Kullanım

```php
use Netliva\SymfonyFormBundle\Form\Types\NetlivaAutocompleteType;

$builder->add('fieldName', NetlivaAutocompleteType::class, [
    'entity_alias' => 'firm_search',
    'multiselect' => false,
    'multiselect_limit' => 5,
    'active_filter' => ['status' => 'active'],
    'label' => 'Firma Seçin'
]);
```

## Parametreler

- `entity_alias`: Konfigürasyonda tanımlı entity alias'ı
- `multiselect`: Çoklu seçim modu
- `multiselect_limit`: Maksimum seçim sayısı
- `active_filter`: Aktif filtreler

## Konfigürasyon

`config/packages/netliva_symfony_form.yaml` dosyasında entity'ler tanımlanmalıdır.