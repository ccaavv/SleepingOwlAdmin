<?php

	namespace SleepingOwl\Admin\Form\Element;

	use Illuminate\Http\UploadedFile;
	use Illuminate\Validation\Validator;

	class ImageCropper extends File
	{
		/**
		 * @var string
		 */
		protected static $route = 'image';

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
		protected $view = 'admin.elements.image_cropper';

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

		public function render()
		{
			return view(
				$this->getView(),
				$this->toArray()
			);

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

			$value = $path . '/' . $filename;

			if (class_exists('Intervention\Image\Facades\Image') and (bool)getimagesize($file->getRealPath())) {
				$image = \Intervention\Image\Facades\Image::make($file);
				foreach ($settings as $method => $args) {
					call_user_func_array([$image, $method], $args);
				}
				$image->save($value);

			}

			return ['path' => asset($value), 'value' => $value];
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

		public function cropperTemplate($data)
		{
			return ['cropper' => view('admin.elements.cropper', $data)->render()];
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
	}
