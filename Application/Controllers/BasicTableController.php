<?php
namespace Finance\Controllers;

use Finance\Core\Controller;
use Finance\Core\View;

/**
 * Class BasicTableController
 *
 * @package Finance\Controllers
 */
class BasicTableController extends Controller
{
    /**
     * @param array $server
     * @param array $request
     * @param array $session
     */
    public function action(array $server, array $request, array $session)
    {
        //$data = ((new IndexModel())->buildData($session, $server))->getData();
        (new View())->generate("basic_table.html.twig", []);
    }
}