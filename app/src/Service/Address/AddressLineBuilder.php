<?php

declare(strict_types=1);

namespace App\Service\Address;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;

class AddressLineBuilder
{
    public function __construct(private Security $security)
    {
    }
    public function build(): array
    {
        $addresses = [];
        $user = $this->getUser();
        foreach ($user->getAddresses() as $address) {
            $addresses[$address->getId()] = 
            sprintf('%s, %s %s', $address->getStreet(), $address->getZip(), $address->getCity());
        }

       return $addresses;
    }

    public function getUser(): User
    {
        return $this->security->getUser();
    }
}