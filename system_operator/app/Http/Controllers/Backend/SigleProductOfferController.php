<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admins;
use App\Models\Product;
use App\Models\SingleProductOffer;
use Image;
use Auth;
use Helper;

class SigleProductOfferController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function index()
    {

        if (is_null($this->user) || !$this->user->can('marketing.single.product.offer')) {
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        $offers = SingleProductOffer::where('status', 1)->orderBy('id')->get();
        return view('backend.pages.marketing.single-product-offer')->with('offers', $offers);
    }

    public function create()
    {
        if (is_null($this->user) || !$this->user->can('marketing.single.product.offer.create')) {
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        $products = Product::where('is_active',1)->where('is_deleted',0)->where('product_qc',1)->orderBy('title', 'asc')->get();
        return view('backend.pages.marketing.create-admin-single-product-offer', compact('products'));
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('marketing.single.product.offer.create')) {
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        $request->validate([
            'title' => 'nullable|max:190',
            'slug' => 'required | unique:single_product_offers',
            'product_id' => 'required'
        ]);

        $offer = new SingleProductOffer();
        $offer->title = $request->title;
        $offer->slug = $request->slug;
        $offer->product_id = $request->product_id;
        $offer->offer_start_date = $request->offer_start_date;
        $offer->offer_end_date = $request->offer_end_date;
        $offer->discount = $request->discount;
        $offer->discount_type = $request->discount_type;
        $offer->status = ($request->status) ? 1 : 0;

        $banners = [];
        for ($i=0; $i < count($request->banner_title) ; $i++) { 
            $banners_array = [
                'image' => $request->banner_images[$i],
                'title' => $request->banner_title[$i],
                'details' =>  $request->banner_details[$i]
            ];
            array_push($banners, $banners_array);
        }

        $offer->banners = json_encode($banners);

        $package = [
            'heading' => $request->package_heading,
            'image' => $request->package_image,
            'details' =>  $request->package_details
        ];
        $offer->package = json_encode($package);

        $why_our_perfume_is_unique = [
            'heading' => $request->why_our_perfume_is_unique,
            'title' => $request->why_our_perfume_is_unique_heading
        ];
        for ($j=0; $j < count($request->why_our_perfume_is_unique_title) ; $j++) { 
            $why_our_perfume_is_unique_array = [
                'icon' => $request->why_our_perfume_is_unique_icon[$j],
                'title' => $request->why_our_perfume_is_unique_title[$j],
                'details' =>  $request->why_our_perfume_is_unique_details[$j]
            ];
            array_push($why_our_perfume_is_unique, $why_our_perfume_is_unique_array);
        }
        $offer->why_our_perfume_is_unique = json_encode($why_our_perfume_is_unique);

        $product_video = [
            'heading' => $request->product_video_deading,
            'link' =>  $request->product_video_link
        ];
        $offer->product_video = json_encode($product_video);

        $size_and_price = [
            'image'=> $request->price_size_image,
        ];
        for ($k=0; $k < count($request->product_size_and_price_size) ; $k++) { 
            $size_and_price_array = [
                'size' => $request->product_size_and_price_size[$k],
                'price' => $request->product_size_and_price_price[$k],
            ];
            array_push($size_and_price, $size_and_price_array);
        }
        $offer->size_and_price = json_encode($size_and_price);

        $why_we_are_best = [
            'image' => $request->why_we_best_image,
            'history' => $request->our_history,
            'return' =>  $request->refund_exchange_facility,
            'cashon' =>  $request->cash_on_delivery_facility,
            'support' =>  $request->our_support
        ];
        $offer->why_we_are_best = json_encode($why_we_are_best);

        $who_can_used = [
            'heading' => $request->who_can_use_the_product_heading,
            'image' => $request->who_can_use_the_product_image,
        ];
        for ($l=0; $l < count($request->who_can_use_the_product_point) ; $l++) { 
            $who_can_used_array = [
                'points' => $request->who_can_use_the_product_point[$l]
            ];
            array_push($who_can_used, $who_can_used_array);
        }
        $offer->who_can_used = json_encode($who_can_used);

        $customer_reviews = [];
        for ($m=0; $m < count($request->reviewer_name) ; $m++) { 
            $customer_reviews_array = [
                'name' => $request->reviewer_name[$m],
                'image' => $request->reviewer_image[$m] ?? null,
                'review' => $request->reviewer_details[$m],
                'stars' => $request->reviewer_star[$m]
            ];
            array_push($customer_reviews, $customer_reviews_array);
        }
        $offer->customer_reviews = json_encode($customer_reviews);

        $faqs = [
            'image' => $request->faq_section_image,
        ];
        for ($n=0; $n < count($request->faqscoppy_question) ; $n++) { 
            $faqs_array = [
                'question' => $request->faqscoppy_question[$n],
                'answare' => $request->faqscoppy_answare[$n]
            ];
            array_push($faqs, $faqs_array);
        }
        $offer->faqs = json_encode($faqs);
        $offer->save();

        return redirect()->route('admin.single.product.offer')->with('success', 'Offer successfully created!');
        
    }

    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('marketing.single.product.offer.create')) {
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        $offer = SingleProductOffer::find($id);
        $products = Product::where('is_active',1)->where('is_deleted',0)->where('product_qc',1)->orderBy('title', 'asc')->get();
        return view('backend.pages.marketing.single-product-offer-edit')->with(
            array(
                'offer' => $offer,
                'products' => $products
            )
        );
    }

    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('marketing.single.product.offer.edit')) {
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        $request->validate([
            'title' => 'nullable|max:190',
            'product_id' => 'required'
        ]);

        $offer = SingleProductOffer::find($id);
        $offer->title = $request->title;
        $offer->product_id = $request->product_id;
        $offer->offer_start_date = $request->offer_start_date;
        $offer->offer_end_date = $request->offer_end_date;
        $offer->discount = $request->discount;
        $offer->discount_type = $request->discount_type;
        $offer->status = ($request->status) ? 1 : 0;

        $banners = [];
        for ($i=0; $i < count($request->banner_title) ; $i++) { 
            $banners_array = [
                'image' => $request->banner_images[$i],
                'title' => $request->banner_title[$i],
                'details' =>  $request->banner_details[$i]
            ];
            array_push($banners, $banners_array);
        }

        $offer->banners = json_encode($banners);

        $package = [
            'heading' => $request->package_heading,
            'image' => $request->package_image,
            'details' =>  $request->package_details
        ];
        $offer->package = json_encode($package);

        $why_our_perfume_is_unique = [
            'heading' => $request->why_our_perfume_is_unique,
            'title' => $request->why_our_perfume_is_unique_heading
        ];
        for ($j=0; $j < count($request->why_our_perfume_is_unique_title) ; $j++) { 
            $why_our_perfume_is_unique_array = [
                'icon' => $request->why_our_perfume_is_unique_icon[$j],
                'title' => $request->why_our_perfume_is_unique_title[$j],
                'details' =>  $request->why_our_perfume_is_unique_details[$j]
            ];
            array_push($why_our_perfume_is_unique, $why_our_perfume_is_unique_array);
        }
        $offer->why_our_perfume_is_unique = json_encode($why_our_perfume_is_unique);

        $product_video = [
            'heading' => $request->product_video_deading,
            'link' =>  $request->product_video_link
        ];
        $offer->product_video = json_encode($product_video);

        $size_and_price = [
            'image'=> $request->price_size_image,
        ];
        for ($k=0; $k < count($request->product_size_and_price_size) ; $k++) { 
            $size_and_price_array = [
                'size' => $request->product_size_and_price_size[$k],
                'price' => $request->product_size_and_price_price[$k],
            ];
            array_push($size_and_price, $size_and_price_array);
        }
        $offer->size_and_price = json_encode($size_and_price);

        $why_we_are_best = [
            'image' => $request->why_we_best_image,
            'history' => $request->our_history,
            'return' =>  $request->refund_exchange_facility,
            'cashon' =>  $request->cash_on_delivery_facility,
            'support' =>  $request->our_support
        ];
        $offer->why_we_are_best = json_encode($why_we_are_best);

        $who_can_used = [
            'heading' => $request->who_can_use_the_product_heading,
            'image' => $request->who_can_use_the_product_image,
        ];
        for ($l=0; $l < count($request->who_can_use_the_product_point) ; $l++) { 
            $who_can_used_array = [
                'points' => $request->who_can_use_the_product_point[$l]
            ];
            array_push($who_can_used, $who_can_used_array);
        }
        $offer->who_can_used = json_encode($who_can_used);

        $customer_reviews = [];
        for ($m=0; $m < count($request->reviewer_name) ; $m++) { 
            $customer_reviews_array = [
                'name' => $request->reviewer_name[$m],
                'image' => $request->reviewer_image[$m] ?? null,
                'review' => $request->reviewer_details[$m],
                'stars' => $request->reviewer_star[$m]
            ];
            array_push($customer_reviews, $customer_reviews_array);
        }
        $offer->customer_reviews = json_encode($customer_reviews);

        $faqs = [
            'image' => $request->faq_section_image,
        ];
        for ($n=0; $n < count($request->faqscoppy_question) ; $n++) { 
            $faqs_array = [
                'question' => $request->faqscoppy_question[$n],
                'answare' => $request->faqscoppy_answare[$n]
            ];
            array_push($faqs, $faqs_array);
        }
        $offer->faqs = json_encode($faqs);
        $offer->save();

        return redirect()->route('admin.single.product.offer')->with('success', 'Offer successfully updated!');
        
    }

    public function delete(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('marketing.single.product.offer.delete')) {
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        $offer = SingleProductOffer::find($id);
        $offer->delete();
        return redirect()->route('admin.single.product.offer')->with('success', 'Offer successfully deleted!');
    }
}
