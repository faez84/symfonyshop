<?php
declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Exception\ValidatorException;

class UserResetPasswordProcessor implements ProcessorInterface 
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')] readonly private ProcessorInterface $internalProcess,
        readonly private UserPasswordHasherInterface $userPasswordHasherInterface,
        private Security $security)
    {
        
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $user = $this->security->getUser();

        if (!$user->getPassword()) {
       
            return $this->internalProcess->process($user, $operation, $uriVariables, $context);
        }


        $oldPass = $data->getOldPassword();
        if (!$this->userPasswordHasherInterface->isPasswordValid($user, $oldPass))
        {
            throw new ValidatorException('Invalid password!');
        }

        $hashedPassword = $this->userPasswordHasherInterface->hashPassword($user, $data->getNewPassword());
        $user->setPassword($hashedPassword);
        $user->eraseCredentials();
        
        // Handle the state

 

        return $this->internalProcess->process($user, $operation, $uriVariables, $context);
    }
}