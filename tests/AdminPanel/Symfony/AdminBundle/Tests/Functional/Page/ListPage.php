<?php

declare (strict_types = 1);

namespace AdminPanel\Symfony\AdminBundle\Tests\Functional\Page;

use Symfony\Bundle\FrameworkBundle\Client;

class ListPage extends BasePage
{
    /**
     * @var string
     */
    private $pageName;

    /**
     * @param Client $client
     * @param string $pageName
     * @param Page $previousPage
     */
    public function __construct(Client $client, string $pageName = 'admin_users', Page $previousPage = null)
    {
        parent::__construct($client, $previousPage);

        $this->pageName = $pageName;
    }

    /**
     * @return string
     */
    public function getUrl() : string
    {
        return '/list/' . $this->pageName;
    }

    /**
     * @return CreateFormPage
     */
    public function openAddNewElementForm() : CreateFormPage
    {
        return (new CreateFormPage($this->client, $this->pageName, $this))->open();
    }

    /**
     * @param int $howManyElements
     * @return ListPage
     */
    public function shouldHaveElementsOnTheList(int $howManyElements) : ListPage
    {
        $elements = $this->getCrawler()->filter(sprintf('#%s tbody tr', $this->pageName))->count();

        \PHPUnit_Framework_Assert::assertEquals($howManyElements, $elements);

        return $this;
    }

    /**
     * @param string $buttonLabel
     * @param int $whichElement
     * @return ListPage
     */
    public function shouldHaveButtonOnElementNumber(string $buttonLabel, int $whichElement) : ListPage
    {
        $elements = $this
            ->getCrawler()
            ->filter(sprintf('#%s tbody tr', $this->pageName))
            ->each(function ($node, $i) use ($buttonLabel, $whichElement) {
                if (($whichElement - 1) == $i) {
                    \PHPUnit_Framework_Assert::assertEquals(1, $node->filter(sprintf('[title="%s"]', $buttonLabel))->count());

                    return $node->text();
                }
            }
        );

        $elements = array_unique($elements);
        if (!$elements) {
            \PHPUnit_Framework_Assert::fail(sprintf('Element %d do not find', $whichElement));
        }

        return $this;
    }

    /**
     * @return ListPage
     */
    public function shouldHaveAddNewElementButton() : ListPage
    {
        $linkNodes = $this->getCrawler()->selectLink('Add new');
        \PHPUnit_Framework_Assert::assertGreaterThan(0, $linkNodes->count());

        return $this;
    }

    /**
     * @return ListPage
     */
    public function shouldHaveDeleteBatchAction() : ListPage
    {
        $options = $this->getCrawler()->filter('#batch_action_action');
        $options = $options->each(function ($element) {
            if ($element->text() === 'Delete') {
                return $element->text();
            }
        });
        $options = array_unique($options);
        \PHPUnit_Framework_Assert::assertGreaterThan(0, count($options));

        return $this;
    }

    /**
     * @return ListPage
     */
    public function shouldNotHaveDeleteBatchAction() : ListPage
    {
        $options = $this->getCrawler()->filter('#batch_action_action');
        $options = $options->each(function ($element) {
            if ($element->text() === 'Delete') {
                return $element->text();
            }
        });
        $options = array_unique($options);
        \PHPUnit_Framework_Assert::assertEquals(0, count($options));

        return $this;
    }

    /**
     * @return ListPage
     */
    public function shouldNotHaveAddNewElementButton() : ListPage
    {
        $linkNodes = $this->getCrawler()->selectLink('Add new');
        \PHPUnit_Framework_Assert::assertEquals(0, $linkNodes->count());

        return $this;
    }

    /**
     * @param int $whichElement
     * @return ListPage|EditPage
     */
    public function clickEditButtonForElementWithNumber(int $whichElement) : Page
    {
        if ($this->clickButtonForElementWithNumber('Edit', $whichElement)) {
            return new EditPage($this->client, $this->pageName, $this);
        }

        return $this;
    }

    /**
     * @param int $whichElement
     * @return ListPage|CustomActionPage
     */
    public function clickCustomButtonForElementWithNumber(int $whichElement) : Page
    {
        if ($this->clickButtonForElementWithNumber('Custom', $whichElement)) {
            return new CustomActionPage($this->client, $this);
        }

        return $this;
    }

    /**
     * @param string $expectedTitle
     * @return ListPage
     */
    public function shouldSeePageHeader(string $expectedTitle) : ListPage
    {
        $title = trim($this->getCrawler()->filter('#page-header')->text());

        \PHPUnit_Framework_Assert::assertEquals($expectedTitle, $title);
        \PHPUnit_Framework_Assert::assertEquals(200, $this->client->getResponse()->getStatusCode());

        return $this;
    }

    /**
     * @param string $buttonLabel
     * @param int $whichElement
     * @return bool
     */
    private function clickButtonForElementWithNumber(string $buttonLabel, int $whichElement) : bool
    {
        $elements = $this
            ->getCrawler()
            ->filter(sprintf('#%s tbody tr', $this->pageName))
            ->each(function ($node, $i) use ($buttonLabel, $whichElement) {
                if (($whichElement - 1) == $i) {
                    $link = $node->filter(sprintf('[title="%s"]', $buttonLabel))->link();
                    $this->client->click($link);

                    return $node->text();
                }
            });

        $elements = array_unique($elements);
        if (!$elements) {
            \PHPUnit_Framework_Assert::fail(sprintf('Element %d do not find', $whichElement));

            return false;
        }

        return true;
    }
}