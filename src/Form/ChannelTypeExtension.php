<?php

declare(strict_types=1);

namespace Nedac\SyliusMinimumOrderValuePlugin\Form;

use Nedac\SyliusMinimumOrderValuePlugin\Model\ChannelInterface;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelType;
use Sylius\Bundle\CurrencyBundle\Form\Type\CurrencyChoiceType;
use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Webmozart\Assert\Assert;

final class ChannelTypeExtension extends AbstractTypeExtension
{
    /**
     * @param FormBuilderInterface<mixed> $builder
     * @param array<string, mixed> $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $formModifier = function (?FormInterface $form, ?string $currencyCode, ?int $minimumOrderValue = null): void {
            Assert::notNull($form);
            Assert::notNull($currencyCode);

            $options = [
                'label' => 'nedac_sylius_minimum_order_value_plugin.ui.minimum_order_value',
                'currency' => $currencyCode,
                'attr' => ['disabled' => true],
                'required' => false
            ];

            if (null !== $minimumOrderValue) {
                $options['data'] = $minimumOrderValue;
            }

            $form->add('minimumOrderValue', MoneyType::class, $options);
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier): void {
                $form = $event->getForm();

                $baseCurrencyForm =  $form->get('baseCurrency');
                $config = $baseCurrencyForm->getConfig();
                $attributes = $config->getAttributes();

                $disabled = true;
                if (isset($attributes['data_collector/passed_options']['disabled'])) {
                    $disabled = $attributes['data_collector/passed_options']['disabled'];
                }

                $form->remove('baseCurrency');

                $builder = $config->getFormFactory()->createNamedBuilder(
                    'baseCurrency',
                    CurrencyChoiceType::class,
                    null,
                    [
                        'label' => 'sylius.form.channel.currency_base',
                        'required' => true,
                        'disabled' => $disabled,
                        'auto_initialize' => false
                    ]
                );

                $builder->addEventListener(
                    FormEvents::POST_SUBMIT,
                    function (FormEvent $event) use ($formModifier): void {
                        /** @var CurrencyInterface|null $baseCurrency */
                        $baseCurrency = $event->getForm()->getData();
                        if (null !== $baseCurrency) {
                            $formModifier($event->getForm()->getParent(), $baseCurrency->getCode());
                        }
                    }
                );

                $form->add($builder->getForm());

                /** @var ChannelInterface $channel */
                $channel = $event->getData();
                $baseCurrency = $channel->getBaseCurrency();
                if (null !== $baseCurrency) {
                    $formModifier($form, $baseCurrency->getCode(), $channel->getMinimumOrderValue());
                }
            }
        );
    }

    /**
     * @return iterable<int, string>
     */
    public function getExtendedTypes(): iterable
    {
        return [
            ChannelType::class
        ];
    }
}
