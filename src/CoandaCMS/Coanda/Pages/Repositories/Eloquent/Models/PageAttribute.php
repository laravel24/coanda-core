<?php namespace CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models;

use Eloquent, Coanda;

class PageAttribute extends Eloquent {

	public $timestamps = false;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'pageattributes';

	/**
	 * Returns the version for this attribute
	 * @return CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageVersion
	 */
	public function version()
	{
		return $this->belongsTo('CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\PageVersion', 'page_version_id');
	}

	/**
	 * Returns the page for this attribute
	 * @return CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\Page
	 */
	public function page()
	{
		return $this->version->page;
	}

	/**
	 * Returns the type for the page
	 * @return CoandaCMS\Coanda\Pages\Repositories\Eloquent\Models\Page
	 */
	public function pageType()
	{
		return $this->page()->pageType();
	}

	/**
	 * Returns the name for this attribute, via the type definition
	 * @return string
	 */
	public function name()
	{
		return $this->pageType()->attributes()[$this->identifier]['name'];
	}

	/**
	 * Returns if the attribute is required
	 * @return boolean
	 */
	public function isRequired()
	{
		return $this->pageType()->attributes()[$this->identifier]['required'];
	}

	/**
	 * Calls the isRequired method
	 * @return boolean
	 */
	public function getIsRequiredAttribute()
	{
		return $this->isRequired();
	}

	/**
	 * Calls the name method
	 * @return string
	 */
	public function getNameAttribute()
	{
		return $this->name();
	}

	/**
	 * Get the type for this type
	 * @return
	 */
	public function type()
	{
		return Coanda::getPageAttributeType($this->type);
	}

	/**
	 * Get the data from the type
	 * @return array
	 */
	public function typeData()
	{
		// Let the type do whatever with the attribute to return the data required...
		return $this->type()->data($this);
	}

	/**
	 * Calls the typeData method
	 * @return array
	 */
	public function getTypeDataAttribute()
	{
		return $this->typeData();
	}

	/**
	 * Stores the data provided
	 * @param  array $data
	 * @return void
	 */
	public function store($data)
	{
		// Let the type class validate/manipulate/store the data..
		$this->type()->store($this, $data);
	}

}