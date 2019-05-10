<?php namespace ss\components\pagesPublisher\app\controllers\main\proc;

class Render extends \Controller
{
    /**
     * @var \ewma\Process\AppProcess
     */
    private $process;

    private $pivot;

    private $pivotData;

    private $cat;

    public function __create()
    {
        $this->process = process();

        $this->pivot = $this->unpackModel('pivot');
        $this->cat = $this->pivot->cat;

        $this->pivotData = _j($this->pivot->data);

        $this->instance_($this->cat->id);
    }

    public function pivotData($path = false)
    {
        return ap($this->pivotData, $path);
    }

    public function run()
    {
        $this->render();

        $this->d('^app~:pids/render|', false, RR);
    }

    private function render()
    {
        $process = process();

        //

        $pivot = $this->unpackModel('pivot');
        $cat = $pivot->cat;

        $stockThreshold = $this->pivotData('threshold');
        $percentThreshold = $this->pivotData('min_percent');
        $cutoffZeroprice = $this->pivotData('cutoff_zeroprice');

        //

        $pages = $cat->containedPages()->with('containers')->orderBy('position')->get();

        $count = count($pages);
        $n = 0;

        $pagesPublished = [];

        foreach ($pages as $page) {
            if (true === $process->handleIteration()) {
                break;
            }

            $n++;

            $inStockCount = 0;
            $productsCount = 0;
            $zeropriceCount = 0;

            $containers = $page->containers()->orderBy('position')->get();

            foreach ($containers as $container) {
                $products = $container->products()->with('multisourceSummary')->orderBy('position')->get();

                $productsCount += count($products);

                foreach ($products as $product) {
                    $productSummary = ss()->multisource->getSummary($product);

                    $stock = 0;
                    $zeroprice = true;

                    foreach ($productSummary as $groupProductSummary) {
                        $stock += $groupProductSummary->stock;

                        if ($groupProductSummary->max_price > 0) {
                            $zeroprice = false;
                        }
                    }

                    if ($zeroprice && $cutoffZeroprice) {
                        $zeropriceCount++;
                    }

                    if ($stockThreshold > 0) {
                        $inStock = $stock >= $stockThreshold;
                    } else {
                        $inStock = $stock > $stockThreshold;
                    }

                    if ($inStock) {
                        $inStockCount++;
                    }
                }
            }

            $notZeropriceCount = $productsCount - $zeropriceCount;

            $inStockThreshold = $notZeropriceCount * $percentThreshold / 100;

            if ($inStockThreshold > 0) {
                $published = $inStockCount >= $inStockThreshold;
            } else {
                $published = $inStockCount > $inStockThreshold;
            }

            $pagesPublished[$page->id] = $published;

            $this->log('render ' . $n . '/' . $count .
                       ' products count: ' . $productsCount .
                       ', in stock: ' . $inStockCount .
                       ', zeroprice: ' . $zeropriceCount .
                       '; ' . ($published ? 'PUBLISHED' : 'NOT PUBLISHED') . ' ' . $page->name);

            $process->progress($n, $count);
        }

        awrite($this->_protected('data', '~:cat_' . $this->cat->id . '/pages_published.php'), $pagesPublished);
    }
}
