Vue.component('element-file-upload', Vue.extend({
	props: {
		url: {
			required: true
		},
		callback: {
			default: ''
		},
	},
	data () {
		return {
			errors: [],
			files: [],
			link: '',
			uploading: false,
		}
	},
	mounted () {
		this.initUpload()
	},
	methods: {
		initUpload () {
			let self = this,
				container = $(self.$el.parentNode),
				button = container.find('.upload-button');
			button.dropzone({
				url: this.url,
				method: 'POST',
				uploadMultiple: false,
				previewsContainer: false,
				parallelUploads: true,
				acceptedFiles: 'image/*',
				dictDefaultMessage: '',
				createImageThumbnails: false,
				sending () {
					self.closeAlert();
					self.uploading = true;
					self.closeAlert()
				},
				success (file, response) {
					self.uploading = false;
					self.initCallback(response)
				},
				error (file, response) {
					if (_.isArray(response.errors)) self.errors = response.errors;
					self.uploading = false;
				},
			});
		},
		sendURL () {
			let self = this;
			self.uploading = true;
			let formData = new FormData();
			formData.append('_token', Admin.token);
			formData.append('url', self.link);
			$.ajax(self.url, {
				method: "POST",
				data: formData,
				processData: false,
				contentType: false,
				success: function (response) {
					self.uploading = false;
					self.link = '';
					self.closeAlert();
					self.initCallback(response)
				},
				error: function (jqXHR) {
					let response = JSON.parse(jqXHR.responseText);
					if (_.isArray(response.errors)) self.errors = response.errors;
					self.uploading = false;
				}
			});
		},
		initCallback(response) {
			let self = this;
			if (self.callback.length > 0) {
				let fn = window[self.callback];
				if (typeof fn === 'function') {
					try {
						fn(response);
					} catch (e) {
						self.$set(self.errors, ["Incorrect callback. " + e.message]);
					}
				}
			}
		},
		closeAlert () {
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
		isLink() {
			let url = '(?:(?:ht|f)tps?://)?(?:[\\-\\w]+:[\\-\\w]+@)?(?:[0-9a-z][\\-0-9a-z]*[0-9a-z]\\.)+[a-z]{2,6}(?::\\d{1,5})?(?:[?/\\\\#][?!^$.(){}:|=[\\]+\\-/\\\\*;&~#@,%\\wА-Яа-я]*)?';
			let reg = new RegExp('^' + url + '$', 'i');
			return reg.test(this.link);
		},
	}
}));
