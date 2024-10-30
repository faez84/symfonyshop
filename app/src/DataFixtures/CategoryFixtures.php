<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Factory\CategoryFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;

use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;


class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getCategoriesData() as [$name, $parent]) {
            $category = new Category();
            $category->setTitle($name);
            //  $parentCategory = $parent;
            // if (!is_null($parent))
            // {
            $parentCategory = $manager->getRepository(Category::class)->findOneBy(['title' => $parent]);
            // }
            $category->setParent($parentCategory);

            $manager->persist($category);
            $manager->flush();
        }
    }

    /**
     * @return array
     */
    private function getCategoriesData(): array
    {
        return [
            ['Furniture', null],
            ['Beds', 'Furniture'],
            ['Chairs', 'Furniture'],
            ['Sports', null],
            ['Walker', 'Sports']
        ];
    }
}
