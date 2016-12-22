<?php
namespace Finance\Controllers;

use Finance\Core\Controller;
use Finance\Core\View;

/**
 * Class MainController
 *
 * @package Finance\Controllers
 */
class GeneralController extends Controller
{
    /**
     * @param array $server
     * @param array $request
     * @param array $session
     */
    public function action(array $server, array $request, array $session)
    {
        //$data = ((new IndexModel())->buildData($session, $server))->getData();
        (new View())->generate("general.html.twig", []);
    }
}