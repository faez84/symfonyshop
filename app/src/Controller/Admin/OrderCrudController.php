<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\Field\MapField;
use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;

/**
 * This will suppress all the PMD warnings in
 * this class.
 *
 * @SuppressWarnings(PHPMD)
 */
class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            MoneyField::new('cost')->setCurrency('EUR'),
            TextField::new('payment'),
            TextField::new('status'),
            DateTimeField::new('createdAt'),
            MapField::new('id', value: 'rrrrr'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
    // this action executes the 'renderInvoice()' method of the current CRUD controller
    $viewInvoice = Action::new('viewInvoice', 'Invoice', 'fa fa-file-invoice')
        ->linkToCrudAction('renderInvoice');
    return $actions
        // ...
        ->add(Crud::PAGE_DETAIL, $viewInvoice)
        ;
    }

    public function renderInvoice(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();

        // add your logic here...
    }

}
