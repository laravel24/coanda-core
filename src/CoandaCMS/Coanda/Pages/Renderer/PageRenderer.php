<?php namespace CoandaCMS\Coanda\Pages\Renderer;

use Coanda;
use View;

class PageRenderer {

    private $page;
    private $location;
    private $meta;
    private $data;
    private $template;
    private $layout;

    public function __construct($page, $location)
    {
        $this->page = $page;
        $this->location = $location;
    }

    public function render()
    {
        $this->checkPageStatus();

        $this->buildMeta();
        $this->buildPageData();

        $this->preRender();

        if ($this->checkForRedirect())
        {
            return $this->data;
        }

        $this->getTemplate();

        return $this->mergeWithLayout($this->renderPage());
    }

    private function renderPage()
    {
        return View::make($this->template, $this->data);
    }

    private function getTemplate()
    {
        $this->template = $this->page->pageType()->template($this->page->currentVersion(), $this->data);
    }

    private function preRender()
    {
        // Does the page type want to do anything before we carry on with the rendering?
        // e.g. Redirect, set some additional data variables
        $this->data = $this->page->pageType()->preRender($this->data);
    }

    private function checkForRedirect()
    {
        // Lets check if we got a redirect request back...
        if (is_object($this->data) && get_class($this->data) == 'Illuminate\Http\RedirectResponse')
        {
            return true;
        }

        return false;
    }

    private function mergeWithLayout($rendered_content)
    {
        // Get the layout template...
        $this->getLayout($this->page->currentVersion());

        // Give the layout the rendered page and the data, and it can work some magic to give us back a complete page...
        $layout_data = [
            'layout' => $this->layout,
            'content' => $rendered_content,
            'meta' => $this->meta,
            'breadcrumb' => ($this->location ? $this->location->breadcrumb() : []),
            'module' => 'pages',
            'module_identifier' => $this->page->id
        ];

        $content = $this->layout->render($layout_data);

        return $content;
    }

    private function getLayout($version)
    {
        $possible_layouts = [
            $version->layout_identifier,
            $version->page->pageType()->defaultLayout(),
        ];

        foreach ($possible_layouts as $possible_layout)
        {
            $this->layout = Coanda::layout()->layoutByIdentifier($possible_layout);

            if ($this->layout)
            {
                break;
            }
        }

        if (!$this->layout)
        {
            $this->layout = Coanda::module('layout')->defaultLayout();
        }
    }

    private function checkPageStatus()
    {
        if ($this->page->is_trashed || !$this->page->is_visible || $this->page->is_hidden)
        {
            App::abort('404');
        }
    }

    private function renderAttributes()
    {
        return $this->page->renderAttributes($this->location);
    }

    private function buildPageData()
    {
        $this->data = [
            'page_id' => $this->page->id,
            'version' => $this->page->current_version,
            'location_id' => ($this->location ? $this->location->id : false),
            'parent' => ($this->location ? $this->location->parent : false),
            'page' => $this->page,
            'attributes' => $this->renderAttributes(),
            'meta' => $this->meta,
            'slug' => $this->location ? $this->location->slug : '',
        ];
    }

    private function buildMeta()
    {
        $meta_title = $this->page->meta_page_title;

        $this->meta = [
            'title' => $meta_title !== '' ? $meta_title : $this->page->present()->name,
            'description' => $this->page->meta_description
        ];
    }

    private function checkForCache()
    {
        $cache_key = $this->generateCacheKey($url->type_id);

        if (Config::get('coanda::coanda.page_cache_enabled'))
        {
            if (Cache::has($cache_key))
            {
                return Cache::get($cache_key);
            }
        }
    }

    private function generateCacheKey($location_id)
    {
        $cache_key = 'location-' . $location_id;

        $all_input = \Input::all();

        // If we are viewing ?page=1 - then this is cached the same as without it...
        if (isset($all_input['page']) && $all_input['page'] == 1)
        {
            unset($all_input['page']);
        }

        $cache_key .= '-' . md5(var_export($all_input, true));

        return $cache_key;
    }

} 