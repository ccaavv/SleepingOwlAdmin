<?php

namespace SleepingOwl\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use SleepingOwl\Admin\Form\FormElements;
use SleepingOwl\Admin\Form\Columns\Column;
use SleepingOwl\Admin\Display\DisplayTable;
use Illuminate\Contracts\Support\Renderable;
use SleepingOwl\Admin\Display\DisplayTabbed;
use Illuminate\Validation\ValidationException;
use SleepingOwl\Admin\Contracts\AdminInterface;
use SleepingOwl\Admin\Model\ModelConfiguration;
use Illuminate\Contracts\Foundation\Application;
use SleepingOwl\Admin\Contracts\Form\FormInterface;
use SleepingOwl\Admin\Contracts\ModelConfigurationInterface;
use SleepingOwl\Admin\Contracts\Display\ColumnEditableInterface;

class AdminController extends Controller
{
    /**
     * @var \DaveJamesMiller\Breadcrumbs\Manager
     */
    protected $breadcrumbs;

    /**
     * @var AdminInterface
     */
    protected $admin;

    /**
     * @var
     */
    private $parentBreadcrumb = 'home';

    /**
     * @var Application
     */
    public $app;

    /**
     * AdminController constructor.
     *
     * @param Request $request
     * @param AdminInterface $admin
     * @param Application $application
     */
    public function __construct(Request $request, AdminInterface $admin, Application $application)
    {
        $this->app = $application;
        $this->admin = $admin;
        $this->breadcrumbs = $admin->template()->breadcrumbs();
        $admin->navigation()->setCurrentUrl($request->getUri());
        if (!$this->breadcrumbs->exists('home')) {
            $this->breadcrumbs->register('home', function ($breadcrumbs) {
                $breadcrumbs->push(trans('sleeping_owl::lang.dashboard'), route('admin.dashboard'));
            });
        }
        $breadcrumbs = [];
        if ($currentPage = $admin->navigation()->getCurrentPage()) {
            foreach ($currentPage->getPathArray() as $page) {
                $breadcrumbs[] = [
                    'id' => $page['id'],
                    'title' => $page['title'],
                    'url' => $page['url'],
                    'parent' => $this->parentBreadcrumb,
                ];
                $this->parentBreadcrumb = $page['id'];
            }
        }
        foreach ($breadcrumbs as $breadcrumb) {
            if (!$this->breadcrumbs->exists($breadcrumb['id'])) {
                $this->breadcrumbs->register($breadcrumb['id'], function ($breadcrumbs) use ($breadcrumb) {
                    $breadcrumbs->parent($breadcrumb['parent']);
                    $breadcrumbs->push($breadcrumb['title'], $breadcrumb['url']);
                });
            }
        }
    }

    /**
     * @return string
     */
    public function getParentBreadcrumb()
    {
        return $this->parentBreadcrumb;
    }

    /**
     * @param string $parentBreadcrumb
     */
    public function setParentBreadcrumb($parentBreadcrumb)
    {
        $this->parentBreadcrumb = $parentBreadcrumb;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getDashboard()
    {
        return $this->renderContent(
            $this->admin->template()->view('dashboard'),
            trans('sleeping_owl::lang.dashboard')
        );
    }

    /**
     * @param ModelConfigurationInterface $model
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function getDisplay(ModelConfigurationInterface $model)
    {
        if (!$model->isDisplayable()) {
            abort(404);
        }
        session([$model->getAlias() . '_back_url' => url()->full()]);

        return $this->render($model, $model->fireDisplay());
    }

    /**
     * @param ModelConfigurationInterface $model
     *
     * @return string
     *
     */
    public function getSectionAlias()
    {
        return $this->getParentBreadcrumb();
    }

    /**
     * @param ModelConfigurationInterface $model
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function getCreate(ModelConfigurationInterface $model)
    {
        if (!$model->isCreatable()) {
            abort(404);
        }
        $create = $model->fireCreate();
        $this->registerBreadcrumb($model->getCreateTitle(), $this->parentBreadcrumb);

        return $this->render($model, $create, $model->getCreateTitle());
    }

    /**
     * @param ModelConfigurationInterface $model
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postStore(ModelConfigurationInterface $model, Request $request)
    {
        if (!$model->isCreatable()) {
            abort(404);
        }
        $createForm = $model->fireCreate();
        $nextAction = $request->input('next_action');
        $backUrl = $this->getBackUrl($model, $request);
        if ($createForm instanceof FormInterface) {
            try {
                $createForm->validateForm($request, $model);
                if ($createForm->saveForm($request, $model) === false) {
                    return redirect()->back()->with([
                        '_redirectBack' => $backUrl,
                        'sleeping_owl_tab_id' => $request->get('sleeping_owl_tab_id') ?: null,
                    ]);
                }
            } catch (ValidationException $exception) {
                return redirect()->back()
                    ->withErrors($exception->validator)
                    ->withInput()
                    ->with([
                        '_redirectBack' => $backUrl,
                        'sleeping_owl_tab_id' => $request->get('sleeping_owl_tab_id') ?: null,
                        'error_message' => trans('sleeping_owl::lang.message.validation_error'),
                    ]);
            }
        }
        if ($nextAction == 'save_and_continue') {
            $newModel = $createForm->getModel();
            $primaryKey = $newModel->getKeyName();
            $redirectUrl = $model->getEditUrl($newModel->{$primaryKey});
            $redirectPolicy = $model->getRedirect();
            /* Make redirect when use in model config && Fix editable redirect */
            if ($redirectPolicy->get('create') == 'display' || !$model->isEditable($newModel)) {
                $redirectUrl = $model->getDisplayUrl();
            }
            $response = redirect()->to(
                $redirectUrl
            )->with([
                '_redirectBack' => $backUrl,
                'sleeping_owl_tab_id' => $request->get('sleeping_owl_tab_id') ?: null,
            ]);
        } elseif ($nextAction == 'save_and_create') {
            $response = redirect()->to($model->getCreateUrl($request->except([
                '_redirectBack',
                '_token',
                'url',
                'next_action',
            ])))->with([
                '_redirectBack' => $backUrl,
                'sleeping_owl_tab_id' => $request->get('sleeping_owl_tab_id') ?: null,
            ]);
        } else {
            $response = redirect()->to($request->input('_redirectBack', $model->getDisplayUrl()));
        }

        return $response->with('success_message', $model->getMessageOnCreate());
    }

    /**
     * @param ModelConfigurationInterface $model
     * @param int $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function getEdit(ModelConfigurationInterface $model, $id)
    {
        $item = $model->getRepository()->find($id);
        if (is_null($item) || !$model->isEditable($item)) {
            abort(401);
        }
        $this->registerBreadcrumb($model->getEditTitle(), $this->parentBreadcrumb);

        return $this->render($model, $model->fireEdit($id), $model->getEditTitle());
    }

    /**
     * @param ModelConfigurationInterface $model
     * @param int $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function getView(ModelConfigurationInterface $model, $id)
    {
        $item = $model->getRepository()->find($id);
        if (is_null($item)) abort(404);
        $this->registerBreadcrumb($model->getViewTitle(), $this->parentBreadcrumb);

        return $this->render($model, $model->fireView($id), $model->getViewTitle());
    }

    /**
     * @param ModelConfigurationInterface $model
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function displayUpdate(ModelConfigurationInterface $model, Request $request)
    {
        $model->onDisplay()->save($model, $request);
        $response = redirect()->to($request->input('_redirectBack', $model->getDisplayUrl()));

        return $response->with('success_message', $model->getMessageOnUpdate());
    }

    /**
     * @param ModelConfigurationInterface $model
     * @param Request $request
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function postUpdate(ModelConfigurationInterface $model, Request $request, $id)
    {
        /** @var FormInterface $editForm */
        $editForm = $model->fireEdit($id);
        $item = $editForm->getModel();
        if (is_null($item) || !$model->isEditable($item)) {
            abort(404);
        }
        $nextAction = $request->input('next_action');
        $backUrl = $this->getBackUrl($model, $request);
        if ($editForm instanceof FormInterface) {
            try {
                $editForm->validateForm($request, $model);
                if ($editForm->saveForm($request, $model) === false) {
                    return redirect()->back()->with([
                        '_redirectBack' => $backUrl,
                        'sleeping_owl_tab_id' => $request->get('sleeping_owl_tab_id') ?: null,
                    ]);
                }
            } catch (ValidationException $exception) {
                return redirect()->back()
                    ->withErrors($exception->validator)
                    ->withInput()
                    ->with([
                        '_redirectBack' => $backUrl,
                        'sleeping_owl_tab_id' => $request->get('sleeping_owl_tab_id') ?: null,
                        'error_message' => trans('sleeping_owl::lang.message.validation_error'),
                    ]);
            }
        }
        $redirectPolicy = $model->getRedirect();
        if ($nextAction == 'save_and_continue') {
            $response = redirect()->back()->with([
                '_redirectBack' => $backUrl,
                'sleeping_owl_tab_id' => $request->get('sleeping_owl_tab_id') ?: null,
            ]);
            if ($redirectPolicy->get('edit') == 'display') {
                $response = redirect()->to(
                    $model->getDisplayUrl()
                )->with([
                    '_redirectBack' => $backUrl,
                    'sleeping_owl_tab_id' => $request->get('sleeping_owl_tab_id') ?: null,
                ]);
            }
        } elseif ($nextAction == 'save_and_create') {
            $response = redirect()->to($model->getCreateUrl($request->except([
                '_redirectBack',
                '_token',
                'url',
                'next_action',
            ])))->with([
                '_redirectBack' => $backUrl,
                'sleeping_owl_tab_id' => $request->get('sleeping_owl_tab_id') ?: null,
            ]);
        } else {
            $response = redirect()->to($request->input('_redirectBack', $model->getDisplayUrl()));
        }

        return $response->with('success_message', $model->getMessageOnUpdate());
    }

    /**
     * @param ModelConfigurationInterface $model
     * @param Request $request
     *
     * @return bool
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function inlineEdit(ModelConfigurationInterface $model, Request $request)
    {
        $field = $request->input('name');
        $id = $request->input('pk');
        $display = $model->fireDisplay();
        $column = null;
        /* @var ColumnEditableInterface|null $column */
        if (is_callable([$display, 'getColumns'])) {
            $column = $display->getColumns()->all()->filter(function ($column) use ($field) {
                return ($column instanceof ColumnEditableInterface) and $field == $column->getName();
            })->first();
        } else {
            if ($display instanceof DisplayTabbed) {
                foreach ($display->getTabs() as $tab) {
                    $content = $tab->getContent();
                    if ($content instanceof DisplayTable) {
                        $column = $content->getColumns()->all()->filter(function ($column) use ($field) {
                            return ($column instanceof ColumnEditableInterface) and $field == $column->getName();
                        })->first();
                    }
                    if ($content instanceof FormElements) {
                        foreach ($content->getElements() as $element) {
                            //Return data-table if inside FormElements
                            if ($element instanceof DisplayTable) {
                                $column = $element->getColumns()->all()->filter(function ($column) use ($field) {
                                    return ($column instanceof ColumnEditableInterface) and $field == $column->getName();
                                })->first();
                            }
                            //Try to find inline Editable in columns
                            if ($element instanceof Column) {
                                foreach ($element->getElements() as $columnElement) {
                                    if ($columnElement instanceof DisplayTable) {
                                        $column = $columnElement->getColumns()->all()->filter(function ($column) use (
                                            $field
                                        ) {
                                            return ($column instanceof ColumnEditableInterface) and $field == $column->getName();
                                        })->first();
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if (is_null($column)) {
            abort(404);
        }
        $repository = $model->getRepository();
        $item = $repository->find($id);
        if (is_null($item) || !$model->isEditable($item)) {
            abort(404);
        }
        $column->setModel($item);
        if ($model->fireEvent('updating', true, $item) === false) {
            return;
        }
        $column->save($request, $model);
        $model->fireEvent('updated', false, $item);
    }

    /**
     * @param ModelConfigurationInterface $model
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function deleteDelete(ModelConfigurationInterface $model, Request $request, $id)
    {
        $item = $model->getRepository()->find($id);
        if (is_null($item) || !$model->isDeletable($item)) {
            abort(404);
        }

        if (method_exists($model, 'beforeDeleteValidation')) {
            $error = $model->beforeDeleteValidation($id);
            if ($error) {
                return redirect()->back()->with(['error_message' => $error]);
            }
        }

        $model->fireDelete($id);
        if ($model->fireEvent('deleting', true, $item) === false) {
            return redirect()->back();
        }
        $model->getRepository()->delete($id);
        $model->fireEvent('deleted', false, $item);

        return redirect($request->input('_redirectBack', back()->getTargetUrl()))
            ->with('success_message', $model->getMessageOnDelete());
    }

    /**
     * @param ModelConfigurationInterface $model
     * @param Request $request
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function deleteDestroy(ModelConfigurationInterface $model, Request $request, $id)
    {
        if (!$model->isRestorableModel()) {
            abort(404);
        }
        $item = $model->getRepository()->findOnlyTrashed($id);
        if (is_null($item) || !$model->isRestorable($item)) {
            abort(404);
        }
        $model->fireDestroy($id);
        if ($model->fireEvent('destroying', true, $item) === false) {
            return redirect()->back();
        }
        $model->getRepository()->forceDelete($id);
        $model->fireEvent('destroyed', false, $item);

        return redirect($request->input('_redirectBack', back()->getTargetUrl()))
            ->with('success_message', $model->getMessageOnDestroy());
    }

    /**
     * @param ModelConfigurationInterface|ModelConfiguration $model
     * @param Request $request
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function postRestore(ModelConfigurationInterface $model, Request $request, $id)
    {
        if (!$model->isRestorableModel()) {
            abort(404);
        }
        $item = $model->getRepository()->findOnlyTrashed($id);
        if (is_null($item) || !$model->isRestorable($item)) {
            abort(404);
        }
        $model->fireRestore($id);
        if ($model->fireEvent('restoring', true, $item) === false) {
            return redirect()->back();
        }
        $model->getRepository()->restore($id);
        $model->fireEvent('restored', false, $item);

        return redirect($request->input('_redirectBack', back()->getTargetUrl()))
            ->with('success_message', $model->getMessageOnRestore());
    }

    /**
     * @param ModelConfigurationInterface $model
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function getDuplicate(ModelConfigurationInterface $model, Request $request, $id)
    {
        $item = $model->getRepository()->find($id);
        if (is_null($item) || !$model->isDuplicatable()) abort(404);
        if (method_exists($model, 'onDuplicate')) {
            $model->fireEvent('duplicating', true, $item);
            $response = $model->onDuplicate($id);
            $model->fireEvent('duplicated', true, $item);

            return $response;
        }

        return redirect()->back();
    }

    /**
     * @param ModelConfigurationInterface $model
     * @param Renderable|string $content
     * @param string|null $title
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function render(ModelConfigurationInterface $model, $content, $title = null)
    {
        if ($content instanceof Renderable) {
            $content = $content->render();
        }
        if (is_null($title)) {
            $title = $model->getTitle();
        }

        return $this->admin->template()->view('_layout.inner')
            ->with('title', $title)
            ->with('content', $content)
            ->with('breadcrumbKey', $this->parentBreadcrumb);
    }

    /**
     * @param Renderable|string $content
     * @param string|null $title
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function renderContent($content, $title = null)
    {
        if ($content instanceof Renderable) {
            $content = $content->render();
        }

        return $this->admin->template()->view('_layout.inner')
            ->with('title', $title)
            ->with('content', $content)
            ->with('breadcrumbKey', $this->parentBreadcrumb);
    }

    /**
     * @param Request $request
     *
     * @return null|string
     */
    protected function getBackUrl(ModelConfigurationInterface $model, Request $request)
    {
//			if (($backUrl = $request->input('_redirectBack')) == \URL::previous()) {
//				$backUrl = null;
//				$request->merge(['_redirectBack' => $backUrl]);
//			}
        $backUrl = session()->has($model->getAlias() . '_back_url') ? session($model->getAlias() . '_back_url') : $model->getDisplayUrl();
        $request->merge(['_redirectBack' => $backUrl]);

        return $backUrl;
    }

    public function getWildcard()
    {
        abort(404);
    }

    /**
     * @param string $title
     * @param string $parent
     */
    protected function registerBreadcrumb($title, $parent)
    {
        $this->breadcrumbs->register('render', function ($breadcrumbs) use ($title, $parent) {
            $breadcrumbs->parent($parent);
            $breadcrumbs->push($title);
        });
        $this->parentBreadcrumb = 'render';
    }
}
