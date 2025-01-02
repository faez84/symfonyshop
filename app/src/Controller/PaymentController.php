<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\PaymentFormType;
use App\Service\BasketManager;
use App\Service\Order\OrderCheckout;
use App\Service\Payment\PaymentMethodFactory;
use App\Service\Payment\PaymentMethodValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PaymentController extends AbstractController
{
    public function __construct(
        protected BasketManager $basketManager,
        protected PaymentMethodValidator $paymentMethodValidator,
        protected PaymentMethodFactory $paymentMethodFactory,
        protected OrderCheckout $orderCheckout
    ) {
    }

    #[Route(path: '/basket/payment', name: "basket_payment")]
    public function basketPayment(Request $request): Response
    {
        $addressId = (string) $request->get('addressId');
        $form = $this->createForm(PaymentFormType::class, $this->getFormConfig($addressId));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if ($this->paymentMethodValidator->valdiate($data['paymentMethod'])) {
                return $this->handlePayment($data, $addressId);
            }
        }

        return $this->render('payment/list.html.twig', $this->getPaymentRenderData($form, $addressId));
    }

    private function getFormConfig(string $addressId): array
    {
        return [
            'methods' => ['CreditCard' => 'CreditCard', 'Paypal' => 'Paypal'],
            'addressId' => $addressId
        ];
    }

    private function handlePayment(mixed $data, string $addressId): RedirectResponse
    {
        $paymentMethod = $this->paymentMethodFactory->getPaymentMethod($data['paymentMethod']);
        $result = $this->orderCheckout->finalizeOrder($paymentMethod, $addressId);

        if ($result === true) {
            return $this->redirectToRoute('order_execute');
        }

        $this->addFlash('notice', $result);

        return $this->redirectToRoute('disply_basket');
    }

    private function getPaymentRenderData(FormInterface $form, string $addressId): array
    {
        return [
            'payments' => ['Cridetcard', 'Paypal'],
            'addressId' => $addressId,
            'form' => $form,

        ];
    }
}
