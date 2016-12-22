<?php
namespace Finance\Core;

/**
 * Class Controller
 * @package Finance\Controllers
 */
abstract class Controller
{
    /**
     * @param array $server
     * @param array $request
     * @param array $session
     * @return
     */
    abstract public function action(array $server, array $request, array $session);
}