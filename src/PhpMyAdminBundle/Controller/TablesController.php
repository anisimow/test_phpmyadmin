<?php

namespace PhpMyAdminBundle\Controller;

use PhpMyAdminBundle\Form\CreateTableType;
use PhpMyAdminBundle\Form\InsertRawType;
use PhpMyAdminBundle\Repository\TablesRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TablesController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Route("/", name="tables_index")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render(
            'default/homepage.html.twig',
            array(
                'tables' => $this->_getTables(),
                'create_table_form' => $this->createForm(CreateTableType::class)->createView()
            )
        );
    }

    /**
     * @Route("/delete_table/{table_name}", requirements={"table_name" = "\w+"},  name="delete_table")
     * @param $table_name
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteTableAction($table_name)
    {
        $this->_getTablesRepository()->dropTable($table_name);

        $this->addFlash('success', 'table.dropped_successfully');

        return $this->redirectToRoute('tables_index');
    }

    /**
     * @Route("/create_table/", name="create_table")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @internal param $table_name
     */
    public function createTableAction(Request $request)
    {
        $form = $this->createForm(CreateTableType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->_getTablesRepository()->createTable($form->getData()['name'])) {
                $this->addFlash('success', 'table.created_successfully');
            } else {
                $this->addFlash('warning', 'table.table_exist');
            }
        }

        return $this->redirectToRoute('tables_index');
    }

    /**
     * @Route("/show_table/{table_name}", requirements={"table_name" = "\w+"}, name="show_table")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @internal param $table_name
     */
    public function showTableAction(Request $request)
    {
        $table_name = $request->get('table_name');

        return $this->render(
            'default/table.html.twig',
            array(
                'table_name' => $table_name,
                'tables' => $this->_getTables(),
                'insert_raw_form' => $this->_getInsertForm($table_name)->createView(),
                'table_paginator' => $this->_getTablesRepository()->getTablePaginator(
                    $table_name,
                    $request->get('page', 1),
                    $request->get('limit', 15)
                )
            )
        );
    }

    /**
     * @Route("/delete_raw/{table_name}/{id}", requirements={"table_name" = "\w+", }, name="delete_raw")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @internal param $table_name
     */
    public function deleteRowAction(Request $request)
    {
        if (
            $this->_getTablesRepository()->deleteTableRow(
                $request->get('table_name'),
                $request->get('id')
            )
        ) {
            $this->addFlash('success', 'table.deleted_successfully');
        } else {
            $this->addFlash('danger', 'table.delete_error');
        }

        return $this->redirectToRoute('show_table', array('table_name' => $request->get('table_name')));
    }

    /**
     * @Route("/insert_raw/{table_name}/", requirements={"table_name" = "\w+", }, name="insert_raw")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @internal param $table_name
     */
    public function insertRowAction(Request $request)
    {
        $form = $this->_getInsertForm($request->get('table_name'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (
            $this->_getTablesRepository()->insertTableRow(
                $request->get('table_name'),
                $form->getData())
            ) {
                $this->addFlash('success', 'table.inserted_successfully');
            } else {
                $this->addFlash('danger', 'table.insert_error');
            }
        }

        return $this->redirectToRoute('show_table', array('table_name' => $request->get('table_name')));
    }


    protected function _getTables()
    {
        return $this->_getTablesRepository()->getTables();
    }

    /**
     * @return TablesRepository
     */
    protected function _getTablesRepository()
    {
        return $this->get('phpmyadmin.repository.tables');
    }

    protected function _getInsertForm($table_name)
    {
        return $this->createForm(
            InsertRawType::class,
            null,
            array('columns' => $this->_getTablesRepository()->getTableColumns($table_name))
        );
    }
}
