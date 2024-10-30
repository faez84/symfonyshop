<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\PaymentFormType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Service\BasketManager;
use App\Service\Order\OrderCheckout;
use App\Service\Payment\PaymentMethodFactory;
use App\Service\Payment\PaymentMethodValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

        $form = $this->createForm(PaymentFormType::class, ['CreditCard' => 'CreditCard', 'Paypal' => 'Paypal']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // data is an array with "name", "email", and "message" keys
            $data = $form->getData();

            //$paymentMethodValidator = $this->container->get('payment.PaymentMethodValidator');
            //$paymentMethodFactory = $this->container->get(PaymentMethodFactory::class);
            if ($this->paymentMethodValidator->valdiate($data['paymentMethod'])) {

                $paymentMethod = $this->paymentMethodFactory->getPaymentMethod($data['paymentMethod']);
                $result = $this->orderCheckout->finalizeOrder($paymentMethod);
                if ($result === true) {
                    return $this->redirectToRoute('order_execute');
                }
                $this->addFlash(
                    'notice',
                    $result
                );
                return $this->redirectToRoute('disply_basket');
            }
        }
        return $this->render('payment/list.html.twig', [
            'payments' => ['Cridetcard', 'Paypal'],
            'form' => $form,
        ]);
    }
}
