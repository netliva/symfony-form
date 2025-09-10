# NetlivaTreeSelectType

Hiyerarşik ağaç yapısı için seçim form tipi.

## Kullanım

```php
use Netliva\SymfonyFormBundle\Form\Types\NetlivaTreeSelectType;

$builder->add('category', NetlivaTreeSelectType::class, [
    'entity_alias' => 'department',
    'label' => 'Kategori Seçin',
    'multiple' => false
]);
```

## Özellikler

- Hiyerarşik veri yapısı
- Ağaç görünümü
- Çoklu seçim desteği
- Arama özelliği

## Parametreler

- `entity_alias`: Konfigürasyonda tanımlı entity alias'ı
- `multiple`: Çoklu seçim modu
- `label`: Form etiketi

## Konfigürasyon

`config/packages/netliva_symfony_form.yaml` dosyasında tree select entity'leri tanımlanmalıdır.