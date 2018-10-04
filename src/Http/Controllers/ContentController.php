<?php
namespace App\Http\Controllers\Piclommerce;

use App\Http\Controllers\Controller;
use Artesaos\SEOTools\Facades\SEOMeta;
use \Artesaos\SEOTools\Facades\OpenGraph;
use Piclou\Piclommerce\Http\Entities\Content;

class ContentController extends Controller
{
    /**
     * @param string $slug
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function index(string $slug, int $id)
    {
        $content = Content::select('id','slug','name','description','content_category_id', 'on_homepage', 'image')
            ->where('published', 1)
            ->where('id', $id)
            ->first();

        if($content->slug != $slug) {
            return redirect(
                Route('content.index',[
                    'slug' => $content->slug,
                    'id' => $content->id
                ]),301);
        }

        //Same categories
        $category = $content->ContentCategory;
        $contentList = null;
        if(!is_null($category)) {
            $contentList = $category->Contents;
        }

        $url = Route('content.index',['slug' => $content->slug,'id' => $content->id ]);
        $title = ($content->seoTitle)?$content->seoTitle:$content->name . " - " . setting("generals.seoTitle");
        $description =($content->seoDescription)?$content->seoDescription:$content->name . " - " .  setting("generals.seoDescription");

        // Arianne
        $arianne = [
            __('piclommerce::web.navigation_home') => '/',
            $content->name =>$url,
        ];
        SEOMeta::setCanonical($url);
        SEOMeta::setTitle($title);
        SEOMeta::setDescription($description);
        OpenGraph::setTitle($title);
        OpenGraph::setDescription($description);
        OpenGraph::setUrl($url);
        OpenGraph::setSiteName(setting("generals.websiteName"));
        if($content->image) {
            OpenGraph::addImage(asset($content->getMedias("image")['target_path']));
        }

        return view("piclommerce::content.index",compact(
            "content",
            "category",
            "contentList",
            "arianne"
        ));
    }
}
