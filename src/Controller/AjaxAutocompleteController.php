<?php
namespace Netliva\SymfonyFormBundle\Controller;
use Doctrine\Persistence\ManagerRegistry;
use Netliva\SymfonyFormBundle\Events\AutoCompleteValueChanger;
use Netliva\SymfonyFormBundle\Events\NetlivaSymfonyFormEvents;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AjaxAutocompleteController extends AbstractController
{

    public function __construct (
        private readonly ManagerRegistry $managerRegistry,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) { }

    public function getJSONAction(Request $request, $entity_alias)
	{
		$letters = $request->request->get("letters");
		$conf_key = $request->request->get("key");
		$extras = $request->request->get("extras");

        $entities = $this->getParameter('netliva_form.autocomplete_entities');
		$configs = $entities[$entity_alias][$conf_key];

		$em = $this->managerRegistry->getManager($configs["em"]);
		if ($configs['role'] !== 'IS_AUTHENTICATED_ANONYMOUSLY'){
			if (false === $this->isGranted( $configs['role'] )) {
				throw new AccessDeniedException();
			}
		}
		$maxRows = 75;
		$properties = $configs['value'];
		$where_clause = [];
		$like = [];


		$select = $this->addPrefix($configs['key']) . " " . $this->alias($configs['key']);

		$this->prepareParts($select, $where_clause, $like, $properties, $configs, $letters, $order);
		if ($configs['search_in_other_values'])
			$this->prepareParts($select, $where_clause, $like, $configs['other_values'], $configs, $letters);
		else
		{
			foreach ($configs['other_values'] as $key => $val)
				$select .= ', '.$this->addPrefix($val, $key) . " " . $this->alias($val, $key);
		}

		$and_where = [];
		if (is_array($configs['filters']) && count($configs['filters']))
			$and_where = $configs['filters'];

		if (is_array($extras) && array_key_exists('activeFilter', $extras) and is_array($extras['activeFilter']))
			$and_where = array_merge($and_where, $extras['activeFilter']);

		$join_dql = '';
		if (array_key_exists('join', $configs) && $configs['join'])
		{
			$table_name = 'a';
			foreach ($configs['join'] as $join)
			{
				$join_dql .= ' JOIN '.$join.' '.++$table_name.' ';
			}
		}

		$query = 'SELECT '.$select.
			' FROM ' . $configs['class'] . ' a ' . $join_dql .
			' WHERE (' . implode(" OR ", $where_clause) . ')'.
			(count($and_where) ? ' AND (' . implode(") AND (", $and_where) . ')' : '') .
			" ORDER BY " .$order. $this->addPrefix($properties[0]);

		$results = $em->createQuery($query)
			->setParameters($like)
			->setMaxResults($maxRows)
			->getScalarResult();


		$res = array();
		foreach ($results AS $r)
		{
			$temp = [];
			foreach ($properties as $key => $value) $temp[] = $r[$this->alias($value)];

			$temp = array(
				"key"	=> $r[$this->alias($configs['key'])],
				"value" => $configs["format"] ? $this->printf_array($configs["format"], $temp) : implode(" - ",$temp)
			);
			foreach ($configs['other_values'] as $key => $value)
				$temp[$this->alias($value, $key)] = $r[$this->alias($value, $key)];

            $event = new AutoCompleteValueChanger($entity_alias, $conf_key, $temp);
            $this->eventDispatcher->dispatch($event, NetlivaSymfonyFormEvents::AUTO_COMPLETE_DATA_CHANGER);

			$res[$temp["key"]] = $event->getData();
		}

		$response = ["status"=> true, "error"=>null, "data" => array_values($res)];
		return new Response(json_encode($response));
	}

	public function callBackAction($id, $entity_alias)
	{
		$entities = $this->getParameter('netliva_form.autocomplete_entities');
		$entity_inf = $entities[$entity_alias];
		$res = array();
		if (count($entity_inf) == 1)
		{
            $conf_key = array_key_first($entity_inf);
            $configs = $entity_inf[$conf_key];
            $em = $this->managerRegistry->getManager($configs["em"]);

            $select = $this->addPrefix($configs['key']) . " " . $this->alias($configs['key']);
            foreach ($configs['value'] as $key => $property)
            {
                $withPro = $this->addPrefix($property, $key);
                if ($this->alias($configs['key']) != $this->alias($property))
                    $select .= ', '.$withPro." ".$this->alias($property);
            }
            foreach ($configs['other_values'] as $key => $val)
                $select .= ', '.$this->addPrefix($val, $key) . " " . $this->alias($val, $key);


            $join_dql = '';
            if (array_key_exists('join', $configs) && $configs['join'])
            {
                $table_name = 'a';
                foreach ($configs['join'] as $join)
                {
                    $join_dql .= ' JOIN '.$join.' '.++$table_name.' ';
                }
            }

            $query = 'SELECT '.$select.
                ' FROM ' . $configs['class'] . ' a ' . $join_dql .
            'WHERE a.' . $configs['key'] . ' = :id ';

            $results = $em->createQuery($query)
				->setParameter('id', $id)
				->getOneOrNullResult();
			if ($results)
			{
                $res = [];
                foreach ($configs['value'] as $key => $value) $res[] = $results[$this->alias($value)];

                $res = array(
                    "key"	=> $results[$this->alias($configs['key'])],
                    "value" => $configs["format"] ? $this->printf_array($configs["format"], $res) : implode(" - ",$res)
                );
                foreach ($configs['other_values'] as $key => $value)
                    $res[$this->alias($value, $key)] = $results[$this->alias($value, $key)];

                $event = new AutoCompleteValueChanger($entity_alias, $conf_key, $res);
                $this->eventDispatcher->dispatch($event, NetlivaSymfonyFormEvents::AUTO_COMPLETE_DATA_CHANGER);

                $res = $event->getData();
            }
		}

		return new Response(json_encode($res));

	}

	private function addPrefix ($prop, $key=null)
	{
		if ($key && !is_numeric($key)) $prop = $key;
		return (preg_match("/[a-z]{1}\..*/",$prop)?"":"a.").$prop;
	}

	private function alias ($prop, $key = null)
	{
		if ($key && !is_numeric($key))
			return $prop;

		if (preg_match("/([a-z]){1}\.(.*)/",$prop, $maches))
			$prop = $maches[1]."_".$maches[2];
		return $prop;
	}

	private function clearPrefix ($prop)
	{
		if (preg_match("/[a-z]{1}\.(.*)/",$prop, $maches))
			$prop = $maches[1];
		return $prop;
	}

	private function printf_array ($format, $arr)
	{
		return call_user_func_array('sprintf', array_merge((array)$format, $arr));
	}

	protected function prepareParts (string &$select, array &$where_clause, array &$like, array $properties, array $configs, string $letters, &$order = null): void
	{
		foreach ($properties as $key1 => $property)
		{
			$withPro = $this->addPrefix($property, $key1);
			$param   = $this->clearPrefix($property).'_like';

			if ($this->alias($configs['key']) != $this->alias($property))
				$select .= ', '.$withPro." ".$this->alias($property);

			switch ($configs['search'])
			{
				case "begins_with":
					$where_clause[] = $withPro.' LIKE :'.$param;
					$like[$param]   = $letters.'%';
					break;
				case "ends_with":
					$where_clause[] = $withPro.' LIKE :'.$param;
					$like[$param]   = '%'.$letters;
					break;
				case "contains":
					$where_clause[] = $withPro.' LIKE :'.$param;
					$like[$param]   = '%'.$letters.'%';
					break;
				case "explode_space_or":
				case "explode_comma_or":
				case "explode_space":
				case "explode_comma":
					if (!is_array($letters))
					{
						$delimiter = (substr($configs['search'], 8, 5) == 'comma' ? "," : " ");
						$letters   = explode($delimiter, $letters);
					}
					$where_sub_clause = [];
					$say = 0;
					foreach ($letters as $key => $word)
					{
						if (trim($word))
						{
							$where = $withPro.' LIKE :'.$param.'_'.$key;
							$like[$param.'_'.$key] = '%'.trim($word).'%';

							if (!$say) // ilk kelimeyi, en başta da ara
							{
								$where = "(" . $where . ' OR ' . $withPro . ' LIKE :' . $param . '_' . $key . '_first)';
								$like[$param.'_'.$key.'_first'] = trim($word).'%';
								if (is_null($order))
								{
									$order = "IF (INSTR($withPro, '".trim($word)."') = 1, 'a','b'), ";
								}
							}
							elseif ($say == count($letters)-1) // son kelimeyi en sonda da ara
							{
								$where = "(" . $where . ' OR ' . $withPro . ' LIKE :' . $param . '_' . $key . '_last)';
								$like[$param.'_'.$key.'_last'] = '%'.trim($word);
							}

							$where_sub_clause[] = $where;
						}
						$say++;
					}

					$glue = str_ends_with($configs['search'], 'or') ? " OR " : " AND ";

					$where_clause[] = "(".implode($glue, $where_sub_clause).")";
					break;

				default:
					throw new \Exception('"search" parametresi belirtilmemiş.');
			}
		}

	}


}
