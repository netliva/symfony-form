<?php

namespace Netliva\SymfonyFormBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\HttpFoundation\JsonResponse;

class EntityToIdTransformer implements DataTransformerInterface
{
	private $manager;
	private $entityInfo;
	private $isMultSelection;

	public function __construct(EntityManagerInterface $manager, $entityInfo, $isMultSelection = false)
	{
		$this->manager    = $manager;
		$this->entityInfo = $entityInfo;
		$this->isMultSelection = $isMultSelection;
	}


	public function transform(mixed $value): object|string
    {
		if (null === $value)
		{
			return '';
		}

		if ($value instanceof ArrayCollection || $value instanceof PersistentCollection)
		{
			$collection = [];
			foreach ($value as $item)
			{
				$tempVal = [];
				foreach ($this->entityInfo['value'] as $val_key)
				{
					$tempVal[] = $item->{'get'.ucfirst($val_key)}();
				}
				$collection[] = [
					"matchedKey" => "value",
					"key"   => $item->{'get'.ucfirst($this->entityInfo['key'])}(),
					"value" => implode(" - ", $tempVal),
				];
			}

			return json_encode($collection);
		}

		return is_object($value) ? $value->{'get'.ucfirst($this->entityInfo['key'])}() : $value ;
	}

	private function isJson($string): bool
    {
        return (json_validate($string) && !is_numeric($string));
	}


	public function reverseTransform(mixed $value): mixed
    {
		// no issue number? It's optional, so that's ok
		if (!$value || (is_array($value) and !count($value)))
		{
			return $this->isMultSelection ? [] : null;
		}

		if ($this->isJson($value))
		{
			$ids = [];
			// dump($id);
			foreach (json_decode($value) as $val)
			{
				$ids[] = $val->key;
			}

            if (!count($ids)) return $this->isMultSelection ? [] : null;

			$qb = $this->manager->getRepository($this->entityInfo['class'])->createQueryBuilder("e");
			$qb->where($qb->expr()->in("e.".$this->entityInfo['key'], $ids));
			$entity = $qb->getQuery()->getResult();
		}
		else
		{
			$entity = $this->manager->getRepository($this->entityInfo['class'])// query for the issue with this id
			->findOneBy([$this->entityInfo['key']=>$value]);
		}

		if (null === $entity)
		{
			throw new TransformationFailedException(sprintf('An issue with number "%s" does not exist!', $value));
		}

		return $entity;
	}
}
