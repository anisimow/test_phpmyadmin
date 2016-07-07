<?php

namespace PhpMyAdminBundle\Controller;

use PhpMyAdminBundle\Form\CreateTableType;
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
     * @Route("/delete/{table_name}", requirements={"table_name" = "\w+"},  name="delete_table")
     * @param $table_name
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($table_name)
    {
        $this->get('phpmyadmin.repository.tables')->dropTable($table_name);

        $this->addFlash('success', 'table.dropped_successfully');

        return $this->redirectToRoute('tables_index');
    }

    /**
     * @Route("/create_table/", name="create_table")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @internal param $table_name
     */
    public function createAction(Request $request)
    {
        $form = $this->createForm(CreateTableType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('phpmyadmin.repository.tables')->createTable( $form->getData()['name']);

            $this->addFlash('success', 'table.created_successfully');
        }

        return $this->redirectToRoute('tables_index');
    }

    protected function _getTables()
    {
        return $this->get('phpmyadmin.repository.tables')->getTables();
    }


}
