<?php

	namespace SleepingOwl\Admin;

	use Illuminate\Support\Collection;
	use SleepingOwl\Admin\Contracts\Navigation\PageInterface;
	use SleepingOwl\Admin\Contracts\Navigation\NavigationInterface;

	class Navigation extends \KodiComponents\Navigation\Navigation implements NavigationInterface
	{
		protected $currentPage;
		protected $currentUrl;

		/**
		 * Overload current page.
		 *
		 * @return \KodiComponents\Navigation\Contracts\PageInterface|null
		 */
		public function getCurrentPage()
		{
			$this->setAliasesId($this->getPages());
			$this->findActivePage();

			return $this->currentPage;
		}

		/**
		 * Set Alias Id to Page.
		 *
		 * @param Collection $pages
		 */
		public function setAliasesId(Collection $pages)
		{
			$pages->each(function (PageInterface $page) {
				$page->setAliasId();
				if ($page->getPages()->count()) {
					$this->setAliasesId($page->getPages());
				}
			});
		}

		/**
		 * @param string $url
		 * @param array  $foundPages
		 */
		protected function findActive($url, array &$foundPages)
		{
			$this->findPageByAliasId($this->getPages(), $url);
		}

		/**
		 * @param Collection $pages
		 * @param            $url
		 */
		protected function findPageByAliasId(Collection $pages, $url)
		{
			$pages->each(function (PageInterface $page) use ($url) {
				$urlPath = parse_url($url, PHP_URL_PATH);
				$url_exploded = explode('/', $urlPath);
				if(isset($url_exploded[1], $url_exploded[2]))
					$urlPath = '/' . $url_exploded[1] . '/' . $url_exploded[2];
				if ($urlPath) {
					if (md5($urlPath) == $page->getAliasId()) {
						$this->currentPage = $page;

						return;
					}
				}
				$this->findPageByAliasId($page->getPages(), $url);
			});
		}

		/**
		 * @return bool
		 */
		protected function findActivePage()
		{
			if (!is_null($this->currentPage)) {
				return true;
			}
			$foundPages = [];
			$url = $this->getCurrentUrl();
			$this->findActive($url, $foundPages);
			if (!is_null($this->currentPage)) {
				$this->currentPage->setActive();
			}
			if (config('navigation.aliases')) {
				$this->findActiveByAliases(
					ltrim(parse_url($url, PHP_URL_PATH), '/')
				);
			}

			return false;
		}
	}
