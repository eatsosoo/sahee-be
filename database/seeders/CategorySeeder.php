<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Education Curriculum',
                'image_url' => 'https://intamphuc.vn/wp-content/uploads/2023/06/mau-bia-sach-dep-2.jpg',
            ],
            [
                'name' => 'Fiction & Fantasy',
                'image_url' => 'https://intamphuc.vn/wp-content/uploads/2023/06/mau-bia-sach-dep-4.jpg',
            ],
            [
                'name' => 'Religion & Spirituality',
                'image_url' => 'https://intamphuc.vn/wp-content/uploads/2023/06/mau-bia-sach-dep-6.jpg',
            ],
            [
                'name' => 'Romance Books',
                'image_url' => 'https://intamphuc.vn/wp-content/uploads/2023/06/mau-bia-sach-dep-7.jpg',
            ],
            [
                'name' => 'Literature & Fiction',
                'image_url' => 'https://intamphuc.vn/wp-content/uploads/2023/06/mau-bia-sach-dep.8.jpg',
            ],
            [
                'name' => 'Biographies & Memoirs',
                'image_url' => 'https://danviet.mediacdn.vn/upload/2-2015/images/2015-06-30/1436846015-tbdlbat_coc_2_ygdr.jpg',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
