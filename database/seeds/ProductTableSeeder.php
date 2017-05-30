<?php

use Illuminate\Database\Seeder;

class ProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $product = new \App\Product([
            'imagePath' =>'https://www.truffleshuffle.co.uk/images_high_res/TS_Mens_Charcoal_Distressed_Superman_Logo_T_Shirt_9_99_HR_1.jpg',
            'title' => 'Super Man',
            'description' => 'Super cool t-shirt',
            'price' => 10
        ]);
        $product->save();

        $product = new \App\Product([
            'imagePath' =>'https://www.truffleshuffle.co.uk/images_high_res/TruffleShuffle_com_Kids_Dark_Grey_Marl_DC_Comics_Batman_Logo_T_Shirt_With_Cape_12_99_hi_res.jpg',
            'title' => 'Bat Man',
            'description' => 'Super cool t-shirt for bat man fantasy',
            'price' => 12
        ]);
        $product->save();

        $product = new \App\Product([
            'imagePath' =>'https://image.spreadshirtmedia.com/image-server/v1/products/1009006377/views/1,width=800,height=800,appearanceId=70,version=1456748648/adventure-block-baseball-t-shirt.jpg',
            'title' => 'Adventure block',
            'description' => 'Super cool Adventure block baseball t-shirt',
            'price' => 12
        ]);
        $product->save();

        $product = new \App\Product([
            'imagePath' =>'https://image.spreadshirtmedia.com/image-server/v1/products/1011045775/views/1,width=800,height=800,appearanceId=550,backgroundColor=E8E8E8,version=1460465149/notorious-esports-baseball-shirt-baseball-t-shirt.jpg',
            'title' => 'Notorious Sports',
            'description' => 'Super cool Notorious t-shirt',
            'price' => 15
        ]);
        $product->save();

        $product = new \App\Product([
            'imagePath' =>'http://store.sho.com/imgcache/property/resized/000/751/327/catl/ray-donovan-fite-club-grey-gloves-t-shirt-741_1000.jpg?k=d2d59d5b&pid=459741&s=catl&sn=showtime',
            'title' => 'Fight Club',
            'description' => 'Super cool Fight Club t-shirt',
            'price' => 10
        ]);
        $product->save();

        $product = new \App\Product([
            'imagePath' =>'https://image.spreadshirtmedia.com/image-server/v1/products/1008013173/views/1,width=800,height=800,appearanceId=70,version=1456748648/4fish-sand-texture-graphics-baseball-t-shirt.jpg',
            'title' => 'What ever',
            'description' => 'Super cool What ever t-shirt',
            'price' => 20
        ]);
        $product->save();

    }
}
