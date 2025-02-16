<?php

declare(strict_types=1);

namespace App\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\State\Pagination\PartialPaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Retrieves data from a persistence layer.
 *
 * @template T of object
 *
 * @author Antoine Bluchet <soyuka@gmail.com>
 */
class UserAddressesProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $internalProcess,
        private UserPasswordHasherInterface $userPasswordHasherInterface,
        private Security $security
    ) {
    }

        /**
     * Provides data.
     *
     * @param array<string, mixed>                                                   $uriVariables
     * @param array<string, mixed>|array{request?: Request, resource_class?: string} $context
     *
     * @return T|PartialPaginatorInterface<T>|iterable<T>|null
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /** @var User $user */
        $user = $this->security->getUser();

        return $user->getAddresses();
    }
}
