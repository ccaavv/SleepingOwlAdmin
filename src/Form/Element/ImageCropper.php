<?php

	namespace SleepingOwl\Admin\Form\Element;

	use Illuminate\Http\UploadedFile;
	use Illuminate\Validation\Validator;

	class ImageCropper extends File
	{
		/**
		 * @var string
		 */
		protected static $route = 'image-cropper';

		/**
		 * @var \Closure
		 */
		protected $saveCallback;

		/**
		 * @var array
		 */
		protected $uploadValidationRules = ['required', 'image'];

		/**
		 * After save callback.
		 *
		 * @var
		 */
		protected $afterSaveCallback;
		/**
		 * @var string
		 */
		protected $view = 'form.element.image_cropper';

		protected $width;

		protected $height;

		protected $data_url = false;

		public function __construct($path, $label = null, $model, $data_url = false)
		{
			$this->width = config($model . '_image_width');
			$this->height = config($model . '_image_height');
			if($data_url) $this->data_url = true;
			parent::__construct($path, $label);
		}

		/**
		 * @param Validator $validator
		 */
		public function customValidation(Validator $validator)
		{
			$validator->after(function ($validator) {
				/** @var \Illuminate\Http\UploadedFile $file */
				$file = array_get($validator->attributes(), 'file');
				$size = getimagesize($file->getRealPath());
				if (!$size) {
					$validator->errors()->add('file', trans('sleeping_owl::validation.not_image'));
				}
			});
		}

		/**
		 * Set.
		 *
		 * @param \Closure $callable
		 * @return $this
		 */
		public function setSaveCallback(\Closure $callable)
		{
			$this->saveCallback = $callable;

			return $this;
		}

		/**
		 * Return save callback.
		 *
		 * @return \Closure
		 */
		public function getSaveCallback()
		{
			return $this->saveCallback;
		}

		/**
		 * Set.
		 *
		 * @param \Closure $callable
		 * @return $this
		 */
		public function setAfterSaveCallback(\Closure $callable)
		{
			$this->afterSaveCallback = $callable;

			return $this;
		}

		/**
		 * Return save callback.
		 *
		 * @return \Closure
		 */
		public function getAfterSaveCallback()
		{
			return $this->afterSaveCallback;
		}

		/**
		 * @param UploadedFile $file
		 * @param string       $path
		 * @param string       $filename
		 * @param array        $settings
		 * @return \Closure|File|array
		 */
		public function saveFile(UploadedFile $file, $path, $filename, array $settings)
		{
			if (is_callable($callback = $this->getSaveCallback())) {
				return $callback($file, $path, $filename, $settings);
			}
			$tmp_name = time();
			$value = $path . '/' . $tmp_name . '.' . $file->extension();
			$value_o = $path . '/' . $tmp_name . '_o.' . $file->extension();
			$value_s = $path . '/' . $tmp_name . '_s.' . $file->extension();
			$value_s_o = $path . '/' . $tmp_name . '_s_o.' . $file->extension();
			if (class_exists('Intervention\Image\Facades\Image') and (bool)getimagesize($file->getRealPath())) {
				$image = \Intervention\Image\Facades\Image::make($file);
				$image->resize(2000, 2000, function ($constraint) {
					$constraint->aspectRatio();
					$constraint->upsize();
				});
				foreach ($settings as $method => $args) {
					call_user_func_array([$image, $method], $args);
				}
				$image->save($value_o, 85);
				$image_s = clone $image;
				$image_s = $this->resizeImage($image_s, $this->width, $this->height);
				$image_s->save($value_s_o, 85);
				$image->save($value,85);
				$image_s->save($value_s, 85);
			}

			return ['path'      => $value,
					'value'     => $value,
					'value_o'   => $value_o,
					'value_s'   => $value_s,
					'value_s_o' => $value_s_o];
		}

		public function resizeImage(\Intervention\Image\Image $image, $x, $y)
		{
			return $image->fit($x, $y, function ($constraint) {
				$constraint->aspectRatio();
			});
		}

		/**
		 * @param UploadedFile $file
		 *
		 * @return string
		 */
		public function defaultUploadPath(UploadedFile $file)
		{
			return config('sleeping_owl.imagesUploadDirectory', 'images/uploads');
		}

		/**
		 * @param \Illuminate\Http\Request $request
		 * @return mixed
		 */
		public function afterSave(\Illuminate\Http\Request $request)
		{
			$value = $request->input($this->getPath());
			$model = $this->getModel();
			if (is_callable($callback = $this->getAfterSaveCallback())) {
				return $callback($value, $model);
			}
		}

		/**
		 * @return array
		 */
		public function toArray()
		{
			$this->setHtmlAttributes([
				'id'   => $this->getName(),
				'name' => $this->getName(),
			]);

			return array_merge(parent::toArray(), [
				'id'         => $this->getName(),
				'value'      => $this->getValueFromModel(),
				'name'       => $this->getName(),
				'width'      => $this->width,
				'height'     => $this->height,
				'data_url'   => $this->data_url,
				'path'       => $this->getPath(),
				'label'      => $this->getLabel(),
				'attributes' => $this->htmlAttributesToString(),
				'helpText'   => $this->getHelpText(),
				'required'   => in_array('required', $this->validationRules),
			]);
		}

		public function render()
		{
			return app('sleeping_owl.template')->view(
				$this->getView(),
				$this->toArray()
			);
		}

	}
