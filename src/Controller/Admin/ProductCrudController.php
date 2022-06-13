<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'Название'),
            IntegerField::new('availableCount', 'Колличество'),
            IntegerField::new('cost', 'Цена'),
            TextField::new('photo', 'Фото'),
            TextEditorField::new('description', 'Описание'),
            TextField::new('gost', 'Гост'),
            TextField::new('odkp2', 'ОДКП2'),
            TextField::new('tnved', 'ТНВД')
        ];
    }
}
