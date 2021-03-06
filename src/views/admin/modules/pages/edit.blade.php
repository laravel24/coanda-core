@extends('coanda::admin.layout.main')

@section('page_title', 'Edit page')

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('pages') }}">Pages</a></li>

			@foreach ($version->parents() as $parent)
				<li><a href="{{ Coanda::adminUrl('pages/view/' . $parent->id) }}">{{ $parent->name }}</a></li>
			@endforeach

			<li>Edit page</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">
			Edit page (version #{{ $version->version }})
			<small>
				<i class="fa {{ $version->page->pageType()->icon() }}"></i>
				{{ $version->page->pageType()->name() }}
			</small>
		</h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12"></div>
</div>


{{ Form::open(['url' => Coanda::adminUrl('pages/editversion/' . $version->page_id . '/' . $version->version), 'files' => true]) }}
<div class="edit-container">

	@if (Session::has('page_saved'))
		<div class="alert alert-success">
			Saved
		</div>
	@endif

	@if (Session::has('error'))
		<div class="alert alert-danger">
			Error
		</div>
	@endif

	<div class="row">
		<div class="col-md-8">

			<ul class="nav nav-tabs">
				<li @if (!Session::has('layout_chosen') && !Session::has('template_chosen')) class="active" @endif><a href="#attributes" data-toggle="tab">Content</a></li>
				
				@if (count($version->availableTemplates()) > 0)
					<li @if (Session::has('template_chosen')) class="active" @endif><a href="#template" data-toggle="tab">Template</a></li>
				@endif

				<li @if (Session::has('layout_chosen')) class="active" @endif><a href="#layout" data-toggle="tab">Layout</a></li>
				@if ($version->page->show_meta)
					<li><a href="#meta" data-toggle="tab">Meta</a></li>
				@endif
			</ul>

			<div class="tab-content edit-container">
				<div class="tab-pane @if (!Session::has('layout_chosen') && !Session::has('template_chosen')) active @endif" id="attributes">
					@foreach ($version->attributes as $attribute)

						@include($attribute->type()->edit_template(), [ 'attribute_definition' => $attribute->definition, 'old_input' => (isset($old_attribute_input[$attribute->identifier]) ? $old_attribute_input[$attribute->identifier] : false), 'attribute_identifier' => $attribute->identifier, 'attribute_name' => $attribute->name, 'invalid_fields' => isset($invalid_fields['attributes']) ? $invalid_fields['attributes'] : [], 'is_required' => $attribute->is_required, 'prefill_data' => $attribute->type_data ])

						@if ($attribute->generates_slug)
							@section('footer')
								<script type="text/javascript">
									$('#attribute_{{ $attribute->identifier }}').slugify('.slugiwugy');
								</script>
							@append
						@endif

					@endforeach
				</div>

				@if (count($version->availableTemplates()) > 0)
					<div class="tab-pane @if (Session::has('template_chosen')) active @endif" id="template">

						@if (Session::has('template_chosen'))
							<div class="alert alert-success">
								Template chosen
							</div>
						@endif

						<div class="form-group">
							<label class="control-label" for="template_identifier">Template</label>

							<div class="row">
								<div class="col-xs-10">
									<select name="template_identifier" id="template_identifier" class="form-control">
										<option value=""></option>
										@foreach ($version->availableTemplates() as $template_identifier => $template_name)
											<option @if ($version->template_identifier == $template_identifier) selected="selected" @endif value="{{ $template_identifier }}">{{ $template_name }}</option>
										@endforeach
									</select>
								</div>
								<div class="col-xs-2">
									{{ Form::button('Choose', ['name' => 'choose_template', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-default btn-block']) }}
								</div>
							</div>

						</div>

					</div>
				@endif

				<div class="tab-pane @if (Session::has('layout_chosen')) active @endif" id="layout">

					@if (Session::has('layout_chosen'))
						<div class="alert alert-success">
							Layout chosen
						</div>
					@endif

					<div class="form-group">
						<label class="control-label" for="layout_identifier">Layout</label>

						<div class="row">
							<div class="col-xs-10">
								<select name="layout_identifier" id="layout_identifier" class="form-control">
									<option value=""></option>
									@foreach ($layouts as $layout)
										<option @if ($version->layout_identifier == $layout->identifier()) selected="selected" @endif value="{{ $layout->identifier() }}">{{ $layout->name() }}</option>
									@endforeach
								</select>
							</div>
							<div class="col-xs-2">
								{{ Form::button('Choose', ['name' => 'choose_layout', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-default btn-block']) }}
							</div>
						</div>

					</div>
				</div>

				@if ($version->page->show_meta)
					<div class="tab-pane" id="meta">
						<div class="form-group @if (isset($invalid_fields['meta_page_title'])) has-error @endif">
							<label class="control-label" for="meta_page_title">Page title</label>
					    	<input type="text" class="form-control" id="meta_page_title" name="meta_page_title" value="{{ Input::old('meta_page_title', $version->meta_page_title) }}">

						    @if (isset($invalid_fields['meta_page_title']))
						    	<span class="help-block">{{ $invalid_fields['meta_page_title'] }}</span>
						    @endif
						</div>
						<div class="form-group @if (isset($invalid_fields['meta_description'])) has-error @endif">
							<label class="control-label" for="meta_description">Meta description</label>
					    	<textarea type="text" class="form-control" id="meta_description" name="meta_description">{{ Input::old('meta_description', $version->meta_description) }}</textarea>

						    @if (isset($invalid_fields['meta_description']))
						    	<span class="help-block">{{ $invalid_fields['meta_description'] }}</span>
						    @endif
						</div>
					</div>
				@endif

			</div>

			<div class="buttons">
				{{ Form::button('Save', ['name' => 'save', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-default']) }}
				{{ Form::button('Save & Exit', ['name' => 'save_exit', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-default']) }}
				{{ Form::button('Discard draft', ['name' => 'discard', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-default']) }}
			</div>

			<div class="publish-options">
				@if (count($publish_handlers) > 1)

					<h2>Publish options</h2>

					@if (Session::has('missing_publish_handler'))
						<div class="alert alert-danger">
							Please choose how you would like this page to be published.
						</div>
					@endif

					@foreach ($publish_handlers as $publish_handler)
						<div class="row">
							<div class="col-sm-6">
								<div class="radio">
									<label>
										<input type="radio" name="publish_handler" id="publish_handler_{{ $publish_handler->identifier }}" value="{{ $publish_handler->identifier }}" {{ (Input::old('publish_handler', $default_publish_handler) == $publish_handler->identifier) ? ' checked="checked"' : '' }}>
										{{ $publish_handler->name }}
									</label>
								</div>
							</div>
							<div class="col-sm-6">
								@include($publish_handler->template, [ 'publish_handler' => $publish_handler, 'publish_handler_invalid_fields' => $publish_handler_invalid_fields ])
							</div>
						</div>
					@endforeach
				@else
					<input type="hidden" name="publish_handler" value="{{ $publish_handlers[array_keys($publish_handlers)[0]]->identifier }}">
					@include($publish_handlers[array_keys($publish_handlers)[0]]->template, [ 'publish_handler' => $publish_handlers[array_keys($publish_handlers)[0]], 'publish_handler_invalid_fields' => $publish_handler_invalid_fields ])
				@endif
			</div>

			<div class="buttons">
				{{ Form::button('Publish', ['name' => 'publish', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-success']) }}
			</div>				

		</div>
		<div class="col-md-4">

			<ul class="nav nav-tabs">
				<li class="active"><a href="#details" data-toggle="tab">Details</a></li>
				<li><a href="#preview" data-toggle="tab">Preview @if ($version->comments->count() > 0) <span class="label label-info">{{ $version->comments->count() }} comments</span> @endif</a></li>
			</ul>

			<div class="edit-container tab-content">

				<div class="tab-pane active" id="details">
					@if (!$version->page->is_home)

						<div class="form-group">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="is_hidden" value="yes" @if ($version->is_hidden) checked="checked" @endif>
									Hide page <i class="pull-right fa fa-info-circle show-tooltip" data-toggle="tooltip" data-placement="top" title="Hiding the page will mean it is no longer accessible."></i>
								</label>
							</div>
						</div>

						<div class="form-group">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="is_hidden_navigation" value="yes" @if ($version->is_hidden_navigation) checked="checked" @endif>
									Hide from navigation <i class="pull-right fa fa-info-circle show-tooltip" data-toggle="tooltip" data-placement="top" title="Hiding the page from navigation will mean the page doesn't appear in any lists or navigation, but can still be accessed directly if the URL is known or linked to."></i>
								</label>
							</div>
						</div>

                        <div class="well well-sm slug-box">
							@if (Session::has('parent_changed'))
								<div class="alert alert-success">
									Parent page updated
								</div>
							@endif

                        	<div class="form-group">
								<label class="control-label">Parent Page</label>

                                <div class="input-group">
                                    <input type="text" class="form-control" id="parent_page_name" value="{{ $version->parent_page_id == 0 ? 'Top level' : $version->parent->name }}" disabled="disabled">
                                    <span class="input-group-btn">
                                    	{{ Form::button('Change', ['name' => 'choose_new_parent_page', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-default']) }}
									</span>
                                </div>

								<input type="hidden" name="parent_page_id" value="{{ $version->parent_page_id }}">
                        	</div>

                        	<div style="height: 5px;"></div>

                            <div class="form-group @if (isset($invalid_fields['slug'])) has-error @endif">

                                <p><em>{{ $version->parent_slug }}/</em></p>

                                <div class="input-group">
                                    <input type="text" class="form-control slugiwugy" id="slug" name="slug" value="{{ (Input::old('slug') && Input::old('slug') !== '') ? Input::old('slug') : $version->slug }}">
                                    <span class="input-group-addon refresh-slug"><i class="fa fa-refresh"></i></span>
                                </div>

                                @if (isset($invalid_fields['slug']))
                                    <span class="help-block">{{ $invalid_fields['slug'] }}</span>
                                @endif

                            </div>
                        </div>
					@endif

					@set('visible_dates_old', Input::old('visible_dates'))

					<div class="row">
						<input type="hidden" name="date_format" value="d/m/Y H:i">
						<div class="col-md-6">
							<div class="form-group @if (isset($invalid_fields['visible_dates_from'])) has-error @endif">
								<label class="control-label" for="visible_dates_from">Visible from</label>
								<div class="input-group datetimepicker" data-date-format="DD/MM/YYYY HH:mm">
									<input type="text" class="date-field form-control" id="visible_dates_from" name="visible_dates[from]" value="{{ isset($visible_dates_old['from']) ? $visible_dates_old['from'] : $version->format('visible_from') }}">
									<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
								</div>
							    @if (isset($invalid_fields['visible_dates_from']))
							    	<span class="help-block">{{ $invalid_fields['visible_dates_from'] }}</span>
							    @endif
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group @if (isset($invalid_fields['visible_dates_to'])) has-error @endif">
								<label class="control-label" for="visible_dates_to">Visible to</label>
								<div class="input-group datetimepicker" data-date-format="DD/MM/YYYY HH:mm">
									<input type="text" class="date-field form-control" id="visible_dates_to" name="visible_dates[to]" value="{{ isset($visible_dates_old['to']) ? $visible_dates_old['to'] : $version->format('visible_to') }}">
									<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
								</div>
							    @if (isset($invalid_fields['visible_dates_to']))
							    	<span class="help-block">{{ $invalid_fields['visible_dates_to'] }}</span>
							    @endif
							</div>
						</div>
					</div>
				</div>

				<div class="tab-pane" id="preview">
					<div class="form-group">
						<label class="control-label" for="preview">Preview URL</label>
						<div class="input-group">
							<input type="text" class="form-control select-all" id="preview" name="preview" value="{{ url($version->preview_url) }}" readonly>
							<span class="input-group-addon"><a class="new-window" href="{{ url($version->preview_url) }}"><i class="fa fa-share-square-o"></i></a></span>
						</div>
					</div>

					@if ($version->comments->count() > 0)
						<h2>Comments</h2>
						@foreach ($version->comments as $comment)
							<div class="well well-sm version-comment">
								<p class="lead">"{{ nl2br($comment->comment) }}"</p>
								<div class="pull-right">{{ $comment->format('created_at') }} from <strong>{{ $comment->name }}</strong></div>
								<div class="clearfix"></div>
							</div>
						@endforeach
					@endif

				</div>
			</div>

		</div>
	</div>
</div>
{{ Form::close() }}

@stop
