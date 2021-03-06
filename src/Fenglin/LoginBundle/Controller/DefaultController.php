<?php

namespace Fenglin\LoginBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function indexAction(Request $request)
    {
        $tel = $request->request->get('tel');
        $password = $request->request->get('password');

        if ($tel && $password) {
            return $this->auth($request);
        }

        return $this->render('fenglin/login/login.html.twig');
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function exampleAction(Request $request)
    {
        $memberId = $request->query->get('member_id');
        return new Response("Member ID: " . $memberId);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    private function auth(Request $request)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         * @var \Panda\ShopperBundle\Entity\Shopper $shopper
         * @var \Panda\StaffBundle\Entity\Staff $staff
         */
        $encoder = $this->container->get('security.password_encoder');

        $em          = $this->getDoctrine()->getManager();
        $shopperRepo = $em->getRepository('PandaShopperBundle:Shopper');
        $adminRepo   = $em->getRepository('FenglinAdminBundle:Admin');
        $staffRepo   = $em->getRepository('PandaStaffBundle:Staff');

        $tel         = $request->request->get('tel');
        $password    = $request->request->get('password');

        $shopper = $shopperRepo->findOneBy(['tel' => $tel]);
        $admin   = $adminRepo->findOneBy(['tel' => $tel]);
        $staff   = $staffRepo->findOneBy(['tel' => $tel]);

        if ($shopper) {
            $password = $encoder->encodePassword($shopper, $password);

            if ($shopper->getStatus() === 0 ){
                return new Response('Shopper not active', 403);
            }

            if ($password == $shopper->getPassword()) {
                return $this->redirectToRoute('fenglin_fenglin_shopper', ['apikey'=> $shopper->getApiKey(), '_fragment' => 'shopper/home']);
            } else {
                return new Response('Password not correct', 403);
            }


        } elseif ($admin) {
            $password = $encoder->encodePassword($admin, $password);

            if ($password == $admin->getPassword()) {
                return $this->redirectToRoute('fenglin_admin_homepage', ['apikey'=> $admin->getApiKey(), '_fragment' => 'admin/shopper/inactive-reactive/account']);
            } else {
                return new Response('Password not correct', 403);
            }


        } elseif($staff) {
            $password = $encoder->encodePassword($staff, $password);

            if ($staff->getStatus() === 0 ){
                return new Response('Staff not active', 403);
            }

            if ($password == $staff->getPassword()) {
                return $this->redirectToRoute('panda_staff_homepage', ['apikey'=> $staff->getApiKey(), '_fragment' => 'shopper/home']);
            } else {
                return new Response('Password not correct', 403);
            }
        } else {
            return new Response('User with tel ' . $tel . 'not found', 403);
        }


        return new Response('Phone number or password is not correct', 403);

    }
}
