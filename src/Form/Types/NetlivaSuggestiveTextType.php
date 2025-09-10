<?php

namespace Netliva\SymfonyFormBundle\Form\Types;

use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NetlivaSuggestiveTextType extends AbstractType
{
    public function __construct(
        private readonly ManagerRegistry $registry
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'suggestions' => null,
            'entity_class' => null,
            'field_name' => null,
            'entity_manager' => 'default',
            'min_length' => 2,
            'max_suggestions' => 10,
            'allow_new_values' => true,
            'placeholder' => '',
            'no_results_text' => 'Öneri bulunamadı',
            'compound' => false,
        ]);

        $resolver->setAllowedTypes('suggestions', ['null', 'array']);
        $resolver->setAllowedTypes('entity_class', ['null', 'string']);
        $resolver->setAllowedTypes('field_name', ['null', 'string']);
        $resolver->setAllowedTypes('entity_manager', ['string', EntityManager::class]);
        $resolver->setAllowedTypes('min_length', 'int');
        $resolver->setAllowedTypes('max_suggestions', 'int');
        $resolver->setAllowedTypes('allow_new_values', 'bool');
    }

    public function getParent(): ?string
    {
        return TextType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setAttribute('suggestions', $options['suggestions']);
        $builder->setAttribute('entity_class', $options['entity_class']);
        $builder->setAttribute('field_name', $options['field_name']);
        $builder->setAttribute('entity_manager', $options['entity_manager']);
        $builder->setAttribute('min_length', $options['min_length']);
        $builder->setAttribute('max_suggestions', $options['max_suggestions']);
        $builder->setAttribute('allow_new_values', $options['allow_new_values']);
        $builder->setAttribute('placeholder', $options['placeholder']);
        $builder->setAttribute('no_results_text', $options['no_results_text']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        // Eğer suggestions verilmemişse, veritabanından çek
        $suggestions = $options['suggestions'];
        if ($suggestions === null) {
            $class = $options['entity_class'];
            $fieldName = $options['field_name'];
            if (!$class)
                $class = $this->getDataClass($form->getParent());
            if (!$fieldName)
                $fieldName = $form->getConfig()->getName();

            if ($class && $fieldName)
            {
                $suggestions = $this->getSuggestionsFromDatabase(
                    $class,
                    $fieldName,
                    $options['entity_manager']
                );
            }

        }

        $view->vars['suggestions'] = $suggestions ?? [];
        $view->vars['entity_class'] = $options['entity_class'];
        $view->vars['field_name'] = $options['field_name'];
        $view->vars['entity_manager'] = $options['entity_manager'];
        $view->vars['min_length'] = $options['min_length'];
        $view->vars['max_suggestions'] = $options['max_suggestions'];
        $view->vars['allow_new_values'] = $options['allow_new_values'];
        $view->vars['placeholder'] = $options['placeholder'];
        $view->vars['no_results_text'] = $options['no_results_text'];
    }

    public function getBlockPrefix(): string
    {
        return 'netliva_suggestive_text';
    }

    private function getDataClass(FormInterface $form): string
    {
        $class = $form->getConfig()->getDataClass();
        if (!$class)
            return $this->getDataClass($form->getParent());
        return $class;
    }


    /**
     * Veritabanından unique değerleri çeker
     */
    private function getSuggestionsFromDatabase(string $entityClass, string $fieldName, $entityManager): array
    {
        try {
            // Entity manager'ı al
            if ($entityManager instanceof EntityManager) {
                $em = $entityManager;
            } else {
                $em = $this->registry->getManager($entityManager);
            }
            // Repository'yi al
            $repository = $em->getRepository($entityClass);
            
            // Query builder oluştur
            $qb = $repository->createQueryBuilder('e')
                ->select("DISTINCT e.{$fieldName} as value")
                ->where("e.{$fieldName} IS NOT NULL")
                ->andWhere("e.{$fieldName} != ''")
                ->orderBy("e.{$fieldName}", 'ASC');
            
            // Sonuçları al
            $results = $qb->getQuery()->getResult();
            
            // Sadece değerleri döndür
            return array_column($results, 'value');
            
        } catch (\Exception $e) {
            // Hata durumunda boş dizi döndür
            return [];
        }
    }
}
