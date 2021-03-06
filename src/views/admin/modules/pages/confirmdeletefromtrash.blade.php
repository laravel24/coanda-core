@extends('coanda::admin.layout.main')

@section('page_title', 'Confirm removal from trash')

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('pages') }}">Pages</a></li>
			<li>Confirm removal from trash</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">Confirm removal from trash <small>Pages</small></h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12">
	</div>
</div>

{{ Form::open(['url' => Coanda::adminUrl('pages/confirm-delete-from-trash')]) }}
<div class="row">
	<div class="col-md-12">
		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#trashedpages" data-toggle="tab">Pages</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="trashedpages">

					<div class="alert alert-danger">
						<i class="fa fa-exclamation-triangle"></i> Are you sure you want to permanently delete the following pages? Please note that any sub pages will also be removed.
					</div>

					@if ($pages->count() > 0)
						<table class="table table-striped">
						@foreach ($pages as $page)
							<tr class="status-{{ $page->status }}">
								<td>
									<input type="hidden" name="confirmed_remove_list[]" value="{{ $page->id }}">

									@if ($page->is_draft)
										<i class="fa fa-circle-o"></i>
									@else
										<i class="fa {{ $page->pageType()->icon() }}"></i>
									@endif
									{{ $page->name }}
								</td>
								<td>
									@foreach ($page->parents() as $parent)
										{{ $parent->name }} /
									@endforeach

									{{ $page->name }}

									<span class="pull-right">{{ $page->subTreeCount() }} sub pages will also be removed.</span>
								<td>
							</tr>
						@endforeach
						</table>

						{{ Form::button('I understand, please delete these pages permanently', ['name' => 'permanent_remove', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-danger']) }}
					@endif
				</div>
			</div>
		</div>
	</div>
</div>
{{ Form::close() }}
@stop
