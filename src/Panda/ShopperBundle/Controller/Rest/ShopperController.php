<?php
namespace Panda\ShopperBundle\Controller\Rest;

use Doctrine\ORM\Query;
use Panda\ShopperBundle\Entity\Shopper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Shopper Controller
 * @package Panda\ShopperBundle\Controller\Rest
 */
class ShopperController extends Controller
{
    /**
     * @var int
     */
    private $code = 200;
    /**
     * @var string
     */
    private $message = '';
    /**
     * @var array
     */
    private $data = [];
    /**
     * @var array
     */
    private $requestObject = null;
    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }
    /**
     * @param int $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }
    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }
    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }
    /**
     * Index action.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function indexAction(Request $request)
    {
        if ($this->getMethod($request) == 'POST' || $this->getMethod($request) == 'PUT') {
            $this->save($request);
        }

        if ($this->getMethod($request) == 'GET') {
            $this->load($request);
        }

        if ($this->getMethod($request) == 'DELETE') {
            $this->delete($request);
        }

        $data = $this->getData();


        if (count($data) > 0) {
            $response = new JsonResponse($data, $this->getCode());
        } else {
            $response = new JsonResponse([
                'message' => $this->getMessage()
            ], $this->getCode());
        }

        $callback = $this->getRequestParameters($request, 'callback');
        if ($callback) {
            $response->setCallback($callback);
        }

        return $response;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function listAction(Request $request)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();
        $page = $request->get('page');

        $qb = $em->createQueryBuilder();
//        $qb->select('COUNT(s)')
//            ->from('PandaShopperBundle:Shopper', 's');
//        $count = $qb->getQuery()->getSingleScalarResult();


        $qb->select('s')
            ->from('PandaShopperBundle:Shopper', 's');

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

        return new JsonResponse($result);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function cashBackListAction(Request $request)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();
        $id = $this->getRequestParameters($request, 'id');

        $qb = $em->createQueryBuilder();
        $qb->select('c')
            ->from('FenglinCashBackBundle:CashBack', 'c')
            ->join('c.shopper', 's')
            ->where($qb->expr()->eq('s.id', ':id'))
            ->setParameter(':id', $id);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

        return new JsonResponse($result);
    }

    /**
     * @param Request $request
     * @return bool
     */
    private function save(Request $request)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         * @var \Panda\ShopperBundle\Entity\Shopper $item
         */
        $em = $this->getDoctrine()->getManager();
        $method = $request->getMethod();

        if ($method == 'POST') {
            $item = new Shopper();
        }

        if ($method == 'PUT') {
            $id = $request->get('id');
            $item = $em->getRepository('PandaShopperBundle:Shopper')->find($id);

            if (!$item) {
                $this->setCode(500);
                $this->setMessage('Shopper not found');
            }
        }

        $fields = [
            'name' => [
                'required' => [
                    'POST'
                ]
            ],
            'logo' => [],
            'address' => [],
            'tel' => [],
            'totalAmount' => [],
            'rebate' => [],
            'rebateLevelRate' => [],
            'rebateLevel2Rate' => [],
            'rebateLevel3Rate' => []
        ];

        foreach($fields as $fieldName => $rule) {
            $fieldValue = $this->getRequestParameters($request, $fieldName);

            //check rule
            if (count($rule) == 0) {

                if ($fieldValue) {
                    $item->{"set" . ucfirst($fieldName) . "($fieldValue)"};
                }

            } else {

                if (isset($rule['required']) && in_array(strtoupper($method), $rule['required']) && $fieldValue) {
                    $item->{"set" . ucfirst($fieldName) . "($fieldValue)"};
                } else {
                    $this->setCode(500);
                    $this->setMessage($fieldName . ' not found');
                    return false;
                }

            }

        }

        try {
            $em->persist($item);
            $em->flush();
            $this->setMessage('Item save successful');
        } catch(\Exception $e) {
            $this->setCode(500);
            $this->setMessage($e->getMessage());
        }
    }

    /**
     * @param Request $request
     */
    private function load(Request $request)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $qb = $em->createQueryBuilder();

        $qb->select('s')
            ->from('PandaShopperBundle:Shopper', 's')
            ->where($qb->expr()->eq('s.id', ':id'))
            ->setParameter(':id', $id);
        $query = $qb->getQuery();

        try {
            $data = $query->getSingleResult(Query::HYDRATE_ARRAY);
            $this->setData($data);
        } catch (\Exception $e) {
            $this->setCode(500);
            $this->setMessage($e->getMessage());
        }
    }

    /**
     * @param Request $request
     */
    private function delete(Request $request)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $item = $em->getRepository('PandaShopperBundle:Shopper')->find($id);
        if ($item) {
            try {
                $em->remove($item);
                $em->flush();
            } catch(\Exception $e) {
                $this->setCode(500);
                $this->setMessage($e->getMessage());
            }
        } else {
            $this->setCode(500);
            $this->setMessage('Item not found');
        }
    }


    /**
     * Get request parameter.
     *
     * @param Request $request
     * @param $parameter
     * @return mixed|null
     */
    private function getRequestParameters(Request $request, $parameter)
    {
        $value = null;

        if (!$this->requestObject) {
            $this->requestObject = json_decode($request->getContent());
        }

        if ($this->requestObject && property_exists($this->requestObject, $parameter)) {

            $value = $this->requestObject->{$parameter};

        } elseif ($request->query->get($parameter)) {

            $value = $request->query->get($parameter);

        } elseif ($request->request->get($parameter)) {

            $value = $request->request->get($parameter);

        }
        return $value;
    }

    /**
     * This method need for compatibility async cross domain requests.
     *
     * @param Request $request
     * @return mixed|null|string
     */
    private function getMethod(Request $request)
    {
        if ($request->getMethod() == 'GET') {
            if ($method = $this->getRequestParameters($request, 'method')) {
                return $method;
            }
        }
        return $request->getMethod();
    }
}