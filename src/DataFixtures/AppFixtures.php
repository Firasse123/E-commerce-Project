<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Product;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures  extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Création des catégories
        $categories = [
            'T-shirts' => 'Collection de t-shirts pour homme',
            'Chemises' => 'Chemises élégantes et décontractées',
            'Pantalons' => 'Pantalons et jeans de qualité',
            'Vestes' => 'Vestes et manteaux pour toutes saisons',
            'Chaussures' => 'Chaussures de ville et décontractées',
            'Accessoires' => 'Ceintures, montres et accessoires'
        ];

        $categoryEntities = [];
        foreach ($categories as $name => $description) {
            $category = new Category();
            $category->setName($name);
            $category->setDescription($description);
            
            $manager->persist($category);
            $categoryEntities[$name] = $category;
        }

        // Création des utilisateurs
        // Admin
        $admin = new User();
        $admin->setEmail('admin@menswear.com');
        $admin->setFirstName('Admin');
        $admin->setLastName('User');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword(
            $this->passwordHasher->hashPassword($admin, 'admin123')
        );
        $admin->setIsVerified(true);
        $manager->persist($admin);

        // Utilisateur normal
        $user = new User();
        $user->setEmail('user@menswear.com');
        $user->setFirstName('John');
        $user->setLastName('Doe');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, 'user123')
        );
        $manager->persist($user);

        // Autres utilisateurs de test
        for ($i = 1; $i <= 5; $i++) {
            $testUser = new User();
            $testUser->setEmail('user' . $i . '@example.com');
            $testUser->setFirstName('User' . $i);
            $testUser->setLastName('Test');
            $testUser->setRoles(['ROLE_USER']);
            $testUser->setPassword(
                $this->passwordHasher->hashPassword($testUser, 'password123')
            );
            $testUser->setIsVerified(true);
            $manager->persist($testUser);
        }

        // Création des produits
        $products = [
            // T-shirts
            [
                'name' => 'T-shirt Basique Blanc',
                'description' => 'T-shirt en coton 100% biologique, coupe classique, parfait pour un look décontracté.',
                'price' => 25.99,
                'stock' => 50,
                'category' => 'T-shirts',
                'image' => 'tshirt-blanc.jpg',
                'featured' => true
            ],
            [
                'name' => 'T-shirt Graphique Noir',
                'description' => 'T-shirt avec design graphique moderne, tissu premium, idéal pour les sorties.',
                'price' => 32.99,
                'stock' => 30,
                'category' => 'T-shirts',
                'image' => 'tshirt-graphique.jpg',
                'featured' => false
            ],
            [
                'name' => 'T-shirt Polo Bleu Marine',
                'description' => 'Polo élégant en coton piqué, col classique, parfait pour le bureau décontracté.',
                'price' => 45.99,
                'stock' => 25,
                'category' => 'T-shirts',
                'image' => 'polo-bleu.jpg',
                'featured' => true
            ],

            // Chemises
            [
                'name' => 'Chemise Oxford Blanche',
                'description' => 'Chemise classique en coton Oxford, coupe ajustée, indispensable du dressing masculin.',
                'price' => 65.99,
                'stock' => 20,
                'category' => 'Chemises',
                'image' => 'chemise-oxford.jpg',
                'featured' => true
            ],
            [
                'name' => 'Chemise à Carreaux',
                'description' => 'Chemise décontractée à motifs carreaux, parfaite pour le week-end.',
                'price' => 55.99,
                'stock' => 15,
                'category' => 'Chemises',
                'image' => 'chemise-carreaux.jpg',
                'featured' => false
            ],
            [
                'name' => 'Chemise Lin Beige',
                'description' => 'Chemise en lin naturel, légère et respirante, idéale pour l\'été.',
                'price' => 71.99,
                'stock' => 12,
                'category' => 'Chemises',
                'image' => 'chemise-lin.jpg',
                'featured' => true
            ],

            // Pantalons
            [
                'name' => 'Jean Slim Brut',
                'description' => 'Jean coupe slim en denim brut, style intemporel et polyvalent.',
                'price' => 99.99,
                'stock' => 35,
                'category' => 'Pantalons',
                'image' => 'jean-slim.jpg',
                'featured' => true
            ],
            [
                'name' => 'Chino Beige',
                'description' => 'Pantalon chino en coton stretch, coupe moderne, parfait pour toutes occasions.',
                'price' => 69.99,
                'stock' => 28,
                'category' => 'Pantalons',
                'image' => 'chino-beige.jpg',
                'featured' => false
            ],
            [
                'name' => 'Pantalon de Costume Noir',
                'description' => 'Pantalon de costume élégant, tissu premium, coupe droite classique.',
                'price' => 125.99,
                'stock' => 18,
                'category' => 'Pantalons',
                'image' => 'pantalon-costume.jpg',
                'featured' => false
            ],

            // Vestes
            [
                'name' => 'Veste en Jean',
                'description' => 'Veste en denim classique, coupe moderne, un essentiel du style casual.',
                'price' => 95.99,
                'stock' => 22,
                'category' => 'Vestes',
                'image' => 'veste-jean.jpg',
                'featured' => true
            ],
            [
                'name' => 'Blazer Marine',
                'description' => 'Blazer élégant en laine mélangée, parfait pour les occasions formelles.',
                'price' => 189.99,
                'stock' => 10,
                'category' => 'Vestes',
                'image' => 'blazer-marine.jpg',
                'featured' => true
            ],
            [
                'name' => 'Bomber Jacket',
                'description' => 'Veste bomber moderne avec finitions premium, style urbain contemporain.',
                'price' => 129.99,
                'stock' => 16,
                'category' => 'Vestes',
                'image' => 'bomber-jacket.jpg',
                'featured' => false
            ],

            // Chaussures
            [
                'name' => 'Sneakers Blanches',
                'description' => 'Baskets en cuir blanc, design minimaliste, confort optimal toute la journée.',
                'price' => 119.99,
                'stock' => 40,
                'category' => 'Chaussures',
                'image' => 'sneakers-blanches.jpg',
                'featured' => true
            ],
            [
                'name' => 'Derbies Marron',
                'description' => 'Chaussures de ville en cuir véritable, finitions soignées, élégance garantie.',
                'price' => 159.99,
                'stock' => 14,
                'category' => 'Chaussures',
                'image' => 'derbies-marron.jpg',
                'featured' => false
            ],
            [
                'name' => 'Boots Chelsea Noir',
                'description' => 'Bottines Chelsea en cuir noir, style britannique authentique.',
                'price' => 179.99,
                'stock' => 12,
                'category' => 'Chaussures',
                'image' => 'boots-chelsea.jpg',
                'featured' => false
            ],

            // Accessoires
            [
                'name' => 'Ceinture Cuir Noir',
                'description' => 'Ceinture en cuir véritable avec boucle classique, accessoire indispensable.',
                'price' => 39.99,
                'stock' => 45,
                'category' => 'Accessoires',
                'image' => 'ceinture-cuir.jpg',
                'featured' => false
            ],
            [
                'name' => 'Montre Analogique',
                'description' => 'Montre élégante avec bracelet en cuir, mouvement quartz précis.',
                'price' => 199.99,
                'stock' => 8,
                'category' => 'Accessoires',
                'image' => 'montre-analogique.jpg',
                'featured' => true
            ],
            [
                'name' => 'Portefeuille Cuir',
                'description' => 'Portefeuille compact en cuir véritable, design minimaliste et fonctionnel.',
                'price' => 49.99,
                'stock' => 25,
                'category' => 'Accessoires',
                'image' => 'portefeuille-cuir.jpg',
                'featured' => false
            ]
        ];

        foreach ($products as $productData) {
            $product = new Product();
            $product->setName($productData['name']);
            $product->setDescription($productData['description']);
            $product->setPrice($productData['price']);
            $product->setStock($productData['stock']);
            $product->setImage($productData['image']);
            $product->setIsActive(true);
            $product->setCategory($categoryEntities[$productData['category']]);
            $product->setCreatedAt(new \DateTimeImmutable());
            $product->setUpdatedAt(new \DateTimeImmutable());

            $manager->persist($product);
        }

        $manager->flush();
    }
}