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

    public function getFilters() {
        return array(
            new TwigFilter('get_a_mobile_or_phone', [$this, 'getAMobileOrPhone']),
            new TwigFilter('get_a_mobile', [$this, 'getAMobile']),
            new TwigFilter('get_a_phone', [$this, 'getAPhone']),
            new TwigFilter('get_a_fax', [$this, 'getAFax']),
            new TwigFilter('get_a_mail', [$this, 'getAMail']),

            new TwigFilter('get_phones', [$this, 'getPhones']),
            new TwigFilter('get_mobiles', [$this, 'getMobiles']),
            new TwigFilter('get_all_phones', [$this, 'getAllPhones']),
            new TwigFilter('get_faxes', [$this, 'getFaxes']),
            new TwigFilter('get_mails', [$this, 'getMails']),

            new TwigFilter('contact_list', [$this, 'getList'], ["is_safe"=>["html"]]),
        );
    }

	public function getList ($contacts, $type=null)
	{
		return $this->environment->render('@NetlivaSymfonyForm/contact.html.twig', [
			'contacts' => $contacts,
			'onlyShow' => $type,
		]);
    }

	public function getPhones ($contacts, $asArray=false)
	{
		return $this->_getPhone("phone", $contacts, $asArray);
    }

	public function getMobiles ($contacts, $asArray=false)
	{
		return $this->_getPhone("gsm", $contacts, $asArray);
    }

	public function getAllPhones ($contacts, $asArray=false)
	{
		return $this->_getPhone("all", $contacts, $asArray);
    }

	public function getFaxes ($contacts, $asArray=false)
	{
		return $this->_getPhone("fax", $contacts, $asArray);
    }

	public function getMails ($contacts, $asArray=false)
	{
		return $this->_getPhone("email", $contacts, $asArray);
    }

	public function getAPhone ($contacts)
	{
		$arr = $this->_getPhone("phone", $contacts, true);
		return count($arr) ? $arr[0] :  "";
    }

	public function getAMobile ($contacts)
	{
		$arr = $this->_getPhone("gsm", $contacts, true);
		return count($arr) ? $arr[0] :  "";
    }

	public function getAMobileOrPhone ($contacts)
	{
		$arr = $this->_getPhone("all", $contacts, true);
		return count($arr) ? $arr[0] :  "";
    }

	public function getAFax ($contacts)
	{
		$arr = $this->_getPhone("fax", $contacts, true);
		return count($arr) ? $arr[0] :  "";
    }

	public function getAMail ($contacts)
	{
		$arr = $this->_getPhone("email", $contacts, true);
		return count($arr) ? $arr[0] :  "";
    }


	private function _getPhone ($type, array $contacts, bool $asArray)
	{
		$return = $asArray ? [] : '';
		foreach ($contacts as $contact)
		{
			if (
				($type == "all" && ($contact[ "type" ] == "phone" || $contact[ "type" ] == "gsm")) ||
				$contact[ "type" ] == $type
			)
			{
				if (!$asArray && $return) $return .= ", ";
				$temp = $contact[ "content" ];
				if ($contact[ "type" ] == "phone" && $contact[ "internal" ]) $temp .= " (" . $contact[ "internal" ] . ")";

				if ($asArray && $contact[ "type" ] == "gsm") array_unshift($return, $temp);
				elseif ($asArray) $return[] = $temp;
				else $return .= $temp;
			}
		}

		return $return;
	}

}
