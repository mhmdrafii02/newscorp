<?php

namespace App\Http\Controllers;

use App\Models\ArticleNews;
use App\Models\Author;
use App\Models\BannerAdvertisement;
use App\Models\Category;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    //
    public function index(){
        $categories = Category::all();

        $articles = ArticleNews::with(['category'])
        ->where('is_featured', 'not_featured')
        ->latest()
        ->take(3)
        ->get();

        $featured_articles = ArticleNews::with(['category'])
        ->where('is_featured', 'featured')
        ->inRandomOrder()
        ->take(3)
        ->get();

        $authors = Author::all();

        $bannerads = BannerAdvertisement::where('is_active', 'active')
        ->where('type', 'banner')
        ->inRandomOrder()
        // ->take(1)
        ->first();

        $entertaiment_articles = ArticleNews::whereHas('category', function ($query) {
            $query->where('name', 'Entertaiment');
        }) 
        ->where('is_featured', 'not_featured')
        ->latest()
        ->take(6)
        ->get();

        $entertaiment_featured_articles = ArticleNews::whereHas('category', function ($query) {
            $query->where('name', 'Entertaiment');
        }) 
        ->where('is_featured', 'featured')
        ->inRandomOrder()
        ->first();

        $business_articles = ArticleNews::whereHas('category', function ($query) {
            $query->where('name', 'Business');
        }) 
        ->where('is_featured', 'not_featured')
        ->latest()
        ->take(6)
        ->get();
        

        $business_featured_articles = ArticleNews::whereHas('category', function ($query) {
            $query->where('name', 'Business');
        }) 
        ->where('is_featured', 'featured')
        ->inRandomOrder()
        ->first();
        
        $automotive_articles = ArticleNews::whereHas('category', function ($query){
            $query->where('name', 'Automotive');
        })
        ->where('is_featured', 'not_featured')
        ->latest()
        ->take(6)
        ->get();
        
        $automotive_featured_articles = ArticleNews::whereHas('category', function ($query) {
            $query->where('name', 'Automotive');
        }) 
        ->where('is_featured', 'featured')
        ->inRandomOrder()
        ->first();

        return view('front.index', compact('automotive_featured_articles', 'automotive_articles', 'business_featured_articles', 'business_articles', 'entertaiment_featured_articles', 'entertaiment_articles', 'categories', 'articles', 'authors', 'featured_articles', 'bannerads'));
    }

    public function category(Category $category){
        $categories = Category::all();
        $bannerads = BannerAdvertisement::where('is_active', 'active')
        ->where('type', 'banner')
        ->inRandomOrder()
        ->first();
        return view('front.category', compact('category', 'categories', 'bannerads'));
    }

    public function author(Author $author){
        $categories = Category::all();
        $bannerads = BannerAdvertisement::where('is_active', 'active')
        ->where('type', 'banner')
        ->inRandomOrder()
        ->first();
        return view('front.author', compact('categories', 'author', 'bannerads'));
    }

    public function search(Request $request){

        $request->validate([
            'keyword' => ['required', 'string', 'max:255'],
        ]);

        $categories = Category::all();

        $keyword = $request->keyword;

        $articles = ArticleNews::with(['category', 'author'])
        ->where('name', 'like', '%' . $keyword . '%')->paginate(6);

        return view('front.search', compact('articles', 'keyword', 'categories'));
    }

    public function details(ArticleNews $articleNews){
        $categories = Category::all();

        $articles = ArticleNews::with(['category'])
        ->where('is_featured', 'not_featured')
        ->where('id', '!=', $articleNews->id)
        ->latest()
        ->take(3)
        ->get();

        $bannerads = BannerAdvertisement::where('is_active', 'active')
        ->where('type', 'banner')
        ->inRandomOrder()
        ->first();

        $square_ads = BannerAdvertisement::where('type', 'square')
        ->where('is_active', 'active')
        ->inRandomOrder()
        ->take(2)
        ->get();

        if($square_ads->count() < 2) {
            $square_ads_1 = $square_ads->first();
            // untuk menampilkan ads tidak sama dengan yang bawah menggunakan null
            $square_ads_2 = null;
            // jika menggunakan ini jika 1 di non active akan samaa iklan yang bawah dan atas
            // $square_ads_2 = $square_ads->first();
        }else {
            $square_ads_1 = $square_ads->get(0);
            $square_ads_2 = $square_ads->get(1);
        }
        
        $author_news = ArticleNews::where('author_id', $articleNews->author_id)
        ->where('id', '!=', $articleNews->id)
        ->inRandomOrder()
        ->get();

        return view('front.details', compact('author_news', 'square_ads_1', 'square_ads_2', 'articleNews', 'categories', 'articles', 'bannerads'));
    }
}
