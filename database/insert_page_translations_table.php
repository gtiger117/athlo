<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $page_translations =[ 
            [ 'page_id'=>101,'locale'=>'en','title'=>'athlokinisi','meta_title'=>'athlokinisi','meta_description'=>'athlokinisi','route'=>'/page/login','created_at'=>'2023-07-27 10=>42=>55','updated_at'=>'2023-07-27 10=>42=>55'],
            [ 'page_id'=>102,'locale'=>'en','title'=>'athlokinisi','meta_title'=>'athlokinisi','meta_description'=>'athlokinisi','route'=>'/page/register','created_at'=>'2023-07-27 10=>43=>40','updated_at'=>'2023-07-27 10=>43=>40'],
            [ 'page_id'=>103,'locale'=>'en','title'=>'athlokinisi','meta_title'=>'athlokinisi','meta_description'=>'athlokinisi','route'=>'/page/forgotten-password','created_at'=>'2023-07-27 10=>44=>43','updated_at'=>'2023-07-27 10=>44=>43'],
            [ 'page_id'=>104,'locale'=>'en','title'=>'athlokinisi','meta_title'=>'athlokinisi','meta_description'=>'athlokinisi','route'=>'/page/password','created_at'=>'2023-07-27 10=>45=>46','updated_at'=>'2023-07-27 10=>45=>46'],
            [ 'page_id'=>105,'locale'=>'en','title'=>'athlokinisi','meta_title'=>'athlokinisi','meta_description'=>'athlokinisi','route'=>'/page/blog','created_at'=>'2023-07-27 10=>49=>06','updated_at'=>'2023-07-27 10=>49=>06'],
            [ 'page_id'=>106,'locale'=>'en','title'=>'athlokinisi','meta_title'=>'athlokinisi','meta_description'=>'athlokinisi','route'=>'/page/contactus','created_at'=>'2023-07-27 10=>51=>02','updated_at'=>'2023-07-27 10=>51=>02'],
            [ 'page_id'=>107,'locale'=>'en','title'=>'athlokinisi','meta_title'=>'athlokinisi','meta_description'=>'athlokinisi','route'=>'/page/checkout','created_at'=>'2023-07-27 10=>53=>14','updated_at'=>'2023-07-27 10=>53=>14'],
            [ 'page_id'=>108,'locale'=>'en','title'=>'athlokinisi','meta_title'=>'athlokinisi','meta_description'=>'athlokinisi','route'=>'/page/account','created_at'=>null,'updated_at'=>null],
            [ 'page_id'=>109,'locale'=>'en','title'=>'athlokinisi','meta_title'=>'athlokinisi','meta_description'=>'athlokinisi','route'=>'/page/wishlist','created_at'=>null,'updated_at'=>null],
            [ 'page_id'=>110,'locale'=>'en','title'=>'athlokinisi','meta_title'=>'athlokinisi','meta_description'=>'athlokinisi','route'=>'/page/shoppingcart','created_at'=>null,'updated_at'=>null],
            [ 'page_id'=>111,'locale'=>'en','title'=>'athlokinisi','meta_title'=>'athlokinisi','meta_description'=>'athlokinisi','route'=>'/voucher/[id]/[name]','created_at'=>null,'updated_at'=>null],
            [ 'page_id'=>112,'locale'=>'en','title'=>'My Orders','meta_title'=>'My Orders','meta_description'=>'My Orders','route'=>'/page/myorders','created_at'=>null,'updated_at'=>null],
            [ 'page_id'=>113,'locale'=>'en','title'=>'Finalize Order','meta_title'=>'Finalize Order','meta_description'=>'Finalize Order','route'=>'/page/finalizeorder/[hash]','created_at'=>null,'updated_at'=>null],
            [ 'page_id'=>114,'locale'=>'en','title'=>'Search Page','meta_title'=>'','meta_description'=>'','route'=>'/page/search','created_at'=>null,'updated_at'=>null],
            [ 'page_id'=>115,'locale'=>'en','title'=>'athlokinisi','meta_title'=>'athlokinisi','meta_description'=>'athlokinisi','route'=>'/page/vouchers','created_at'=>null,'updated_at'=>null],
            [ 'page_id'=>116,'locale'=>'en','title'=>'Quickview','meta_title'=>'','meta_description'=>'','route'=>'/page/quickview/[id]','created_at'=>null,'updated_at'=>null],
            [ 'page_id'=>117,'locale'=>'en','title'=>'product page','meta_title'=>'','meta_description'=>'','route'=>'/product/[id]/[name]','created_at'=>null,'updated_at'=>null],
            [ 'page_id'=>118,'locale'=>'en','title'=>'athlokinisi','meta_title'=>'athlokinisi','meta_description'=>'athlokinisi','route'=>'/page/blogpost-detail/[index]','created_at'=>null,'updated_at'=>null],
            [ 'page_id'=>119,'locale'=>'en','title'=>'athlokinisi','meta_title'=>'athlokinisi','meta_description'=>'athlokinisi','route'=>'/page/21/brands','created_at'=>null,'updated_at'=>null],
            [ 'page_id'=>120,'locale'=>'en','title'=>'Home Page','meta_title'=>'','meta_description'=>'','route'=>'/page/home','created_at'=>null,'updated_at'=>null],
            [ 'page_id'=>121,'locale'=>'en','title'=>'sabbiancowebsite','meta_title'=>'sabbiancowebsite','meta_description'=>'sabbiancowebsite','route'=>'/category/[id]/[name]','created_at'=>null,'updated_at'=>null]
        ];
        DB::table('page_translations')->insert($page_translations);  
        // $this->insert('page_translations',$page_translations);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_translations');
    }
};
