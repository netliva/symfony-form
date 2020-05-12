# DEPENDENT ENTITY

Bir entity type'ı başka bir ChoiceType veya EntityType'a bağımlı olarak güncellenmesini isterseniz aşağıdaki DependentType'ı kullanabilirsiniz.


webpack yardımıyla gerekli olan dosyaları projeye dahil edebilirsiniz;

Yalnızca bu form tipini kullanacaksanız, aşağıdaki gibi yalnız bu js dosyasını projenize dahil edin.
```javascript
// assets/js/app.js
import '@netliva/symfony-form/src/js/dependent'
```
Yada diğer form tiplerini de kullanacaksanız,
```javascript
// assets/js/app.js
import '@netliva/symfony-form'
```
Şeklinde tek seferde, tüm js kütüphanesini projenize dahil edebilirsiniz.



Route tanımlamasını yapınız

```yaml
# ./config/routes.yaml
netliva_form_route:
  resource: '@NetlivaSymfonyFormBundle/Resources/config/routing.yaml'
  prefix: /

```

## Kullanım

Aşağıdaki gibi bağımlılık tanımlanabilir. Burada Kullanıcılar, bir üsteki gruplara göre bağlı olarak listelenmektedir. Gruplar Choice olabilieceği gibi veritabanından gelen EntityType'ta oalbilir
```php
<?php
//...
public function buildForm (FormBuilderInterface $formBuilder, array $options)
{
	//...
	$formBuilder			
        ->add('user_group', ChoiceType::class, [
            'label' => 'Kullanıcı Grupları',
            'choices' => [
                'Grup 1' => '1',
                'Grup 2' => '2',
                'Grup 3' => '3',
                'Grup 4' => '4',
            ],
        ])
        ->add(
            'users', 
            \Netliva\SymfonyFormBundle\Form\Types\NetlivaDependentType::class, 
            [
                "label" => "Kullanıcılar",
                'entity_alias' => 'user', // Config dosyasına eklenecek bilgiler ile ilgili tanımlama
                'depend_to' => 'user_group', // bu değerin hangi dieğere bağlı olarak güncelleneceğinin tanımı, bu örnekte üstteki kullanıcı gruplarına bağlı
                'transformer' => false // Veritabanına kaydolacak değer formadan gelenID ise false olmalı, true olduğunda değer Entity'ye çevrilir.
            ]
        );

    //...
}
//...
?>
```

Yukarıda eklenen NetlivaDependentType'ta belirtilen entity_alias tanımı config dosyasında tanımlanır.
Symfony 4 öncesi `./app/config/config.yml` yeni versinyonlarda `./config/packages`klasörü altında `netliva_form.yaml`
 adlı dosya oluşturarak içine aşağıdaki örnek kodlara göre ekleme yapın.
```yaml
# ...
netliva_symfony_form:
  dependent_entities: # tüm tanımlamalarınız ayrı ayrı dependent_entities altında tanımlanacaktır  
    # ...
    user:
      class: App\Entity\User
      where: group
      value: [ name ] # listede görünecekbilgiler
      # aşağıdaki değerler opsiyoneldir ihtiyaca göre tanımlayın
      static_only_show_with_results: true # statik değerlerin bağımlı gelen sonuç ile birlikte gösterilmesi 
      static_values: # burda belirtilen her bir değer, bağımlı gelen öğelerle birlikte listelenir 
         0: ---- SEÇİLMEDİ ----        

      em: ~ # varsayılan entity manager dışında bir tanım kullanmak için
      other_values: [ username ] # burda belirtilen değerler, listede data olarak listelenir <option data-username="value"></option> 
    # ...
# ...
```
 
Yuarıdaki örnekteki tanımlama, kullanıcıyı guruplarına göre arama yaparak listelemeye yönelik bir örnektir. Listede kullanıcı isimleri gözükürken, data olarak da kullanıcıadları eklenir. Listenin en üstünde ise ---SEÇİLMEDİ--- bilgisi yer alır.

#### JOIN DEPENDENT

Belirtilen class'ta group alanında arama yapmak için bu yöntem kullanılırken, join olan diğer tablolarda arama yapmakta mümkündür.

Bunun için farklı bir örnek kurgulayabiliriz; Diyelim ki kitap listesi oluşturacaksınız. Kitaplar kitaplıklara onlarda kütüphanelere bağlı ve siz kütüphanelere bağlı olarak kütüphanedeki tüm kitapların listelenmesini istiyorsunuz. O durumda yapımız şu şekilde olacak;


 ```php
<?php
 //...
 public function buildForm (FormBuilderInterface $formBuilder, array $options)
 {
 	//...
 	$formBuilder			
         ->add('library', EntityType::class, [
             'label' => 'Kütüphane Seçiniz',
             'class' => Library::class, 
             'property' => 'name'
             #...
         ])
         ->add(
             'books', 
             \Netliva\SymfonyFormBundle\Form\Types\NetlivaDependentType::class, 
             [
                 "label" => "Kitaplar",
                 'entity_alias' => 'book_alias', 
                 'depend_to' => 'library', 
                 'transformer' => true 
             ]
         );
 
     //...
 }
 //...
 ?>
 ```

Config dosyamızda join tanımlaması şu şekilde yapılacak

```yaml
# ...
netliva_symfony_form:
  dependent_entities: # tüm tanımlamalarınız ayrı ayrı dependent_entities altında tanımlanacaktır  
    # ...
    book_alias:
      class: App\Entity\Books
      value: [ a.book_name, b.shelf_no ] # listede "Kitap adı - 112" şeklinde kirap adları ve kitaplık numaraları listelenir 
      other_values: [ b.id ] # data olarak kitaplık ID'si gönderilir
      where: c.id
      join:
          - a.bookshelf
          - b.library
   # ...
# ...
```

Bu noktada tablo harflendirmelerine dikkat edilmelidir. class'ta belirtilen ana tablonun tanımı a harfidir. Daha sonra sırayla join edilen her bir tablo b,c,d,e.. şeklinde tnaımlar almaya devam eder.
Eğer join kulanıyorsanız, wherei value, other_values alanlarında tanımlamalarınızı tablo ekleriyle yapmanız karışıklığı önleyecektir. 
