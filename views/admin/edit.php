<?php  $view->style('codemirror'); $view->script('form-edit', 'bixie/formmaker:app/bundle/form-edit.js', ['vue', 'editor', 'formmaker-formmakerfields', 'uikit-nestable']); ?>

<div id="form-edit" v-cloak>
	<form class="uk-form" name="form" v-on="valid: save">

		<div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
			<div data-uk-margin>

				<h2 class="uk-margin-remove" v-if="formitem.id">{{ 'Edit form' | trans }} <em>{{
						formitem.title | trans }}</em> <a v-attr="href: $url.route(formitem.url)" target="_blank"
														 class="uk-icon-external-link uk-icon-hover uk-text-small uk-margin-small-left"
														 title="{{ 'Preview form' | trans }}"
														 data-uk-tooltip="{delay:500}"></a></h2>

				<h2 class="uk-margin-remove" v-if="!formitem.id">{{ 'Add form' | trans }}</h2>

			</div>
			<div data-uk-margin>

				<a class="uk-button uk-margin-small-right" v-attr="href: $url.route('admin/formmaker')">{{ formitem.id ?
					'Close' :
					'Cancel' | trans }}</a>
				<button class="uk-button uk-button-primary" type="submit">{{ 'Save' | trans }}</button>

			</div>
		</div>

		<ul class="uk-tab" v-el="tab">
			<li><a>{{ 'General' | trans }}</a></li>
			<li><a>{{ 'Fields' | trans }}</a></li>
			<li><a>{{ 'Appearance' | trans }}</a></li>
			<li><a>{{ 'Submission' | trans }}</a></li>
			<li><a>{{ 'Email' | trans }}</a></li>
		</ul>

		<div class="uk-switcher uk-margin" v-el="content">
			<div>
				<div class="uk-grid">
				    <div class="uk-width-medium-3-4">
						<formbasic></formbasic>
				    </div>
				    <div class="uk-width-medium-1-4">
						<h3>{{ 'Plugin code' | trans }}</h3>
						<p>{{ 'Add this code to any Pagekit content to show the form.' | trans }}</p>
						<kbd>(formmaker){"id":"{{ formitem.id }}"}</kbd>
						<p>{{ 'Optionally, you can hide the title of the form.' | trans }}</p>
						<kbd>(formmaker){"id":"{{ formitem.id }}", "hide_title":"1"}</kbd>
					</div>
				</div>
			</div>
			<div>
				<formfields v-ref="formfields"></formfields>
			</div>
			<div>
				<appearance></appearance>
			</div>
			<div>
				<submission></submission>
			</div>
			<div>
				<emailsettings></emailsettings>
			</div>
		</div>

	</form>

	<v-modal v-ref="editmodal" large>
		<fieldedit formitem="{{@ formitem }}" fieldid="{{ editid }}"></fieldedit>
	</v-modal>
</div>

