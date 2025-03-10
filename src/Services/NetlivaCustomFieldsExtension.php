<?php
namespace Netliva\SymfonyFormBundle\Services;


use Netliva\FileTypeBundle\Form\Type\NetlivaFileType;
use Netliva\FileTypeBundle\Service\UploadHelperService;
use Netliva\SymfonyFormBundle\Form\Types\NetlivaDatePickerType;
use Netliva\TwigBundle\Twig\Extension\NetlivaExtension;
use Netliva\TwigBundle\Twig\Extension\SortByFieldExtension;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class NetlivaCustomFieldsExtension extends AbstractExtension
{
	private $container, $uploadHelperService;
	public function __construct (ContainerInterface $container, UploadHelperService $uploadHelperService)
	{
		$this->container = $container;
		$this->uploadHelperService = $uploadHelperService;
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

	public function dateFormat($content, $time=true)
	{
		if (!($content instanceof \DateTime)) return "";

		return $content->format("d.m.Y").($time?$content->format(" - H:i"):"");
	}

    public function getValue($values, $field_info, $fieldKey)
	{
        if (is_array($values) && key_exists($fieldKey, $values) && !is_null($values[$fieldKey]))
		{
			if ($field_info["type"] == "date" or $field_info["type"] == "datetime")
			{
				if ( $values[$fieldKey] instanceof \DateTime)  $date_temp = $values[$fieldKey]["date"];
				else if (is_array($values[$fieldKey]) and key_exists('date', $values[$fieldKey]))  $date_temp = new \DateTime($values[$fieldKey]["date"]);
				else $date_temp = new \DateTime($values[$fieldKey]);

				return $this->dateFormat($date_temp, $field_info["type"] == "datetime");
			}

			if ($field_info["type"] == "file")
			{
                return '<a href="'.$this->uploadHelperService->getFileUri($values[$fieldKey]).'" target="_blank">
						<i class="fa fa-external-link-alt"></i>
						'.$this->uploadHelperService->getFileName($values[$fieldKey]).'
					</a>';
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
			foreach ($this->sortByFieldFilter($fields["fields"], 'order') as $field_key => $field_info)
			{
				$html .= '<li class="list-group-item labelled">';
				$html .= '<small class="badge badge-secondary badge-absolute"> '.$field_info["label"].'</small>';
				$html .= $this->getValue($values, $field_info, $field_key);
				$html .= '</li>';
			}
		}

		return $html;
    }

	private function sortByFieldFilter($content, $sort_by = null, $direction = 'asc') {

		if (is_a($content, 'Doctrine\Common\Collections\Collection')) {
			$content = $content->toArray();
		}

		if (!is_array($content)) {
			throw new \InvalidArgumentException('Variable passed to the sortByField filter is not an array');
		} elseif (count($content) < 1) {
			return $content;
		} elseif ($sort_by === null) {
			throw new Exception('No sort by parameter passed to the sortByField filter');
		} else {
			// Unfortunately have to suppress warnings here due to __get function
			// causing usort to think that the array has been modified:
			// usort(): Array was modified by the user comparison function
			uasort($content, function ($a, $b) use ($sort_by, $direction) {
				$flip = ($direction === 'desc') ? -1 : 1;

				$a_sort_value = null;
				if (is_array($a) && key_exists($sort_by, $a))
					$a_sort_value = $a[$sort_by];
				else if (is_object($a) && method_exists($a, 'get' . ucfirst($sort_by)))
					$a_sort_value = $a->{'get' . ucfirst($sort_by)}();
				else if (is_object($b) && property_exists($a, $sort_by))
					$a_sort_value = $a->$sort_by;

				$b_sort_value = null;
				if (is_array($b) && key_exists($sort_by, $b))
					$b_sort_value = $b[$sort_by];
				else if (is_object($b) && method_exists($b, 'get' . ucfirst($sort_by)))
					$b_sort_value = $b->{'get' . ucfirst($sort_by)}();
				else if (is_object($b) && property_exists($b, $sort_by))
					$b_sort_value = $b->$sort_by;

				if ($a_sort_value == $b_sort_value) {
					return 0;
				} else if ($a_sort_value > $b_sort_value) {
					return (1 * $flip);
				} else {
					return (-1 * $flip);
				}
			});
		}
		return $content;
	}

    private $fieldTypes = [
        'text'     => ['class' => TextType::class, 'default_options' => []],
        'textarea' => ['class' => TextareaType::class, 'default_options' => [] ],
        'date'     => ['class' => NetlivaDatePickerType::class, 'default_options' => ['format'=>'date']],
        'datetime' => ['class' => NetlivaDatePickerType::class, 'default_options' => ['format'=>'full']],
        'file'     => ['class' => NetlivaFileType::class, 'default_options' => ['multiple' => false, 'bootstrap' => true, 'attr'=>['placeholder'=>'Belge Seçiniz']]],
        'choice'   => ['class' => ChoiceType::class, 'default_options' => []],
    ];

    public function prepareFieldOptions ($field)
    {
        $fieldOptions = [];
        if ($field['type'] == 'choice')
        {
            $choices = [];
            foreach ($field['options'] as $option)
            {
                $info = explode("=>", $option);
                if (count($info)>=2)
                    $choices[trim(array_shift($info))] = trim(implode("=>", $info));
                else
                    $choices[trim($option)] = trim($option);
            }

            $fieldOptions['multiple'] = $field['multiple'];
            $fieldOptions['expanded'] = $field['expanded'];
            $fieldOptions['choices'] = array_flip($choices);
        }
        elseif ($field['type'] == 'text')
        {
            $fieldOptions['attr'] = [];
            if (key_exists("inputType", $field))
            {
                if ($field["inputType"] == "number")
                    $fieldOptions['attr']['ncf-touch-spin'] = 'prepare';
                elseif ($field["inputType"] != "text")
                    $fieldOptions['attr']['ncf-mask-'.$field["inputType"]] = 'prepare';
            }

            if (key_exists("suffix", $field) && $field["suffix"])
                $fieldOptions['attr']['ncf-suffix'] = $field["suffix"];
        }

        return [
            $this->fieldTypes[$field['type']]['class'],
            array_merge(
                $this->fieldTypes[$field['type']]['default_options'],
                $fieldOptions,
                [
                    'label'    => $field['label'],
                    'required' => $field['required']
                ]
            )
        ];
    }
}
