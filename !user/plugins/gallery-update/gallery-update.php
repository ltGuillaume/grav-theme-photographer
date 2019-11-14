<?php
namespace Grav\Plugin;

use Grav\Common\Page\Page;
use Grav\Common\Plugin;
use RocketTheme\Toolbox\Event\Event;

class GalleryUpdatePlugin extends Plugin
{
	public static function getSubscribedEvents()
	{
		return [
			'onAdminSave' => ['onAdminSave', 0]
		];
	}

	public function onAdminSave(Event $e)
	{
		$page = $e['object'];
		if ($page->template() != 'gallery') return;

		$header = $page->header();
		$filter_misc = $header->filter_misc_name ?? 'misc';	// ?? = PHP7
		$images = $page->media()->images();
		
		$filters = array();
		$gallery = array();

		foreach($images as $img => $html) {
			if (strpos($img, "-")) {
				$filter = strtolower(trim(substr($img, 0, strpos($img, "-"))));
				$desc   =            trim(substr($img, strpos($img, "-") + 1, stripos($img, ".jpg") - strpos($img, "-") - 1));
			} else {
				$filter = $filter_misc;
				$desc = trim(substr($img, 0, stripos($img, ".jpg")));
			}
			if (!in_array(array('name' => $filter), $filters))
				array_push($filters, array('name' => $filter));
			array_push($gallery, array('filter' => $filter, 'image' => $img, 'description' => $desc));
		}

		sort($filters);
		$header->filters = $filters;
		$header->gallery = $gallery;
		$page->header($header);
	}
}
