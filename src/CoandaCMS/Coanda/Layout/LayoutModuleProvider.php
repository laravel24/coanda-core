<?php namespace CoandaCMS\Coanda\Layout;

use Route, App, Config, Coanda, View;
use CoandaCMS\Coanda\CoandaModuleProvider;
use Illuminate\Foundation\Application;

/**
 * Class LayoutModuleProvider
 * @package CoandaCMS\Coanda\Layout
 */
class LayoutModuleProvider implements CoandaModuleProvider {

    /**
     * @var string
     */
    public $name = 'layout';

    /**
     * @var array
     */
    private $layouts = [];

    /**
     * @var array
     */
    private $layouts_by_page_type = [];

    /**
     * @param \CoandaCMS\Coanda\Coanda $coanda
     * @return mixed|void
     */
    public function boot(\CoandaCMS\Coanda\Coanda $coanda)
	{
		$this->loadLayouts();
	}

    /**
     *
     */
    private function loadLayouts()
	{
		$layouts = Config::get('coanda::coanda.layouts');

		foreach ($layouts as $layout_class)
		{
			if (class_exists($layout_class))
			{
				$layout = new $layout_class;

				$this->layouts[$layout->identifier()] = $layout;

                $this->associateLayoutsWithPageTypes($layout);
			}
		}
	}

    /**
     * @param $layout
     */
    private function associateLayoutsWithPageTypes($layout)
    {
        if (count($layout->pageTypes()) > 0)
        {
            foreach ($layout->pageTypes() as $page_type)
            {
                $this->layouts_by_page_type[$page_type][] = $layout;
            }
        }
        else
        {
            foreach (Coanda::pages()->allPageTypes() as $page_type)
            {
                $this->layouts_by_page_type[$page_type->identifier()][] = $layout;
            }
        }
    }


    /**
     * @return array
     */
    public function layouts()
	{
		return $this->layouts;
	}

    /**
     * @param $identifier
     * @return mixed
     * @throws Exceptions\LayoutNotFound
     */
    public function layoutByIdentifier($identifier)
	{
		if (array_key_exists((string) $identifier, $this->layouts))
		{
			return $this->layouts[$identifier];
		}

        return false;
	}

    /**
     * @param $page_type
     * @return array
     */
    public function layoutsByPageType($page_type)
	{
		if (array_key_exists($page_type, $this->layouts_by_page_type))
		{
			return $this->layouts_by_page_type[$page_type];
		}

		return [];
	}

    /**
     * @param $for_identifier
     * @return mixed
     */
    public function layoutFor($for_identifier)
    {
        $layout_mappings = Config::get('coanda::coanda.layout_mapping');

        if (is_array($layout_mappings))
        {
            if (array_key_exists($for_identifier, $layout_mappings))
            {
                return $this->layouts[$layout_mappings[$for_identifier]];
            }
        }

        $default_layout = Config::get('coanda::coanda.default_layout');

        return $this->layouts[$default_layout];
    }

    /**
     * @return mixed
     */
    public function defaultLayout()
    {
        return $this->layouts[Config::get('coanda::coanda.default_layout')];
    }

    /**
     *
     */
    public function adminRoutes()
	{
	}

    /**
     *
     */
    public function userRoutes()
	{
	}

    /**
     * @param Application $app
     * @return mixed
     */
    public function bindings(Application $app)
	{
        $app->bind('CoandaCMS\Coanda\Layout\Repositories\LayoutRepositoryInterface', 'CoandaCMS\Coanda\Layout\Repositories\Eloquent\EloquentLayoutRepository');
	}

    /**
     * @param $permission
     * @param $parameters
     * @param array $user_permissions
     * @return mixed|void
     */
    public function checkAccess($permission, $parameters, $user_permissions = [])
	{
	}

    /**
     * @param $coanda
     * @return mixed|void
     */
    public function buildAdminMenu($coanda)
    {
    }
}