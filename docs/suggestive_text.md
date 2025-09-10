# NetlivaSuggestiveTextType

Bu form tipi, text input alanında autocomplete özelliği sağlar ve kullanıcının yeni değerler eklemesine olanak tanır.

## Özellikler

- **Hibrit Autocomplete**: Hem statik dizi hem de veritabanından otomatik veri çekme
- **EntityType Benzeri**: Entity class ve field name ile otomatik veri çekme
- **Yeni Değer Ekleme**: Kullanıcı yeni değer girebilir (opsiyonel)
- **Hızlı Performans**: AJAX isteği yapmaz, form render sırasında veri çeker
- **Esnek Konfigürasyon**: Suggestions dizisi veya entity bilgileri ile kullanım
- **Responsive Tasarım**: Mobil ve desktop uyumlu
- **Dark Mode Desteği**: Karanlık tema desteği

## Kurulum

### 1. Form Type'ı Kaydet

`config/packages/netliva_suggestive_text.yaml` dosyası oluşturulmuştur.

### 2. JavaScript ve CSS'i Dahil Et

`monorepo/js-packages/symfony-form-js/index.js` dosyasına gerekli import'lar eklenmiştir.

### 3. Form Type'ı Kullan

Form type'ı doğrudan kullanılabilir.

## Kullanım

### Statik Suggestions ile Kullanım

```php
use Netliva\SymfonyFormBundle\Form\Types\NetlivaSuggestiveTextType;

$builder->add('fieldName', NetlivaSuggestiveTextType::class, [
    'suggestions' => ['Değer 1', 'Değer 2', 'Değer 3'],
    'label' => 'Field Label'
]);
```

### Veritabanından Otomatik Çekme ile Kullanım

```php
$builder->add('firmName', NetlivaSuggestiveTextType::class, [
    'entity_class' => 'App\Entity\Firms\Firms',
    'field_name' => 'name',
    'entity_manager' => 'default',
    'label' => 'Firma Adı'
]);
```

### Gelişmiş Konfigürasyon

```php
$builder->add('companyName', NetlivaSuggestiveTextType::class, [
    'suggestions' => [
        'Acme Corporation',
        'Tech Solutions Ltd.',
        'Global Industries Inc.',
        'Innovation Systems'
    ],
    'min_length' => 2,
    'max_suggestions' => 10,
    'allow_new_values' => true,
    'placeholder' => 'Şirket adı yazın...',
    'no_results_text' => 'Şirket bulunamadı',
    'label' => 'Şirket Adı'
]);
```

## Parametreler

| Parametre | Tip | Varsayılan | Açıklama |
|-----------|-----|------------|----------|
| `suggestions` | array\|null | `null` | Autocomplete için kullanılacak değerler dizisi (null ise veritabanından çeker) |
| `entity_class` | string\|null | `null` | Veritabanından veri çekilecek entity class'ı |
| `field_name` | string\|null | `null` | Autocomplete için kullanılacak field adı |
| `entity_manager` | string | `'default'` | Kullanılacak entity manager |
| `min_length` | int | `2` | Arama için minimum karakter sayısı |
| `max_suggestions` | int | `10` | Maksimum öneri sayısı |
| `allow_new_values` | bool | `true` | Yeni değer eklemeye izin ver |
| `placeholder` | string | `'Yazmaya başlayın...'` | Input placeholder metni |
| `no_results_text` | string | `'Sonuç bulunamadı'` | Sonuç bulunamadığında gösterilecek metin |

## JavaScript Events

Form tipi aşağıdaki custom event'leri tetikler:

```javascript
// Yeni değer eklendiğinde
$input.on('netliva:suggestive-text:new-value', function(event, value) {
    console.log('Yeni değer eklendi:', value);
    // Burada yeni değeri veritabanına ekleyebilirsiniz
});

// Mevcut değer seçildiğinde
$input.on('netliva:suggestive-text:existing-value', function(event, item) {
    console.log('Mevcut değer seçildi:', item);
});

// Seçim iptal edildiğinde
$input.on('netliva:suggestive-text:cancel', function() {
    console.log('Seçim iptal edildi');
});
```


## Örnek Kullanım

```php
// Form Type'da
class ExampleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Statik suggestions ile
            ->add('companyName', NetlivaSuggestiveTextType::class, [
                'suggestions' => [
                    'Acme Corporation',
                    'Tech Solutions Ltd.',
                    'Global Industries Inc.'
                ],
                'label' => 'Şirket Adı (Statik)',
                'placeholder' => 'Şirket adı yazın...'
            ])
            // Veritabanından otomatik çekme ile
            ->add('firmName', NetlivaSuggestiveTextType::class, [
                'entity_class' => 'App\Entity\Firms\Firms',
                'field_name' => 'name',
                'entity_manager' => 'default',
                'label' => 'Firma Adı (Veritabanından)',
                'placeholder' => 'Firma adı yazın...'
            ]);
    }
}
```

```twig
{# Template'de #}
{{ form_start(form) }}
    {{ form_row(form.companyName) }}
    <button type="submit" class="btn btn-primary">Kaydet</button>
{{ form_end(form) }}
```

## Güvenlik

- Tüm input'lar validate edilir
- SQL injection koruması vardır
- Entity class'ları kontrol edilir
- Hata durumlarında güvenli yanıtlar döndürülür

## Performans

- Veritabanı sorguları optimize edilmiştir
- Caching mekanizması kullanılabilir
- Lazy loading desteklenir
- Responsive tasarım ile mobil performansı optimize edilmiştir

## Sorun Giderme

### Form Type Bulunamıyor Hatası

```bash
# Cache'i temizle
docker exec -it livawork_php sh -c "cd /srv/app/Symfony && php bin/console cache:clear"
```

### JavaScript Çalışmıyor

1. `suggestive_text.js` dosyasının yüklendiğinden emin olun
2. Browser console'da hata mesajlarını kontrol edin
3. jQuery ve typeahead kütüphanelerinin yüklü olduğundan emin olun

### Autocomplete Çalışmıyor

1. Controller endpoint'lerinin çalıştığını kontrol edin
2. Entity class ve field name'in doğru olduğundan emin olun
3. Veritabanı bağlantısını kontrol edin

## Geliştirme

### Yeni Özellik Ekleme

1. Form type'ı güncelleyin
2. JavaScript komponenti güncelleyin
3. CSS stillerini güncelleyin
4. Controller endpoint'lerini güncelleyin
5. Test edin

### Test Etme

```bash
# Unit testler
docker exec -it livawork_php sh -c "cd /srv/app/Symfony && php bin/phpunit"

# Functional testler
docker exec -it livawork_php sh -c "cd /srv/app/Symfony && php bin/console doctrine:schema:validate"
```
