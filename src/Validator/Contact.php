<?php

namespace Netliva\SymfonyFormBundle\Validator;


use Symfony\Component\Validator\Constraint;


/**
 * @Annotation
 */

class Contact extends Constraint
{
	public $message = [
		'must_array' => 'İletişim bilgileri dizi değişken olarak gönderilmelidir',
	    'in_must_array' => '{{ order }}. sıradaki iletişim bilgisi dizi değişken olmalıdır',
	    'type_not_found' => '{{ order }}. sıradaki iletişim bilgisinde tip (type) bilgisi bulunamadı',
	    'not_found_content' => '{{ order }}. sıradaki iletişim bilgisinde içerik (content) bilgisi bulunamadı',
	    'type_mismatch' => '{{ order }}. sıradaki iletişim bilgisinde tip bilgisi tanınamadı. "gsm", "phone", "fax", "email", "glob_gsm" ve "glob_phone" değerlerinden biri gönderilmelidir',
	    'phone_content_mismatch' => '{{ order }}. sıradaki iletişim bilgisinde girilen telefon numarası geçersizdir. Beklenen Format : +90(000)000-0000',
	    'mobile_content_mismatch' => '{{ order }}. sıradaki iletişim bilgisinde girilen mobil telefon numarası geçersizdir. Beklenen Format : +90(500)000-0000, Girilen Değer : {{ value }}',
	    'mail_content_mismatch' => '{{ order }}. sıradaki iletişim bilgisinde girilen e-posta adresi geçersizdir',
	];

	public function validatedBy(): string
	{
		return \get_class($this).'Validator';
	}

}
