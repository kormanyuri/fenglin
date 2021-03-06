<?php

namespace Panda\ShopperBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function loginAction(Request $request)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         * @var \Panda\ShopperBundle\Entity\Shopper $shopper
         */
        $encoder = $this->container->get('security.password_encoder');

        $em = $this->getDoctrine()->getManager();
        $shopperRepo = $em->getRepository('PandaShopperBundle:Shopper');
        $tel = $request->request->get('tel');
        $password = $request->request->get('password');

        $shopper = $shopperRepo->findOneBy(['tel' => $tel]);

        if ($shopper) {
            $password = $encoder->encodePassword($shopper, $password);

            if ($password == $shopper->getPassword()) {
                return $this->redirect($request->getSchemeAndHttpHost() . '/shopper?apikey=' . $shopper->getApiKey() . '#shopper/home');
            } else {
                return new Response('Password not correct', 403);
            }


        } else {
            return new Response('Shopper with tel ' . $tel . 'not found', 403);
        }

        return new Response('Phone number or password is not correct', 403);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function readQRAction(Request $request)
    {
        $memberId = $request->get('memberId');

        return $this->render('shopper/read_qr.html.twig', [
            'memberId' => $memberId
        ]);
    }
}
