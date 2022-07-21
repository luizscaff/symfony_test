<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\ByteString;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\RateLimiter\RateLimiterFactory;

use App\Entity\HashResult;

use function Symfony\Component\String\u;

/**
 * @Route("/api/hash_results", name="hash_results_")
 */

class HashResultController extends AbstractController
{
  //---------------------------------------------------------------------------------------

  /**
   * @Route("/generate/{inputString}", name="generate", methods={"POST"})
   */
  public function Generate($inputString, RateLimiterFactory $anonymousApiLimiter)
  {
    try
    {
      $limiter = $anonymousApiLimiter->create($this->container->get('request_stack')->getCurrentRequest()->getClientIp());

      if($limiter->consume(1)->isAccepted() === false)
        throw new TooManyRequestsHttpException(null, 'Too Many Attempts', null, 429);

      $batch         = new \DateTime('now', new \DateTimeZone('America/Sao_Paulo'));
      $dataArray     = $this->FindHash($inputString);

      $entityManager = $this->getDoctrine()->getManager();
      $hashResult    = $this->Save($entityManager, $batch, $inputString, $dataArray["key"], $dataArray["hash"], $dataArray["numberOfTries"]);

      return $this->json([
          'hash'     => $dataArray["hash"],
          'key'      => $dataArray["key"],
          'attempts' => $dataArray["numberOfTries"]
      ]);
    }
    catch(TooManyRequestsHttpException $e)
    {
      return $this->json([
        'status_code' => $e->getStatusCode(),
        'message'     => $e->getMessage()
      ]);
    }
    catch(\Exception $e)
    {
      return $this->json([
        'message' => $e->getMessage()
      ]);
    }
  }

  //---------------------------------------------------------------------------------------

  public static function FindHash($inputString)
  {
    for($numberOfTries = 0; ; $numberOfTries++)
    {
      $key  = ByteString::fromRandom(8)->toString(); 
      $hash = md5($inputString . $key);

      $firstFourDigits = u($hash)->slice(0, 4);

      if(strcmp($firstFourDigits, "0000") == 0)
        break;
    }

    return ["key"           => $key,
            "hash"          => $hash,
            "numberOfTries" => $numberOfTries];
  }

  //---------------------------------------------------------------------------------------

  public static function Save($entityManager, $batch, $inputString, $key, $hash, $numberOfTries, $requisitionIndex = null)
  {
    $hashResult = new HashResult();
    $hashResult->setBatch($batch);
    $hashResult->setInputString($inputString);
    $hashResult->setSolutionKey($key);
    $hashResult->setHash($hash);
    $hashResult->setNumberOfTries($numberOfTries);
    $hashResult->setRequisitionIndex($requisitionIndex);

    $entityManager->persist($hashResult);
    $entityManager->flush();

    return $hashResult;
  }

  //---------------------------------------------------------------------------------------

  /**
   * @Route("/get_hash_results/{maxNumberOfTries?}", name="get_hash_results", methods={"GET"})
   */
  public function GetHashResults($maxNumberOfTries = null)
  {
    try
    {
      $entityManager = $this->getDoctrine()->getManager();
      $query = $entityManager->createQueryBuilder()
                             ->select('hr.batch', 'hr.requisition_index',
                                      'hr.input_string', 'hr.solution_key')
                             ->from('App\Entity\HashResult', 'hr');
      if(isset($maxNumberOfTries) && is_numeric($maxNumberOfTries) && $maxNumberOfTries > 0)
        $query->where('hr.number_of_tries <= ' . $maxNumberOfTries);

      $result = $query->getQuery()->getResult();

      return $this->json($result);
    }
    catch(\Exception $e)
    {
      return $this->json([
        'message' => $e->getMessage()
      ]);
    }
  }
  
  //---------------------------------------------------------------------------------------
}
