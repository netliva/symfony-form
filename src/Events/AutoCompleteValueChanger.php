<?php
namespace Netliva\SymfonyFormBundle\Events;


use Netliva\CommentBundle\Entity\AuthorInterface;
use Symfony\Contracts\EventDispatcher\Event;

class AutoCompleteValueChanger extends Event
{
    /** @var array */
	private $data;
    /** @var string */
    private $alias;
    /** @var string */
    private $key;

	public function __construct (string $alias, string $key, array $data) {
		$this->data = $data;
        $this->alias = $alias;
        $this->key = $key;
	}

    /**
     * @return array
     */
    public function getData (): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData (array $data): void
    {
        $this->data = $data;
    }



    /**
     * @return string
     */
    public function getAlias (): string
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     */
    public function setAlias (string $alias): void
    {
        $this->alias = $alias;
    }

    /**
     * @return string
     */
    public function getKey (): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey (string $key): void
    {
        $this->key = $key;
    }


}
