<?php

declare(strict_types=1);

namespace AdminPanel\Symfony\AdminBundle\Admin\CRUD;

use AdminPanel\Symfony\AdminBundle\Admin\AbstractElement;
use AdminPanel\Symfony\AdminBundle\Exception\RuntimeException;
use AdminPanel\Component\DataGrid\DataGridFactoryInterface;
use AdminPanel\Component\DataGrid\DataGridInterface;
use AdminPanel\Component\DataSource\DataSourceFactoryInterface;
use AdminPanel\Component\DataSource\DataSourceInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class GenericListElement extends AbstractElement implements ListElement
{
    /**
     * @var \AdminPanel\Component\DataSource\DataSourceFactoryInterface
     */
    protected $datasourceFactory;

    /**
     * @var \AdminPanel\Component\DataGrid\DataGridFactoryInterface
     */
    protected $datagridFactory;

    /**
     * {@inheritdoc}
     */
    public function getRoute()
    {
        return 'admin_panel_list';
    }

    /**
     * {@inheritdoc}
     */
    public function setDataGridFactory(DataGridFactoryInterface $factory)
    {
        $this->datagridFactory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function setDataSourceFactory(DataSourceFactoryInterface $factory)
    {
        $this->datasourceFactory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function createDataGrid()
    {
        $datagrid = $this->initDataGrid($this->datagridFactory);

        if (!is_object($datagrid) || !$datagrid instanceof DataGridInterface) {
            throw new RuntimeException('initDataGrid should return instanceof AdminPanel\\Component\\DataGrid\\DataGridInterface');
        }

        return $datagrid;
    }

    /**
     * {@inheritdoc}
     */
    public function createDataSource()
    {
        $datasource = $this->initDataSource($this->datasourceFactory);

        if (!is_object($datasource) || !$datasource instanceof DataSourceInterface) {
            throw new RuntimeException('initDataSource should return instanceof AdminPanel\\Component\\DataSource\\DataSourceInterface');
        }

        return $datasource;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'allow_delete' => false,
            'template_list' => null,
        ]);

        $resolver->setAllowedValues('allow_delete', false);
        $resolver->setAllowedTypes('template_list', ['null', 'string']);
    }

    /**
     * {@inheritdoc}
     */
    public function apply($object)
    {
        $this->delete($object);
    }

    /**
     * Initialize DataGrid.
     *
     * @param \AdminPanel\Component\DataGrid\DataGridFactoryInterface $factory
     * @return \AdminPanel\Component\DataGrid\DataGridInterface
     */
    abstract protected function initDataGrid(DataGridFactoryInterface $factory);

    /**
     * Initialize DataSource.
     *
     * @param \AdminPanel\Component\DataSource\DataSourceFactoryInterface $factory
     * @return \AdminPanel\Component\DataSource\DataSourceInterface
     */
    abstract protected function initDataSource(DataSourceFactoryInterface $factory);
}
