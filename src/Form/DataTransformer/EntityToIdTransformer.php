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

	public function __construct(EntityManagerInterface $manager, $entityInfo)
	{
		$this->manager    = $manager;
		$this->entityInfo = $entityInfo;
	}

	/**
	 * Transforms an object (entity) to a string (number).
	 *
	 * @param  Object|null $entity
	 * @return string
	 */
	public function transform($entity)
	{
		if (null === $entity)
		{
			return '';
		}

		if ($entity instanceof ArrayCollection || $entity instanceof PersistentCollection)
		{
			$collection = [];
			foreach ($entity as $item)
			{
				$value = [];
				foreach ($this->entityInfo['value'] as $val_key)
				{
					$value[] = $item->{'get'.ucfirst($val_key)}();
				}
				$collection[] = [
					"matchedKey" => "value",
					"key"   => $item->{'get'.ucfirst($this->entityInfo['key'])}(),
					"value" => implode(" - ", $value),
				];
			}

			return json_encode($collection);
		}

		return is_object($entity) ? $entity->{'get'.ucfirst($this->entityInfo['key'])}() : $entity ;
	}

	private function isJson($string) {
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE && !is_numeric($string));
	}

	/**
	 * Transforms a string (number) to an object (issue).
	 *
	 * @param  string $issueNumber
	 * @return Object|null
	 * @throws TransformationFailedException if object (issue) is not found.
	 */
	public function reverseTransform($id)
	{
		// no issue number? It's optional, so that's ok
		if (!$id || (is_array($id) and !count($id)))
		{
			return null;
		}

		if ($this->isJson($id))
		{
			$ids = [];
			// dump($id);
			foreach (json_decode($id) as $value)
			{
				$ids[] = $value->key;
			}

			if (!count($ids)) return null;

			$qb = $this->manager->getRepository($this->entityInfo['class'])->createQueryBuilder("e");
			$qb->where($qb->expr()->in("e.".$this->entityInfo['key'], $ids));
			$entity = $qb->getQuery()->getResult();
		}
		else
		{
			$entity = $this->manager->getRepository($this->entityInfo['class'])// query for the issue with this id
			->findOneBy([$this->entityInfo['key']=>$id]);
		}

		if (null === $entity)
		{
			throw new TransformationFailedException(sprintf('An issue with number "%s" does not exist!', $id));
		}

		return $entity;
	}
}
