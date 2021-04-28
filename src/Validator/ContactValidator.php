<?php

namespace Netliva\SymfonyFormBundle\Validator;

use App\Entity\Staff\Staff;
use Crm\ApiBundle\Classes\ApiResponse;
use Crm\ManagementBundle\Services\DoctrineHelper;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ContactValidator extends ConstraintValidator
{
	/**
	 * Checks if the passed value is valid.
	 *
	 * @param mixed      $value      The value that should be validated
	 * @param Constraint $constraint The constraint for the validation
	 */
	public function validate ($value, Constraint $constraint)
	{
		if (!$constraint instanceof Contact) {
			throw new UnexpectedTypeException($constraint, Contact::class);
		}

		if (null === $value || '' === $value) {
			return;
		}

		if (!is_array($value))
			$this->context->buildViolation($constraint->message['must_array'])->addViolation();


		foreach ($value as $cKey => $cValue)
		{
			$order = is_numeric($cKey) ? (int)$cKey + 1 : $cKey;

			if (!is_array($cValue))
			{
				$this->context->buildViolation($constraint->message['in_must_array'])->setParameter('{{ order }}', $order)->addViolation();
				break;
			}

			if (!key_exists('type', $cValue))
			{
				$this->context->buildViolation($constraint->message['type_not_found'])->setParameter('{{ order }}', $order)->addViolation();
				break;
			}

			if (!key_exists('content', $cValue))
			{
				$this->context->buildViolation($constraint->message['not_found_content'])->setParameter('{{ order }}', $order)->addViolation();
				break;
			}

			if (!in_array($cValue['type'], ['gsm', 'phone', 'fax', 'email', 'glob_gsm', 'glob_phone']))
			{
				$this->context->buildViolation($constraint->message['type_mismatch'])->setParameter('{{ order }}', $order)->addViolation();
				break;
			}

			if (in_array($cValue['type'], ['phone', 'fax']) && !preg_match('/^\+90\(\d{3}\)\d{3}-\d{4}/', trim($cValue['content'])))
			{
				$this->context->buildViolation($constraint->message['phone_content_mismatch'])->setParameter('{{ order }}', $order)->addViolation();
				break;
			}

			if ($cValue['type'] == 'gsm' && !preg_match('/^\+90\(5\d{2}\)\d{3}-\d{4}/', trim($cValue['content'])))
			{
				$this->context->buildViolation($constraint->message['mobile_content_mismatch'])->setParameter('{{ order }}', $order)->addViolation();
				break;
			}

			if ($cValue['type'] == 'email' && !filter_var(trim($cValue['content']), FILTER_VALIDATE_EMAIL))
			{
				$this->context->buildViolation($constraint->message['mail_content_mismatch'])->setParameter('{{ order }}', $order)->addViolation();
				break;
			}
		}

	}
}
