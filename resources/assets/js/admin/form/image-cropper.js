import Cropper from './../../libs/cropper';

Vue.component('element-image-cropper', Vue.extend({
	props: {
		url: {
			required: true
		},
		value: {
			default: ''
		},
		readonly: {
			type: Boolean,
			default: false
		},
		name: {
			type: String,
			required: true
		},
		model: {
			type: String,
			required: true
		},
		width: {
			type: String,
			required: true
		},
		height: {
			type: String,
			required: true
		},
		dataurl: {
			type: Boolean,
			default: false
		},
	},
	data() {
		return {
			errors: [],
			uploading: false,
			cropper: {},
			scale_x: 1,
			scale_y: 1,
			now: Date.now(),
			val: false,
		}
	},
	mounted() {
		this.val = this.value;
		Cropper.noConflict();
		this.initModalListener();
		this.initUpload();
	},
	methods: {
		initUpload() {
			let self = this,
				container = $(self.$el.parentNode),
				button = container.find('.upload-button');
			if (self.dataurl === true) {
				button.dropzone({
					url: this.url,
					uploadMultiple: false,
					previewsContainer: false,
					acceptedFiles: 'image/*',
					dictDefaultMessage: '',
					thumbnailWidth: null,
					thumbnailHeight: 300,
					thumbnailMethod: 'contain',
					thumbnail: function (file, dataUrl) {
						self.val = dataUrl;
						self.initCrop();
					}
				});
			} else {
				button.dropzone({
					url: this.url,
					method: 'POST',
					uploadMultiple: false,
					previewsContainer: false,
					acceptedFiles: 'image/*',
					dictDefaultMessage: '',
					sending() {
						self.uploading = true;
						self.closeAlert()
					},
					success(file, response) {
						self.val = response.value_s;
					},
					error(file, response) {
						if (_.isArray(response.errors)) {
							self.$set(self.errors, response.errors);
						}
					},
					complete() {
						self.uploading = false;
					}
				});
			}
		},
		initCrop() {
			let self = this,
				container = $(self.$el.parentNode),
				cropper_modal = container.find('.modal'),
				_image = cropper_modal.find('img');
			cropper_modal.modal('toggle');
		},
		initModalListener() {
			let self = this,
				container = $(self.$el.parentNode),
				cropper_modal = container.find('.modal'),
				_image = cropper_modal.find('img');
			cropper_modal.on('shown.bs.modal', function () {
				self.cropper = new Cropper(_image[0], {
					viewMode: 0,
					aspectRatio: parseInt(self.width) / parseInt(self.height),
					// minCanvasWidth: parseInt(self.width),
					// minCanvasHeight: parseInt(self.height),
				});
			}).on('hidden.bs.modal', function () {
				self.cropper.destroy();
				self.cropper = {};
				$('.cropper-container').remove();
				cropper_modal.find('img.cropper-hidden').removeClass('cropper-hidden');
			});
		},
		setDragMode(value) {
			let self = this;
			self.cropper.setDragMode(value);
		},
		zoom(value) {
			let self = this;
			self.cropper.zoom(value);
		},
		move(left, top) {
			let self = this;
			self.cropper.move(left, top);
		},
		rotate(degree) {
			let self = this;
			self.cropper.rotate(degree);
		},
		scaleX() {
			let self = this;
			self.cropper.scaleX((self.scale_x * -1));
			self.scale_x = (self.scale_x * -1);
		},
		scaleY() {
			let self = this;
			self.cropper.scaleY((self.scale_y * -1));
			self.scale_y = (self.scale_y * -1);
		},
		reset() {
			let self = this;
			self.cropper.reset();
		},
		clear() {
			let self = this;
			self.cropper.clear();
		},
		crop() {
			let self = this;
			if (self.dataurl === true) {
				self.val = self.cropper.getCroppedCanvas({
					width: self.width,
					height: self.height,
					imageSmoothingEnabled: true,
					imageSmoothingQuality: 'high',
					fillColor: '#fff'
				}).toDataURL('image/jpeg');
				$(self.$el.parentNode).find('.modal').modal('toggle');
			} else {
				self.cropper.getCroppedCanvas({
					width: self.width,
					height: self.height,
					imageSmoothingEnabled: true,
					imageSmoothingQuality: 'high',
					fillColor: '#fff'
				}).toBlob(function (blob) {
					let formData = new FormData();
					formData.append('croppedImage', blob);
					formData.append('_token', Admin.token);
					formData.append('image_path', self.val);
					formData.append('model', self.model);
					$.ajax('/admin/renew-image', {
						method: "POST",
						data: formData,
						processData: false,
						contentType: false,
						success: function (response) {
							if (response.value) {
								self.now = Date.now();
								self.val = response.value;
							}
							$(self.$el.parentNode).find('.modal').modal('toggle');
						},
						error: function () {
							alert('Ошибка сохранения новой миниатюры!')
						}
					});
				});
			}
		},
		remove() {
			let self = this;
			Admin.Messages.confirm(trans('lang.message.are_you_sure')).then(() => {
				self.val = false;
			});
		},
		closeAlert() {
			this.$set(this.errors, []);
		}
	},
	computed: {
		uploadClass() {
			if (!this.uploading) {
				return 'fa fa-upload';
			}
			return 'fa fa-spinner fa-spin'
		},
		has_value() {
			return this.val.length > 0
		},
		is_default() {
			return this.val.search('default') > 0;
		},
		image() {
			return (typeof this.val === 'string' && this.val.length > 0)
				? (this.dataurl === true)
					? this.val
					: Admin.Url.app(this.val + '?' + this.now)
				: Admin.Url.upload(this.val);
		},
		image_crop() {
			return this.image.replace('_s.', '_o.')
		},
	}
}));
