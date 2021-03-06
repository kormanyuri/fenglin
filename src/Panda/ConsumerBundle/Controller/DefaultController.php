<?php

namespace Panda\ConsumerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('PandaConsumerBundle:Default:index.html.twig');
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function readQRAction(Request $request)
    {
        $shopperId = $request->get('shopperId');
        $shopper = $this->getDoctrine()->getRepository('PandaShopperBundle:Shopper')->find($shopperId);

        return $this->render('consumer/read_qr.html.twig', [
            'shopperId' => $shopperId,
            'shopper' => $shopper,
            'biz' => urlencode($this->container->getParameter('wechat_biz'))
        ]);
    }

    public function redirectAction(Request $request)
    {
        $biz = urlencode($this->container->getParameter('wechat_biz'));
        $url = 'http://mp.weixin.qq.com/mp/profile_ext?action=home&__biz=' . $biz . '#wechat_redirect';
        return $this->redirect($url);

//        return $this->render('consumer/middle_qr.html.twig', [
//            'url' => $url
//        ]);
    }

    public function getWeChatHeadersAction(Request $request)
    {
        echo '<pre>';
        print_r($request->headers->all());
        exit;
    }
}
