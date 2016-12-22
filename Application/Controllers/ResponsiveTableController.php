<?php
namespace Finance\Controllers;

use Finance\Core\Controller;
use Finance\Core\View;

/**
 * Class ResponsiveTableController
 *
 * @package Finance\Controllers
 */
class ResponsiveTableController extends Controller
{
    /**
     * @param array $server
     * @param array $request
     * @param array $session
     */
    public function action(array $server, array $request, array $session)
    {
        //$data = ((new IndexModel())->buildData($session, $server))->getData();
        (new View())->generate("responsive_table.html.twig", []);
    }
}