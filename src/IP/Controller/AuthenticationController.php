<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bud
 * Date: 7/22/13
 * Time: 8:25 PM
 * To change this template use File | Settings | File Templates.
 */

namespace IP\Controller;

use Silex\Application;
use Hautelook\Phpass\PasswordHash;
use Symfony\Component\HttpFoundation\Request;
use IP\Mapper\UserMapper;

class AuthenticationController
{
    public function loginAction(Request $request, Application $app)
    {
        $error = '';
        if($_POST) {
            $hasher = new PasswordHash(8, true);

            $username = $request->get('_username');
            $password = $request->get('_password');

            $userMapper = new UserMapper($app['db']);
            $user = $userMapper->fetchViaAuth($username, $password, $hasher);

            if(null !== $user) {
                $app['session']->set('user', $user);
                return $app->redirect('/');
            } else {
                $error = 'The username or password was invalid';
            }
        }

        return $app['twig']->render('Authentication/index.html.twig', compact('error'));
    }

    public function logoutAction(Application $app)
    {
        $app['session']->clear();
        return $app->redirect('/');
    }
}
