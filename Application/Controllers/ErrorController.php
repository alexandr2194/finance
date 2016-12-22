<?php
namespace Finance\Controllers;

use Finance\Core\Controller;

/**
 * Class ErrorController
 *
 * @package Finance\Controllers
 */
class ErrorController extends Controller
{
    /**
     * @param array $server
     * @param array $request
     * @param array $session
     */
    public function action(array $server, array $request, array $session)
    {
        //$data = ((new IndexModel())->buildData($session, $server))->getData();
        //(new IndexView())->generate([]);
        var_dump($request);
    }
}