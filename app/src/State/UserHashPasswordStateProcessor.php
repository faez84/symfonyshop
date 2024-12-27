<?php
declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class UserHashPasswordStateProcessor implements ProcessorInterface 
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        readonly private ProcessorInterface $internalProcess,
        readonly private UserPasswordHasherInterface $userPasswordHasherInterface)
    {
        
    }

    public function process(mixed $user, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!$user->getPassword()) {
            return $this->internalProcess->process($user, $operation, $uriVariables, $context);
        }

        if ($user instanceof User) {
        // Handle the state
        $hashedPassword = $this->userPasswordHasherInterface->hashPassword($user, $user->getPassword());
        $user->setPassword($hashedPassword);
        }
        $user->eraseCredentials();
        return $this->internalProcess->process($user, $operation, $uriVariables, $context);
    }
}