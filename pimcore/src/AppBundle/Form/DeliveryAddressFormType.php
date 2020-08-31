<?php
/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

declare(strict_types=1);

namespace AppBundle\Form;

use Pimcore\Localization\Locale;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeliveryAddressFormType extends AbstractType
{

    /**
     * @var Locale
     */
    protected $locale;

    public function __construct(Locale $locale)
    {
        $this->locale = $locale;
    }

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $regionArray = $this->locale->getDisplayRegions();

        $builder
            ->add('email', EmailType::class, [
                'label' => 'checkout.email'
            ])
            ->add('firstname', TextType::class, [
                'label' => 'checkout.firstname'
            ])
            ->add('lastname', TextType::class, [
                'label' => 'checkout.lastname'
            ])
            ->add('company', TextType::class, [
                'label' => 'checkout.company',
                'required' => false
            ])
            ->add('street', TextType::class, [
                'label' => 'checkout.street'
            ])
            ->add('zip', TextType::class, [
                'label' => 'checkout.zip'

            ])
            ->add('city', TextType::class, [
                'label' => 'checkout.city'
            ])
            ->add('countryCode', ChoiceType::class, [
                'label' => 'checkout.country',
                'choices' => [
                    strtoupper($regionArray['AT']) => 'AT',
                    strtoupper($regionArray['DE']) => 'DE'
                ],
                'choice_translation_domain' => false
            ])

            ->add('_submit', SubmitType::class, [
                'label' => 'checkout.submit-address'
            ]);
    }

    /**
     * @inheritDoc
     */
    public function getBlockPrefix()
    {
        // we need to set this to an empty string as we want _username as input name
        // instead of login_form[_username] to work with the form authenticator out
        // of the box
        return '';
    }
    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
    }
}
