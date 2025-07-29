<?php
namespace Netliva\SymfonyFormBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TreeSelectController extends AbstractController
{

    public function __construct (
        private readonly EntityManagerInterface $entityManager,
    ) { }

    public function getOptionsAction(Request $req, $entity_alias, $val)
	{
		$entities = $this->getParameter('netliva_form.treeselect_entities');
		$entity_inf = $entities[$entity_alias];

		$postWhere = $req->request->get("where");
		if (!is_array($postWhere)) $postWhere = [];

		$val = $val == "null" ? null : $val;
		$isTreeView = array_key_exists("where", $entity_inf);

		if ($entity_inf['role'] !== 'IS_AUTHENTICATED_ANONYMOUSLY'){
			if (false === $this->isGranted( $entity_inf['role'] )) {
				throw new AccessDeniedException();
			}
		}
		// $maxRows = 75;

		$queryText = 'SELECT e.' . $entity_inf['value'] . ', e.' . $entity_inf['key'] . ' ' .
			($entity_inf['value2'] ? ', e.' . $entity_inf['value2'] : '').
			(count($entity_inf['other_values']) ? ', e.'.implode(", e.", $entity_inf['other_values']).' ':' ').
			'FROM ' . $entity_inf['class'] . ' e ';
		if (count($postWhere) || $isTreeView)
		{
			$queryText .= 'WHERE ';
			if ($isTreeView) $queryText .=	'e.' . $entity_inf['where'] . ($val ? ' = :val ' : ' IS NULL ');
			if (count($postWhere) && $isTreeView) $queryText .= ' AND ';
			$say = 0;
			foreach ($postWhere as $k=>$v)
			{
				if ($say) $queryText .= ' AND ';
				$queryText .= 'e.' . $k . " = :val_".$k." ";
				$say++;
			}
		}
		$queryText .= 'ORDER BY e.' . $entity_inf['value'];
		$query = $this->entityManager->createQuery($queryText);

		foreach ($postWhere as $k=>$v)
			if ($v) $query->setParameter('val_'.$k, $v );
		if ($isTreeView && $val) $query->setParameter('val', $val );

		$results = $query->getScalarResult();


		/** /		dump($results);
		dump($query->getSql());
		dump($query->getDql());
		dump($query->getParameters());
		die;

		/**/

		$res = "";
		if ($entity_inf['static_only_show_with_results'] && count($results) || !$entity_inf['static_only_show_with_results']) // kayıt olup olmamasının kontrolü, bağlı bulunduğu
		{
			foreach ($entity_inf['static_values'] AS $key => $value){
				$res .= '<option value="'.$key.'">'.$value.'</option>';
			}
		}
		foreach ($results AS $r){
			$res .= '<option value="'.$r[$entity_inf['key']].'"';
			foreach ($entity_inf['other_values'] as $value) $res .= ' data-'.$value.'="'.$r[$value].'"';
			$res .= '>'.$r[$entity_inf['value']].($entity_inf['value2']?' - '.$r[$entity_inf['value2']] :'').'</option>';
		}

		if ($isTreeView && count($results)) $selectedId = null;
		else $selectedId = $val;

		return new Response(json_encode(["selectedId" =>  $selectedId, "options" => $res]));
	}
}
