<?php
namespace Netliva\SymfonyFormBundle\Services;


use Netliva\TwigBundle\Twig\Extension\NetlivaExtension;
use Netliva\TwigBundle\Twig\Extension\SortByFieldExtension;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class NetlivaCustomFieldsExtension extends AbstractExtension
{
	private $container;
	/**
	 * @var SortByFieldExtension
	 */
	private $sorter;
	/**
	 * @var NetlivaExtension
	 */
	private $netext;

	public function __construct (
		ContainerInterface $container,
		SortByFieldExtension $sorter,
		NetlivaExtension $netext
	)
	{
		$this->container = $container;
		$this->sorter = $sorter;
		$this->netext = $netext;
	}

	public function getName() {
        return 'netliva_customfields_filters';
    }

    public function getFilters() {
        return array(
            new TwigFilter('fetch_fields', [$this, 'fetchFields'] , ['is_safe' => ['html']]),
            new TwigFilter('get_value', [$this, 'getValue'] , ['is_safe' => ['html']]),
            new TwigFilter('option_key_val', [$this, 'optionKeyVal'] , ['is_safe' => ['html']]),
        );
    }

    public function optionKeyVal($options)
	{
		$newOptions = [];
		foreach ($options as $option)
		{
			if(preg_match("/(.*)=>(.*)/",$option,$matches))
				$newOptions[trim($matches[1])] = trim($matches[2]);
			else
				$newOptions[trim($option)] = trim($option);
		}
		return $newOptions;
    }

    public function getValue($values, $field_info, $fieldKey)
	{
		if (key_exists($fieldKey, $values) && $values[$fieldKey])
		{
			if ($field_info["type"] == "date" or $field_info["type"] == "datetime")
			{
				if ( $values[$fieldKey] instanceof \DateTime)  $date_temp = $values[$fieldKey]["date"];
				else if (is_array($values[$fieldKey]) and key_exists('date', $values[$fieldKey]))  $date_temp = new \DateTime($values[$fieldKey]["date"]);
				else $date_temp = new \DateTime($values[$fieldKey]);

				return $this->netext->dateFormat($date_temp, $field_info["type"] == "datetime");
			}

			if ($field_info["type"] == "choice")
			{
				$options = $this->optionKeyVal($field_info["options"]);
				if (is_array($values[$fieldKey]))
				{
					$returnVal = "";
					foreach ($values[$fieldKey] as $value)
					{
						if ($returnVal) $returnVal .= ", ";
						$returnVal .= key_exists($value, $options) ? $options[$value] : $value;
					}
					return $returnVal;
				}
				return key_exists($values[$fieldKey], $options) ? $options[$values[$fieldKey]] : $values[$fieldKey];
			}

			if (is_array($values[$fieldKey]))
				return implode(", ", $values[$fieldKey]);

			return  $values[$fieldKey] . ((key_exists("suffix", $field_info) and $field_info["suffix"]) ? " ".$field_info["suffix"] : "");
		}

		return null;
    }

    public function fetchFields($values, $fields)
	{
		$html = '';
		if (is_array($fields) and key_exists("fields",$fields) and count($fields["fields"]))
		{
			foreach ($this->sorter->sortByFieldFilter($fields["fields"], 'order') as $field_key => $field_info)
			{
				$html .= '<li class="list-group-item labelled">';
				$html .= '<small class="badge badge-secondary badge-absolute"> '.$field_info["label"].'</small>';
				$html .= $this->getValue($values, $field_info, $field_key);
				$html .= '</li>';
			}
		}

		return $html;
    }
}
