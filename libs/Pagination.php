<?php
/**
 * Author: York
 * Email: yorkshp@gmail.com
 * Date: 26.04.2020
 */

namespace Libs;


use Core\BaseObject;
use Core\Url;

class Pagination extends BaseObject
{
    protected $page;
    protected $pageSize;
    protected $count;

    public $pageName = 'page';
    public $sortName = 'sort';
    public $paginationClass = 'pagination';
    public $paginationItemClass = 'page-item';
    public $paginationLinkClass = 'page-link';
    private $activePageClass = 'active';

    public function __construct($count = null, $page = 1, $pageSize = 10)
    {
        $this->count = $count;
        $this->page = $page;
        $this->pageSize = $pageSize;
    }

    /**
     * @param int $count
     * @return Pagination
     */
    public function setCount(int $count): Pagination
    {
        $this->count = $count;
        return $this;
    }

    /**
     * @param int $page
     * @return Pagination
     */
    public function setPage(int $page): Pagination
    {
        $this->page = $page;
        return $this;
    }

    /**
     * @param int $size
     * @return Pagination
     */
    public function setPageSize(int $size): Pagination
    {
        $this->pageSize = $size;
        return $this;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->pageSize;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return  ($this->page - 1) * $this->pageSize;
    }

    /**
     * @param $field
     * @param null $text
     * @return string
     */
    public function sortLinks($field, $text = null): string
    {
        return  $this->createLink($text, $this->generateSortUrl($field));
    }

    /**
     * @param $text
     * @param $href
     * @return string
     */
    private function createLink($text, $href): string
    {
        return Html::tag('a', $text, ['href' => $href]);
    }

    /**
     * @param $field
     * @return string
     */
    private function generateSortUrl($field): string
    {
        $request = new Url();
        $params = [$this->sortName => $field];
        if ($request->get($this->sortName) === $field) {
            if (!$request->hasParam('desc'))
            $params['desc'] = 1;
        }

        return $request->buildLink($params);
    }

    /**
     * @return string
     */
    public function links()
    {
        $request = new Url();

        $totalPages = ceil($this->count / $this->pageSize);

        $html = Html::openTag('ul', ['class' => $this->paginationClass]);

        $activePage = $request->hasParam($this->pageName) ? (int)$request->get($this->pageName) : 1;


        foreach (range(1, $totalPages) as $item) {
            $options['class'] = $this->paginationItemClass;
            if ($activePage === (int)$item)
                $options['class'] .= ' ' . $this->activePageClass;

            $html .= Html::tag('li', $this->createPaginationLink($item), $options);
        }
        $html .= Html::closeTag('ul');

        return $html;
    }

    /**
     * @param $number
     * @return string
     */
    private function createPaginationLink($number)
    {
        $options = ['href' => $this->createPrginationUrl($number)];
        $options['class'] = $this->paginationLinkClass;

        return Html::tag('a', $number, $options);
    }

    /**
     * @param $number
     * @return string
     */
    private function createPrginationUrl($number)
    {
        $request = new Url();
        $params = $request->getParams();
        $params[$this->pageName] = $number;

        return $request->buildLink($params);
    }
}
