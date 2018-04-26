<div class="form-group form-element-image-cropper {{ $errors->has($name) ? 'has-error' : '' }}">
	<label for="{{ $name }}" class="control-label">
		{{ $label }}

		@if($required)
			<span class="form-element-required">*</span>
		@endif
	</label>
	@include(AdminTemplate::getViewPath('form.element.partials.helptext'))
	<element-image-cropper
			url="{{ route('admin.form.element.image-cropper', [
				'adminModel' => AdminSection::getModel($model)->getAlias(),
				'field' => $path,
				'id' => $model->getKey()
			]) }}"
			value="{{ ($value) ? $value : '' }}"
			:readonly="{{ $readonly ? 'true' : 'false' }}"
			name="{{ $name }}"
			model="{{ AdminSection::getModel($model)->getAlias() }}"
			width="{{ $width }}"
			height="{{ $height }}"
			:dataurl="{{ $data_url ? 'true' : 'false' }}"
			inline-template
	>
		<div>
			<div v-if="errors.length" class="alert alert-warning">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close" @click="closeAlert()">
				<span aria-hidden="true">&times;</span>
				</button>
				<p v-for="error in errors"><i class="fa fa-hand-o-right" aria-hidden="true"></i> @{{ error }}</p>
			</div>
			<div class="form-element-files clearfix" v-if="has_value">
				<div class="form-element-files__item">
					<a :href="image" class="form-element-files__image" data-toggle="lightbox">
						<img :src="image"/>
					</a>
					<div class="form-element-files__info">
						<a href="#" v-if="!is_default" class="btn btn-primary btn-xs pull-right crop-btn" @click.prevent="initCrop()">
							<i class="fa fa-crop"></i>
						</a>
						<a :href="image_crop" class="btn btn-default btn-xs pull-right" target="_blank">
							<i class="fa fa-cloud-download"></i>
						</a>
						<button v-if="has_value && !readonly" type="button" class="btn btn-danger btn-xs" @click.prevent="remove()">
							<i class="fa fa-times"></i> {{ trans('sleeping_owl::lang.image.remove') }}
						</button>
					</div>
				</div>
			</div>
			<div v-if="!readonly">
				<div class="btn btn-primary upload-button">
					<i :class="uploadClass"></i> {{ trans('sleeping_owl::lang.image.browse') }}
				</div>
			</div>
			<input :name="name" type="hidden" :value="val">
			@include(AdminTemplate::getViewPath('form.element.partials.cropper_modal'))
		</div>
	</element-image-cropper>
	<div class="errors">
		@include(AdminTemplate::getViewPath('form.element.partials.errors'))
	</div>
</div>
