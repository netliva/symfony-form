<?php
namespace Netliva\SymfonyFormBundle\Events;


final class NetlivaSymfonyFormEvents
{
	/**
	 * Otomatik tamamlama sırasında bulunan değerler üzerinde düzenleme yapmayı sağlar
	 *
	 * @Event(Netliva\SymfonyFormBundle\Events\AutoCompleteValueChanger")
	 */
	const  AUTO_COMPLETE_DATA_CHANGER = 'netliva_symfony_form.auto_complete.data_changer';
}
