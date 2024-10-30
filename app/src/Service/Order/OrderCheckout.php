<?php

declare(strict_types=1);

namespace App\Service\Order;
use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\Product;
use App\Service\BasketManager;
use App\Service\Payment\IPayment;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class OrderCheckout
{
    //private const METHODS = ['Paypal', 'CreditCard'];

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        protected BasketManager $basketManager
    ) {

    }
    public function finalizeOrder(IPayment $payment): bool|string
    {
        $conn = $this->entityManager->getConnection();
        $conn->beginTransaction();
        $basket = $this->basketManager->getBasketProducts();
        try {
            $order = $this->saveOrder($payment->getPaymentName(), $basket['cost']);
            $this->saveOrderArticles($order, $basket["products"]);
            $this->executePayment($payment, $order);
            $conn->commit();

        } catch (Exception) {
            $conn->rollBack();

            return 'Error during finalizing Order';
        }
        return true;
    }

    public function saveOrder(string $payment, float $cost): Order
    {
        $order = new Order();

        $order->setPayment($payment);
        $order->setStatus("init");
        $order->setCost($cost);
        $order->setCreatedAt();
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order;
    }

    /**
     * @param array<array> $productIds
     * @return void
     * @throws Exception
     */
    public function saveOrderArticles(Order $order, array $productIds): void
    {
        foreach ($productIds as $productId => $productData) {
            $product = $this->entityManager->getRepository(Product::class)->find($productId);
            $amount = (int) $productData['amount'];
            $quantity = $product->getQuantity();
            if ($amount > $product->getQuantity()) {
                throw new Exception(sprintf("Product : %s is out of stock", $product->getTitle()));
            }
            $orderProduct = new OrderProduct();
            $orderProduct->setOOrder($order);
            $orderProduct->setPproduct($product);
            $orderProduct->setAmount($amount);
            $orderProduct->setCost($product->getPrice() * $amount);
            $orderProduct->setCreatedAt();
            $this->entityManager->persist($orderProduct);

            $newQuantity = $quantity - $amount;
            $product->setQuantity($newQuantity);
            $this->entityManager->persist($product);
        }

        $this->entityManager->flush();
    }

    public function executePayment(IPayment $payment, Order $order): void
    {
        $status = 'NotFinished';
        if ($payment->executePayment()) {
            $status = 'Finished';
        }
        $order->setStatus($status);

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        $this->basketManager->resetBasket();
    }
}