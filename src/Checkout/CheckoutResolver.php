<?php

declare(strict_types=1);

namespace Nedac\SyliusMinimumOrderValuePlugin\Checkout;

use SM\Factory\FactoryInterface;
use Sylius\Bundle\CoreBundle\Checkout\CheckoutStateUrlGeneratorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class CheckoutResolver implements EventSubscriberInterface
{
    private CartContextInterface $cartContext;
    private CheckoutStateUrlGeneratorInterface $urlGenerator;
    private RequestMatcherInterface $requestMatcher;
    private FactoryInterface $stateMachineFactory;

    public function __construct(
        CartContextInterface $cartContext,
        CheckoutStateUrlGeneratorInterface $urlGenerator,
        RequestMatcherInterface $requestMatcher,
        FactoryInterface $stateMachineFactory
    ) {
        $this->cartContext = $cartContext;
        $this->urlGenerator = $urlGenerator;
        $this->requestMatcher = $requestMatcher;
        $this->stateMachineFactory = $stateMachineFactory;
    }

    public function onKernelRequest(GetResponseEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (!$this->requestMatcher->matches($request)) {
            return;
        }

        /** @var OrderInterface $order */
        $order = $this->cartContext->getCart();
        if ($order->isEmpty()) {
            $event->setResponse(new RedirectResponse($this->urlGenerator->generateForCart()));
        }

        $transition = $this->getRequestedTransition($request);

        $stateMachine = $this->stateMachineFactory->get($order, $this->getRequestedGraph($request));
        if ($stateMachine->can($transition)) {
            return;
        }

        if (OrderCheckoutTransitions::TRANSITION_ADDRESS === $transition) {
            $event->setResponse(new RedirectResponse($this->urlGenerator->generateForCart()));
            return;
        }

        $event->setResponse(new RedirectResponse($this->urlGenerator->generateForOrderCheckoutState($order)));
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    private function getRequestedGraph(Request $request): string
    {
        return $request->attributes->get('_sylius')['state_machine']['graph'];
    }

    private function getRequestedTransition(Request $request): string
    {
        return $request->attributes->get('_sylius')['state_machine']['transition'];
    }
}
