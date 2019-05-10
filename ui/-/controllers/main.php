<?php namespace ss\components\pagesPublisher\ui\controllers;

class Main extends \Controller
{
    public function apply()
    {
        $pivot = $this->data('pivot');
        $cat = $pivot->cat;

        $publishedList = aread($this->_protected('data', '^app~:cat_' . $cat->id . '/pages_published.php'));

        $this->c('\ss\components\cats svc:setPublishedList', [
            'cat'            => $cat,
            'published_list' => $publishedList
        ]);
    }
}
