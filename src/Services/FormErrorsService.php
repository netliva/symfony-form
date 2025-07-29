<?php

namespace Netliva\SymfonyFormBundle\Services;


use Symfony\Component\Form\FormInterface;

class FormErrorsService
{
	private $errors = [];

	public function getFormErrors(FormInterface $form)
	{
		$this->_getFormErrors($form, null);
		return $this->errors;
	}

	private function _getFormErrors(FormInterface $form, $parentName)
	{
		foreach ($form->getErrors() as $error)
		{
			$this->errors[($parentName?$parentName."_":"").$form->getName()][] = $error->getMessage();
		}
		if (count($form->all()))
		{
			foreach ($form->all() as $child)
			{
				$this->_getFormErrors ($child, ($parentName?$parentName."_":"").$form->getName());
			}
		}
	}
}
