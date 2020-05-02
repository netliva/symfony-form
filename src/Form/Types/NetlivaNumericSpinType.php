<?phpnamespace Netliva\SymfonyFormBundle\Form\Types;use Symfony\Component\Form\AbstractType;use Symfony\Component\Form\Extension\Core\Type\TextType;use Symfony\Component\Form\FormBuilderInterface;use Symfony\Component\Form\FormEvent;use Symfony\Component\Form\FormEvents;use Symfony\Component\Form\FormView;use Symfony\Component\Form\FormInterface;use Symfony\Component\OptionsResolver\OptionsResolver;class NetlivaNumericSpinType extends AbstractType{	private $default_options = [		'min'            => 0,		'max'            => 999999999999,		'step'           => 1,		'decimals'       => 0,		'boostat'        => 5,		'maxboostedstep' => 10,		'postfix'        => '',		'initval'        => 0,	];	public function configureOptions (OptionsResolver $resolver)	{		$resolver->setDefaults([			'spin_option' => $this->default_options		]);	}	public function getBlockPrefix ()	{		return 'netliva_numeric_spin';	}	public function getParent ()	{		return TextType::class;	}	public function buildForm (FormBuilderInterface $builder, array $options)	{	}	public function buildView (FormView $view, FormInterface $form, array $options)	{		$view->vars['spin_option'] = $options['spin_option'] + $this->default_options;	}}