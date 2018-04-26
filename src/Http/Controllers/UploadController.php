<?php

	namespace SleepingOwl\Admin\Http\Controllers;

	use Validator;
	use Illuminate\Http\Request;
	use Illuminate\Http\JsonResponse;
	use Illuminate\Routing\Controller;
	use SleepingOwl\Admin\Form\Element\File;
	use SleepingOwl\Admin\Contracts\ModelConfigurationInterface;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

	class UploadController extends Controller
	{
		/**
		 * @param Request                     $request
		 * @param ModelConfigurationInterface $model
		 * @param string                      $field
		 * @param int|null                    $id
		 *
		 * @return JsonResponse
		 */
		public function fromField(Request $request, ModelConfigurationInterface $model, $field, $id = null)
		{
			if (!is_null($id)) {
				$item = $model->getRepository()->find($id);
				if (is_null($item) || !$model->isEditable($item)) {
					return new JsonResponse([
						'message' => trans('lang.message.access_denied'),
					], 403);
				}
				$form = $model->fireEdit($id);
			} else {
				if (!$model->isCreatable()) {
					return new JsonResponse([
						'message' => trans('lang.message.access_denied'),
					], 403);
				}
				$form = $model->fireCreate();
			}
			/** @var File $element */
			if (is_null($element = $form->getElement($field))) {
				throw new NotFoundHttpException("Field [{$field}] not found");
			}
			$rules = $element->getUploadValidationRules();
			$messages = $element->getUploadValidationMessages();
			$labels = $element->getUploadValidationLabels();
			/** @var \Illuminate\Contracts\Validation\Validator $validator */
			$validator = Validator::make($request->all(), $rules, $messages, $labels);
			$element->customValidation($validator);
			if ($validator->fails()) {
				return new JsonResponse([
					'message' => trans('lang.message.validation_error'),
					'errors'  => $validator->errors()->get('file'),
				], 400);
			}
			$file = $request->file('file');
			$filename = $element->getUploadFileName($file);
			$path = $element->getUploadPath($file);
			$settings = $element->getUploadSettings();
			$result = $element->saveFile($file, $path, $filename, $settings);

			/* When driver not file */

			return new JsonResponse($result);
		}

		public function renewImage(Request $request)
		{
			if ($request->hasFile('croppedImage')) {
				$site_path = url('/');
				$image_path = preg_replace('/\?[\d]+/', '', $request->image_path);
				$image_path = str_replace($site_path . '/', '', $image_path);
				$image = \Intervention\Image\Facades\Image::make($request->croppedImage);
				$image->save(str_replace( '_s.', '_s_o.', $image_path));

				$watermark = config($request->model.'_watermark');
				if($watermark == 'small' || $watermark == 'big') {
					$watermark = \Intervention\Image\Facades\Image::make(resource_path() . '/watermark.png');
					$watermark->resize($image->width(), $image->height() - 10, function ($constraint) {
						$constraint->aspectRatio();
						$constraint->upsize();
					});

					$image->insert($watermark, 'bottom-center', 0, 10);
				}
				$image->save($image_path);

				return new JsonResponse(['image_path' => $image_path,
										 'value'      => $image_path]);
			} else return new \Exception('Bad cropped image');
		}
	}
