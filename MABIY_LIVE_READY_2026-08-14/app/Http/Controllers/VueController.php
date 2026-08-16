<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;

class VueController extends Controller
{
public function __invoke($url = null)
    {
        return view('master', [
            'meta' => $this->getMeta(),
            'session_key' => Str::random(64)
        ]);

    }

    private function getMeta(): array
    {
        $meta['title'] = '';
        $meta['keyword'] = '';
        $meta['description'] = '';
        $meta['image'] = '';
        $meta['ssr'] = '';

        $urlPath = \Request::path();
        $urlExplode = explode('/',$urlPath);
        
        if($urlExplode){
            $urlPath = $urlExplode[0];
        }



        switch ($urlPath) {
            case "product":
                if(isset($urlExplode[1])){
                    $slug = $urlExplode[1];
                    $product = \DB::table('products')->where('slug',$slug)->first();
                    if($product){
                        $meta['type'] = 'product';
                        $meta['title'] = \DB::table('product_metas')->where('product_id',$product->id)->where('meta_key','meta_title')->first()->meta_value ?? $product->title;
                        $meta['keyword']  = \DB::table('product_metas')->where('product_id',$product->id)->where('meta_key','meta_keyword')->first()->meta_value ?? '';
                        $meta['description'] = \DB::table('product_metas')->where('product_id',$product->id)->where('meta_key','meta_description')->first()->meta_value ?? $product->short_description;
                        $meta['image'] = env('APP_API_URL').'/'.$product->default_image;
                        $meta['id'] = $product->id;
                        $meta['price'] = $product->price;
                        $meta['availability'] = ($product->in_stock == 1) ? 'in stock' : 'out of stock';
                        $meta['ssr'] = '<h1>'.$product->title.'</h1> <div>'.$product->short_description.'</div> <div>'.$product->description.'</div>';
                    }
                }
            break;
            case "category":
                if(isset($urlExplode[1])){
                    $slug = $urlExplode[1];
                    $category = \DB::table('categories')->where('slug', $slug)->first();
                    if($category){
                        $meta['type'] = 'category';
                        $meta['title'] = \DB::table('categories')->where('slug', $slug)->first()->meta_title ?? $category->title;
                        $meta['keyword']  = \DB::table('categories')->where('slug', $slug)->first()->meta_keyword ?? '';
                        $meta['description'] = \DB::table('categories')->where('slug', $slug)->first()->meta_description ?? $category->description;
                        $meta['image'] = env('APP_API_URL').'/'.$category->banner;
                        $meta['ssr'] = '<h1>'.$category->title.'</h1> <div>'.$category->description.'</div>';
                    }
                }
            break;
            case "property":
                
                if(isset($urlExplode[1])){
                    $slug = $urlExplode[1];
                    $blogs = \DB::table('blogs')->where('slug', $slug)->first();
                    if($blogs){
                        $meta['type'] = 'property';
                        $meta['title'] = \DB::table('blogs')->where('slug', $slug)->first()->meta_title ?? $blogs->title;
                        $meta['keyword']  = \DB::table('blogs')->where('slug', $slug)->first()->meta_keyword ?? '';
                        $meta['description'] = \DB::table('blogs')->where('slug', $slug)->first()->meta_description ?? $blogs->description;
                        //$meta['image'] = env('APP_API_URL').'/'.$blogs->banner;
                        $meta['ssr'] = '<h1>'.$blogs->title.'</h1> <div>'.$blogs->description.'</div>';
                    }
                }
            break;
            case "shop":
                if(isset($urlExplode[1])){
                    $slug = $urlExplode[1];
                    $shop = \DB::table('shop_info')->where('slug', $slug)->first();
                    if($shop){
                        $meta['type'] = 'shop';
                        $meta['title'] = \DB::table('shop_info')->where('slug', $slug)->first()->meta_title ?? $shop->name;
                        $meta['keyword']  = \DB::table('shop_info')->where('slug', $slug)->first()->name ?? '';
                        $meta['description'] = \DB::table('shop_info')->where('slug', $slug)->first()->meta_description ?? $shop->name;
                        $meta['image'] = env('APP_API_URL').'/'.$shop->banner;
                        $meta['ssr'] = '<h1>'.$shop->name.'</h1> <div>'.$shop->name.'</div>';
                    }
                }
            break;
            case "pages":
                if(isset($urlExplode[1])){
                    $slug = $urlExplode[1];
                    $pages = \DB::table('pages')->where('slug', $slug)->first();
                    if($pages){
                        $meta['type'] = 'Privacy Policy';
                        $meta['title'] = \DB::table('pages')->where('slug', $slug)->first()->meta_title ?? $pages->title;
                        $meta['keyword']  = \DB::table('pages')->where('slug', $slug)->first()->meta_keyword ?? '';
                        $meta['description'] = \DB::table('pages')->where('slug', $slug)->first()->meta_description ?? $pages->meta_description;
                        $meta['image'] = env('APP_API_URL').'/uploads/images/settings/'.\DB::table('settings')->where('key','header_logo')->first()->value ?? '';
                        $meta['ssr'] = '<h1>'.$pages->title.'</h1> <div>'.$pages->meta_description.'</div>';
                    }
                }
            break;

            case "offer":
                if(isset($urlExplode[1]) == 'products'){
                    if(isset($urlExplode[2])){
                        $slug = $urlExplode[2];
                        $category = \DB::table('categories')->where('slug', $slug)->first();
                        if($category){
                            $meta['type'] = 'offer';
                            $meta['title'] = \DB::table('categories')->where('slug', $slug)->first()->meta_title ?? $category->title;
                            $meta['keyword']  = \DB::table('categories')->where('slug', $slug)->first()->meta_keyword ?? '';
                            $meta['description'] = \DB::table('categories')->where('slug', $slug)->first()->meta_description ?? strip_tags($category->description);
                            $meta['image'] = env('APP_API_URL').'/'.$category->banner;
                            $meta['ssr'] = '<h1>'.$category->title.'</h1> <div>'.strip_tags($category->description).'</div>';
                        }
                    }
                }
            break;
            default:
                $meta['title'] = \DB::table('settings')->where('key','site_meta_title')->first()->value ?? '';
                $meta['keyword'] = \DB::table('settings')->where('key','site_meta_keyword')->first()->value ?? '';
                $meta['description'] = \DB::table('settings')->where('key','site_meta_description')->first()->value ?? '';
                $meta['image'] = env('APP_API_URL').'/uploads/images/settings/'.\DB::table('settings')->where('key','header_logo')->first()->value ?? '';
                $meta['ssr'] = '';
                break;
        }

        return $meta;
    }






}
