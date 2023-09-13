<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Term;
use App\Models\Article;
use App\Models\Article_Title;
use App\Models\Article_Content;
use App\Models\Language;
use App\Models\Term_Name;
use App\Models\Term_Title;
use App\Models\Term_Content;
use App\Models\Term_Sound;
use App\Models\Category_Name;
use App\Traits\DataFormController;

class HomeController extends Controller
{
    use DataFormController;

    public function getTermIndex() {
        return view('site.term');
    }

    public function getArticleIndex() {
        return view('site.article');
    }

    public function getTerm(Request $request) {
        $lang = Language::where('symbol', 'like', '%' . $request->lang . '%')->first();

        $term = Term::find($request->id);

        $term_name = Term_Name::where('language_id', $lang->id)->where('term_id', $term->id)->first();
        $term->name = $term_name->term;
        $term_title = Term_Title::where('language_id', $lang->id)->where('term_id', $term->id)->first();
        $term->title = $term_title->title;
        $term_content = Term_Content::where('language_id', $lang->id)->where('term_id', $term->id)->first();
        $term->content = $term_content->content;
        $term_sound = Term_Sound::where('language_id', $lang->id)->where('term_id', $term->id)->first();
        $term->sound = $term_sound ? $term_sound->sound : '';

        return $this->jsonData(true, true, '', [], $term);
    }

    public function getArticle(Request $request) {
        $lang = Language::where('symbol', 'like', '%' . $request->lang . '%')->first();

        $article = Article::find($request->id);

        $article_title = Article_Title::where('language_id', $lang->id)->where('article_id', $article->id)->first();
        $article->title = $article_title->title;
        $article_content = Article_Content::where('language_id', $lang->id)->where('article_id', $article->id)->first();
        $article->content = $article_content->content;

        return $this->jsonData(true, true, '', [], $article);
    }

    public function getLatestTerms(Request $request) {
        $lang = Language::where('symbol', 'like', '%' . $request->lang . '%')->first();

        $terms =  Term::with('category')->latest()->orderby('id', 'desc')->take(12)->get();

        foreach ($terms as $term) {
            $category_name = Category_Name::where('category_id', $term->category->id)->where('language_id', $lang->id)->first();
            $term->category_name = $category_name->name;
            $term_name = Term_Name::where('language_id', $lang->id)->where('term_id', $term->id)->first();
            $term->name = $term_name->term;
            $term_title = Term_Title::where('language_id', $lang->id)->where('term_id', $term->id)->first();
            $term->title = $term_title->title;
            $term_content = Term_Content::where('language_id', $lang->id)->where('term_id', $term->id)->first();
            $term->content = $term_content->content;
            $term_sound = Term_Sound::where('language_id', $lang->id)->where('term_id', $term->id)->first();
            $term->sound = $term_sound ? $term_sound->sound : '';
        }

        return $this->jsonData(true, true, '', [], $terms);
    }

    public function getLatestLatest(Request $request) {
        $lang = Language::where('symbol', 'like', '%' . $request->lang . '%')->first();

        $articles =  Article::with('category')->latest()->orderby('id', 'desc')->take(12)->get();

        foreach ($articles as $article) {
            $category_name = Category_Name::where('category_id', $article->category->id)->where('language_id', $lang->id)->first();
            $article->category_name = $category_name->name;
            $article_title = Article_Title::where('language_id', $lang->id)->where('article_id', $article->id)->first();
            $article->title = $article_title->title;
            $articlecontent = Article_Content::where('language_id', $lang->id)->where('article_id', $article->id)->first();
            $article->content = $articlecontent->content;
        }

        return $this->jsonData(true, true, '', [], $articles);
    }
}
