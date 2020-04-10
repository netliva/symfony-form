<?php
namespace Netliva\SymfonyFormBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AjaxAutocompleteController extends AbstractController
{

	public function getJSONAction(Request $request, $entity_alias)
	{
		$letters = $request->request->get("letters");
		$conf_key = $request->request->get("key");
		$entities = $this->get('service_container')->getParameter('netliva_form.autocomplete_entities');
		$configs = $entities[$entity_alias][$conf_key];

		$em = $this->get('doctrine')->getManager($configs["em"]);
		if ($configs['role'] !== 'IS_AUTHENTICATED_ANONYMOUSLY'){
			if (false === $this->get('security.token_storage')->isGranted( $configs['role'] )) {
			    throw new AccessDeniedException();
			}
		}
		$maxRows = 75;
		$properties = $configs['value'];
		$where_clause = [];
		$like = [];


		$select = $this->addPrefix($configs['key']) . " " . $this->alias($configs['key']);


		$this->prepareParts($select, $where_clause, $like, $properties, $configs, $letters);
		if ($configs['search_in_other_values'])
			$this->prepareParts($select, $where_clause, $like, $configs['other_values'], $configs, $letters);
		else
		{
			foreach ($configs['other_values'] as $val)
				$select .= ', '.$this->addPrefix($val) . " " . $this->alias($val);
		}

		$join_dql = '';
		if (key_exists('join', $configs) && $configs['join'])
		{
			$table_name = 'a';
			foreach ($configs['join'] as $join)
			{
				$join_dql .= ' JOIN '.$join.' '.++$table_name.' ';
			}
		}

		$query = 'SELECT '.$select.
			' FROM ' . $configs['class'] . ' a ' . $join_dql .
			' WHERE ' . implode(" OR ", $where_clause) .
			' ORDER BY ' . $this->addPrefix($properties[0]);

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
			foreach ($configs['other_values'] as $key => $value) $temp[$value] = $r[$value];
			$res[$temp["key"]] = $temp;
		}

		$response = ["status"=> true, "error"=>null, "data" => array_values($res)];
		return new Response(json_encode($response));
	}

	public function callBackAction($id, $entity_alias)
	{
		$entities = $this->get('service_container')->getParameter('netliva_form.autocomplete_entities');
		$entity_inf = $entities[$entity_alias];
		$res = array();
		if (count($entity_inf) == 1)
		{
			$entity_inf = current($entity_inf);
			$em = $this->get('doctrine')->getManager($entity_inf["em"]);

			$query = 'SELECT e.'. implode(', e.', $entity_inf['value']). ', e.' . $entity_inf['key'] .
				(count($entity_inf['other_values']) ? ', e.'.implode(", e.", $entity_inf['other_values']).' ':' ').
				'FROM ' . $entity_inf['class'] . ' e ' .
				'WHERE e.' . $entity_inf['key'] . ' = :id ';
			$results = $em->createQuery($query)
				->setParameter('id', $id )
				->getScalarResult();
			if (count($results))
			{
				$res = array(
					"key" => $results[0][$entity_inf['key']],
				);
				$values = [];
				foreach ($entity_inf['value'] as $key => $value) $values[] = $results[0][$value];
				$res["value"] = implode(" - ", $values);
				foreach ($entity_inf['other_values'] as $key => $value) $res[$value] = $results[0][$value];
			}
		}

		return new Response(json_encode($res));

	}

	private function addPrefix ($prop)
	{
		return (preg_match("/[a-z]{1}\..*/",$prop)?"":"a.").$prop;
	}

	private function alias ($prop)
	{
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

	/**
	 * @param string $select
	 * @param array  $where_clause
	 * @param array  $like
	 * @param        $properties
	 * @param        $configs
	 * @param        $letters
	 */
	protected function prepareParts (string &$select, array &$where_clause, array &$like, $properties, $configs, $letters): void
	{
		foreach ($properties as $property)
		{
			$withPro = $this->addPrefix($property);
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
				case "explode_space":
				case "explode_comma":
					$letters          = is_array($letters) ? $letters : explode(
						($configs['search'] == 'explode_comma' ? "," : " "),
						$letters
					);
					$where_sub_clause = [];
					foreach ($letters as $key => $word)
					{
						if (trim($word))
						{
							$where_sub_clause[]    = $withPro.' LIKE :'.$param.'_'.$key;
							$like[$param.'_'.$key] = '%'.trim($word).'%';
						}
					}
					$where_clause[] = "(".implode(" OR ", $where_sub_clause).")";
					break;
				default:
					throw new \Exception('"search" parametresi belirtilmemiş.');
			}
		}

	}


}
