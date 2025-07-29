<?php

namespace Netliva\SymfonyFormBundle\Services;


use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CollectionContactExtension extends AbstractExtension
{
	private $container;
	private $environment;

	public function __construct (ContainerInterface $container, Environment $environment)
	{
		$this->container = $container;
		$this->environment = $environment;
	}

	public function getName() {
        return 'netliva_collection_contact_ext';
    }

    public function getFilters(): array {
        return array(
            new TwigFilter('get_a_mobile_or_phone', $this->getAMobileOrPhone(...)),
            new TwigFilter('get_a_mobile', $this->getAMobile(...)),
            new TwigFilter('get_a_phone', $this->getAPhone(...)),
            new TwigFilter('get_a_fax', $this->getAFax(...)),
            new TwigFilter('get_a_mail', $this->getAMail(...)),

            new TwigFilter('get_phones', $this->getPhones(...)),
            new TwigFilter('get_mobiles', $this->getMobiles(...)),
            new TwigFilter('get_all_phones', $this->getAllPhones(...)),
            new TwigFilter('get_faxes', $this->getFaxes(...)),
            new TwigFilter('get_mails', $this->getMails(...)),

            new TwigFilter('contact_list', $this->getList(...), ["is_safe"=>["html"]]),
        );
    }

	public function getList ($contacts, $type=null, $options=[])
	{
        $contactOptions = [];
        foreach ($this->container->getParameter('netliva_form.contact_options') as $contactOption)
            $contactOptions[$contactOption['key']] = $contactOption['value'];

        $options = array_merge([
               'mode'   => "show",
               'column' => 2,
               'border' => true,
           ], $options);

		return $this->environment->render('@NetlivaSymfonyForm/contact.html.twig', [
            'contactOptions' => $contactOptions,
            'contacts'       => $contacts,
            'onlyShow'       => $type,
            'options'        => $options,
		]);
    }

	public function getPhones ($contacts, $asArray=false)
	{
		return $this->_getContact("phone", $contacts, $asArray);
    }

	public function getMobiles ($contacts, $asArray=false)
	{
		return $this->_getContact("gsm", $contacts, $asArray);
    }

	public function getAllPhones ($contacts, $asArray=false)
	{
		return $this->_getContact("all", $contacts, $asArray);
    }

	public function getFaxes ($contacts, $asArray=false)
	{
		return $this->_getContact("fax", $contacts, $asArray);
    }

	public function getMails ($contacts, $asArray=false)
	{
		return $this->_getContact("email", $contacts, $asArray);
    }

	public function getAPhone ($contacts)
	{
		$arr = $this->_getContact("phone", $contacts, true);
		return count($arr) ? $arr[0] :  "";
    }

	public function getAMobile ($contacts)
	{
		$arr = $this->_getContact("gsm", $contacts, true);
		return count($arr) ? $arr[0] :  "";
    }

	public function getAMobileOrPhone ($contacts)
	{
		$arr = $this->_getContact("all", $contacts, true);
		return count($arr) ? $arr[0] :  "";
    }

	public function getAFax ($contacts)
	{
		$arr = $this->_getContact("fax", $contacts, true);
		return count($arr) ? $arr[0] :  "";
    }

	public function getAMail ($contacts)
	{
		$arr = $this->_getContact("email", $contacts, true);
		return count($arr) ? $arr[0] :  "";
    }


	private function _getContact ($type, ?array $contacts, $asArray)
	{
		$return = $asArray ? [] : '';
        if (is_null($contacts)) return $return;
		foreach ($contacts as $contact)
		{
			$cType = $contact[ "type" ];
			if (substr($cType,0,5) == "glob_")
				$cType = substr($cType, 5);
			if (
				($type == "all" && ($cType == "phone" || $cType == "gsm")) ||
				$cType == $type
			)
			{
				if (!$asArray && $return) $return .= ", ";
                if ($asArray === 'extend')
                {
                    $temp = $contact;
                }
                else
                {
                    $temp = $contact[ "content" ];
				    if ($cType == "phone" && $contact[ "internal" ]) $temp .= " (" . $contact[ "internal" ] . ")";
                }

				if ($asArray && $cType == "gsm") array_unshift($return, $temp);
				elseif ($asArray) $return[] = $temp;
				else $return .= $temp;
			}
		}

		return $return;
	}

}
