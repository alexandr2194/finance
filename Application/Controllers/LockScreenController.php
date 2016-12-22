<?php
namespace Finance\Controllers;

use Finance\Core\Controller;
use Finance\Core\View;

/**
 * Class LockScreenController
 *
 * @package Finance\Controllers
 */
class LockScreenController extends Controller
{
    /**
     * @param array $server
     * @param array $request
     * @param array $session
     */
    public function action(array $server, array $request, array $session)
    {
        //$data = ((new IndexModel())->buildData($session, $server))->getData();
        (new View())->generate("lock_screen.html.twig", []);
    }
}